"""
recommender.py
==============
The main AI recommendation engine called by Laravel's AiRecommendationService.

Usage (called by PHP via shell_exec):
    python3 ai_module/recommender.py <base64-encoded-json-payload>

Input JSON (base64-decoded):
    {
        "request_id":   42,
        "title":        "Kitchen sink leaking",
        "description":  "Water dripping from pipe joint under kitchen sink ...",
        "category":     "plumbing",          # optional hint from customer
        "service_id":   1,                   # optional — null if customer didn't pick
        "city":         "Ranchi",
        "pincode":      "834001",
        "latitude":     23.36,               # optional
        "longitude":    85.33,               # optional
        "budget_max":   2000,                # optional
        "preferred_date": "2026-03-25"       # optional
    }

Output JSON (printed to stdout — Laravel reads this):
    {
        "request_id":           42,
        "recommended_service":  "Pipe Repair & Leak Fixing",
        "recommended_service_id": 1,
        "predicted_category":   "plumbing",
        "confidence":           0.91,
        "top_providers": [
            {
                "provider_id":      1,
                "name":             "Ramesh Plumbing Works",
                "score":            0.94,
                "avg_rating":       4.7,
                "total_jobs":       142,
                "years_experience": 8,
                "hourly_rate":      300
            },
            ...
        ],
        "model_version": "v1.0",
        "scoring_breakdown": { ... }   # for transparency / project report
    }
"""

import os
import sys
import json
import math
import pickle
import base64
import logging
import re
from typing import Any

import numpy as np

# ── Paths ─────────────────────────────────────────────────────────────────────

BASE_DIR       = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH     = os.path.join(BASE_DIR, "model.pkl")
PROVIDERS_PATH = os.path.join(BASE_DIR, "data", "providers.json")

# ── Service name lookup (mirrors the services table) ─────────────────────────

SERVICE_NAMES = {
    "1": "Pipe Repair & Leak Fixing",
    "2": "Tap & Faucet Installation",
    "3": "Wiring & Switchboard Repair",
    "4": "Fan & Light Installation",
    "5": "Furniture & Carpentry Repair",
    "6": "Deep Cleaning Service",
    "7": "Painting & Waterproofing",
}

# ── Logging (to stderr so it doesn't pollute stdout which Laravel reads) ──────

logging.basicConfig(
    stream=sys.stderr,
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%H:%M:%S",
)
log = logging.getLogger(__name__)


# ── Text preprocessing (must match train_model.py exactly) ───────────────────

def preprocess(text: str) -> str:
    text = text.lower().strip()
    text = re.sub(r"[^\w\s]", " ", text)
    text = re.sub(r"\s+", " ", text)
    return text


# ── Load model bundle ─────────────────────────────────────────────────────────

def load_model() -> dict:
    if not os.path.exists(MODEL_PATH):
        raise FileNotFoundError(
            f"Model not found at {MODEL_PATH}. "
            "Run: python3 ai_module/train_model.py"
        )
    with open(MODEL_PATH, "rb") as f:
        return pickle.load(f)


# ── Load provider data ────────────────────────────────────────────────────────

def load_providers() -> list[dict]:
    with open(PROVIDERS_PATH, "r") as f:
        return json.load(f)


# ── Core NLP prediction ───────────────────────────────────────────────────────

def predict_service_and_category(
    model: dict,
    description_clean: str,
    category_hint: str | None,
    service_id_hint: int | None,
) -> tuple[str, str, float]:
    """
    Returns (predicted_service_id, predicted_category, confidence).

    If the customer already picked a service, we trust that (confidence=1.0).
    If they picked a category, we use it as a strong prior.
    Otherwise, both service and category come from the ML model.
    """

    # Customer pre-selected a specific service — skip prediction
    if service_id_hint:
        svc_id = str(service_id_hint)
        # Infer category from service
        cat = _category_for_service(svc_id)
        return svc_id, cat, 1.0

    # Run service classifier
    svc_pipeline = model["service_pipeline"]
    cat_pipeline  = model["category_pipeline"]
    cat_le        = model["category_le"]

    svc_probs     = svc_pipeline.predict_proba([description_clean])[0]
    svc_classes   = svc_pipeline.classes_
    top_svc_idx   = int(np.argmax(svc_probs))
    predicted_svc = svc_classes[top_svc_idx]
    svc_conf      = float(svc_probs[top_svc_idx])

    # Run category classifier
    cat_probs    = cat_pipeline.predict_proba([description_clean])[0]
    cat_idx      = int(np.argmax(cat_probs))
    predicted_cat = cat_le.inverse_transform([cat_idx])[0]
    cat_conf      = float(cat_probs[cat_idx])

    # If customer hinted a category and model's top differs, blend
    if category_hint and category_hint != predicted_cat:
        log.info(
            "Category hint '%s' overrides model prediction '%s' (conf=%.2f)",
            category_hint, predicted_cat, cat_conf,
        )
        predicted_cat = category_hint
        # Re-score: find best service within the hinted category
        cat_service_ids = _services_for_category(category_hint)
        mask = np.array([c in cat_service_ids for c in svc_classes])
        if mask.any():
            masked_probs = np.where(mask, svc_probs, 0)
            top_svc_idx = int(np.argmax(masked_probs))
            predicted_svc = svc_classes[top_svc_idx]
            svc_conf = float(masked_probs[top_svc_idx]) * cat_conf

    # Final confidence = geometric mean of svc and cat confidence
    confidence = math.sqrt(svc_conf * cat_conf)
    confidence = min(confidence, 0.99)  # cap at 0.99 — never claim perfect confidence

    return predicted_svc, predicted_cat, confidence


def _category_for_service(service_id: str) -> str:
    mapping = {
        "1": "plumbing", "2": "plumbing",
        "3": "electrical", "4": "electrical",
        "5": "carpentry",
        "6": "cleaning",
        "7": "painting",
    }
    return mapping.get(service_id, "general")


def _services_for_category(category: str) -> list[str]:
    mapping = {
        "plumbing":   ["1", "2"],
        "electrical": ["3", "4"],
        "carpentry":  ["5"],
        "cleaning":   ["6"],
        "painting":   ["7"],
        "hvac":       ["3", "4"],
    }
    return mapping.get(category, [])


# ── Provider scoring ──────────────────────────────────────────────────────────

def score_providers(
    providers: list[dict],
    predicted_category: str,
    predicted_service_id: str,
    pincode: str | None,
    budget_max: float | None,
) -> list[dict]:
    """
    Scores each eligible provider on 4 weighted dimensions:

    ┌─────────────────────────────────┬────────┐
    │ Dimension                       │ Weight │
    ├─────────────────────────────────┼────────┤
    │ Rating score  (0–5 → 0–1)       │  35%   │
    │ Experience    (log scale)        │  25%   │
    │ Job volume    (log scale)        │  20%   │
    │ Location match (pincode)        │  20%   │
    └─────────────────────────────────┴────────┘

    Budget filtering is a hard filter — providers whose min rate
    exceeds budget_max are excluded before scoring.
    """

    W_RATING     = 0.35
    W_EXPERIENCE = 0.25
    W_JOBS       = 0.20
    W_LOCATION   = 0.20

    # Max values for normalisation
    MAX_RATING = 5.0
    MAX_EXP    = 20.0   # cap normalisation at 20 years
    MAX_JOBS   = 500.0  # cap normalisation at 500 jobs

    scored = []

    for p in providers:
        if not p.get("is_available", False):
            continue

        # ── Category/service eligibility ──────────────────────────────────────
        provider_cats = [c.lower() for c in p.get("categories", [])]
        provider_svcs = [str(s) for s in p.get("service_ids", [])]

        category_match = predicted_category.lower() in provider_cats
        service_match  = predicted_service_id in provider_svcs

        # Must match either the specific service or at least the category
        if not (service_match or category_match):
            continue

        # ── Budget hard filter ────────────────────────────────────────────────
        hourly_rate = p.get("hourly_rate") or 0
        if budget_max and hourly_rate > budget_max:
            log.info(
                "Provider %d ('%s') excluded: rate ₹%d > budget ₹%d",
                p["provider_id"], p["name"], hourly_rate, budget_max,
            )
            continue

        # ── Scoring ───────────────────────────────────────────────────────────

        # 1. Rating score (linear, 0–1)
        rating      = float(p.get("avg_rating") or 0)
        rating_norm = min(rating / MAX_RATING, 1.0)

        # 2. Experience score (log scale — going from 0→5 yrs matters more than 15→20)
        exp      = float(p.get("years_experience") or 0)
        exp_norm = math.log1p(exp) / math.log1p(MAX_EXP)

        # 3. Job volume score (log scale — same reasoning as experience)
        jobs      = float(p.get("total_jobs") or 0)
        jobs_norm = math.log1p(jobs) / math.log1p(MAX_JOBS)

        # 4. Location score
        #    — exact pincode match → 1.0
        #    — same city (no pincode match) → 0.5
        #    — no location info → 0.3 (assume city-wide)
        provider_pincodes = p.get("pincodes", [])
        if pincode and pincode in provider_pincodes:
            loc_score = 1.0
        elif pincode and provider_pincodes:
            loc_score = 0.5
        else:
            loc_score = 0.3

        # Bonus: exact service match gets a 5% boost over category-only match
        specificity_bonus = 0.05 if service_match else 0.0

        # Weighted composite score
        composite = (
            W_RATING     * rating_norm +
            W_EXPERIENCE * exp_norm    +
            W_JOBS       * jobs_norm   +
            W_LOCATION   * loc_score   +
            specificity_bonus
        )

        # Clip to [0, 1]
        composite = min(max(composite, 0.0), 1.0)

        scored.append({
            "provider_id":      p["provider_id"],
            "name":             p["name"],
            "score":            round(composite, 4),
            "avg_rating":       rating,
            "total_jobs":       int(jobs),
            "years_experience": int(exp),
            "hourly_rate":      hourly_rate,
            # Breakdown for project report / transparency
            "_breakdown": {
                "rating_score":      round(rating_norm,  4),
                "experience_score":  round(exp_norm,     4),
                "jobs_score":        round(jobs_norm,     4),
                "location_score":    round(loc_score,    4),
                "specificity_bonus": specificity_bonus,
                "category_match":    category_match,
                "service_match":     service_match,
            },
        })

    # Sort descending by composite score
    scored.sort(key=lambda x: x["score"], reverse=True)
    return scored


# ── Main recommendation function ──────────────────────────────────────────────

def recommend(payload: dict) -> dict:
    log.info("Processing request_id=%s", payload.get("request_id"))

    # ── Load assets ───────────────────────────────────────────────────────────
    model     = load_model()
    providers = load_providers()

    # ── Extract inputs ────────────────────────────────────────────────────────
    title       = payload.get("title", "")
    description = payload.get("description", "")
    full_text   = f"{title} {description}"           # combine for richer context

    category_hint   = payload.get("category")        # may be None
    service_id_hint = payload.get("service_id")      # may be None
    pincode         = payload.get("pincode")
    budget_max      = payload.get("budget_max")

    if budget_max is not None:
        try:
            budget_max = float(budget_max)
        except (ValueError, TypeError):
            budget_max = None

    # ── Predict ───────────────────────────────────────────────────────────────
    clean_text = preprocess(full_text)
    predicted_svc_id, predicted_cat, confidence = predict_service_and_category(
        model, clean_text, category_hint, service_id_hint
    )

    log.info(
        "Prediction → service_id=%s  category=%s  confidence=%.3f",
        predicted_svc_id, predicted_cat, confidence,
    )

    # ── Score providers ───────────────────────────────────────────────────────
    scored = score_providers(
        providers, predicted_cat, predicted_svc_id, pincode, budget_max
    )

    # Return top 3 — enough for the UI without overwhelming the customer
    top_providers = scored[:3]

    # Strip internal _breakdown from the main provider list (move to separate key)
    scoring_breakdown = {p["provider_id"]: p.pop("_breakdown") for p in top_providers}

    # ── Build response ────────────────────────────────────────────────────────
    result = {
        "request_id":              payload.get("request_id"),
        "recommended_service":     SERVICE_NAMES.get(predicted_svc_id, "General Home Service"),
        "recommended_service_id":  int(predicted_svc_id) if predicted_svc_id.isdigit() else None,
        "predicted_category":      predicted_cat,
        "confidence":              round(confidence, 4),
        "top_providers":           top_providers,
        "model_version":           model.get("version", "v1.0"),
        "scoring_breakdown":       scoring_breakdown,
    }

    log.info(
        "Returning %d provider(s). Top: '%s' (score=%.3f)",
        len(top_providers),
        top_providers[0]["name"] if top_providers else "none",
        top_providers[0]["score"] if top_providers else 0,
    )

    return result


# ── Entry point ───────────────────────────────────────────────────────────────

def main():
    if len(sys.argv) < 2:
        error_out("No payload argument provided. Expected base64-encoded JSON.")

    # Laravel passes the payload as a base64-encoded JSON string
    try:
        raw = base64.b64decode(sys.argv[1]).decode("utf-8")
        payload = json.loads(raw)
    except Exception as e:
        error_out(f"Failed to decode payload: {e}")

    try:
        result = recommend(payload)
        # Print ONLY the JSON to stdout — Laravel reads this
        print(json.dumps(result, ensure_ascii=False))
    except FileNotFoundError as e:
        error_out(str(e))
    except Exception as e:
        log.exception("Unexpected error during recommendation")
        error_out(f"Recommendation engine error: {e}")


def error_out(message: str):
    """Print a structured error JSON and exit with code 1."""
    print(json.dumps({"error": message, "top_providers": []}), file=sys.stdout)
    sys.exit(1)


if __name__ == "__main__":
    main()
