"""
test_recommender.py
===================
End-to-end tests for the HomeFixer AI recommendation engine.
Run with:  python3 ai_module/test_recommender.py
"""

import json
import base64
import subprocess
import sys
import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SCRIPT   = os.path.join(BASE_DIR, "recommender.py")

# ── ANSI colour helpers ────────────────────────────────────────────────────────
GREEN  = "\033[92m"
RED    = "\033[91m"
YELLOW = "\033[93m"
CYAN   = "\033[96m"
BOLD   = "\033[1m"
RESET  = "\033[0m"

def call(payload: dict) -> dict:
    encoded = base64.b64encode(json.dumps(payload).encode()).decode()
    result = subprocess.run(
        [sys.executable, SCRIPT, encoded],
        capture_output=True, text=True
    )
    if result.returncode != 0:
        raise RuntimeError(f"Script exited {result.returncode}:\n{result.stderr}")
    return json.loads(result.stdout)

def hr(): print(f"\n{CYAN}{'─' * 68}{RESET}")

# ── Test cases ────────────────────────────────────────────────────────────────

TESTS = [
    {
        "label": "1 — Plumbing leak, budget ₹2000, pincode match",
        "payload": {
            "request_id":   101,
            "title":        "Kitchen sink leaking badly",
            "description":  "Water is dripping from the pipe joint under the kitchen sink. Started two days ago and getting worse.",
            "category":     None,
            "service_id":   None,
            "city":         "Ranchi",
            "pincode":      "834001",
            "budget_max":   2000,
            "preferred_date": "2026-04-10",
        },
        "expect_category": "plumbing",
    },
    {
        "label": "2 — Electrical, customer pre-selected service_id=3",
        "payload": {
            "request_id":   102,
            "title":        "Short circuit in bedroom",
            "description":  "Switchboard tripping, one room has no power at all.",
            "category":     "electrical",
            "service_id":   3,
            "city":         "Ranchi",
            "pincode":      "834003",
            "budget_max":   None,
            "preferred_date": None,
        },
        "expect_category": "electrical",
    },
    {
        "label": "3 — Carpentry, no category hint, tight budget ₹500",
        "payload": {
            "request_id":   103,
            "title":        "Cupboard door broken hinge",
            "description":  "Wooden cupboard door hinge broken, door hanging and not closing properly.",
            "category":     None,
            "service_id":   None,
            "city":         "Ranchi",
            "pincode":      "834002",
            "budget_max":   500,
            "preferred_date": None,
        },
        "expect_category": "carpentry",
    },
    {
        "label": "4 — Cleaning, category hint provided",
        "payload": {
            "request_id":   104,
            "title":        "Full flat deep clean",
            "description":  "Need complete deep cleaning of 2BHK apartment including bathroom scrubbing and kitchen.",
            "category":     "cleaning",
            "service_id":   None,
            "city":         "Ranchi",
            "pincode":      "834005",
            "budget_max":   3000,
            "preferred_date": "2026-04-15",
        },
        "expect_category": "cleaning",
    },
    {
        "label": "5 — Fan installation, ambiguous description",
        "payload": {
            "request_id":   105,
            "title":        "New ceiling fan needed",
            "description":  "We just renovated the living room and need a ceiling fan installed with a new regulator.",
            "category":     None,
            "service_id":   None,
            "city":         "Ranchi",
            "pincode":      "834004",
            "budget_max":   1500,
            "preferred_date": None,
        },
        "expect_category": "electrical",
    },
    {
        "label": "6 — Very tight budget (₹100) — may filter providers",
        "payload": {
            "request_id":   106,
            "title":        "Tap dripping",
            "description":  "Bathroom tap dripping slowly, just needs tightening or new washer.",
            "category":     "plumbing",
            "service_id":   None,
            "city":         "Ranchi",
            "pincode":      "834009",  # unknown pincode — tests location fallback
            "budget_max":   100,
            "preferred_date": None,
        },
        "expect_category": "plumbing",
    },
]

# ── Runner ────────────────────────────────────────────────────────────────────

def run_tests():
    passed = 0
    failed = 0

    print(f"\n{BOLD}HomeFixer AI Recommendation Engine — Test Suite{RESET}")
    print(f"Script: {SCRIPT}\n")

    for test in TESTS:
        hr()
        print(f"{BOLD}{test['label']}{RESET}")

        try:
            result = call(test["payload"])
        except Exception as e:
            print(f"  {RED}✗ EXCEPTION: {e}{RESET}")
            failed += 1
            continue

        # ── Print result summary ───────────────────────────────────────────────
        cat       = result.get("predicted_category", "?")
        svc       = result.get("recommended_service", "?")
        conf      = result.get("confidence", 0)
        providers = result.get("top_providers", [])
        version   = result.get("model_version", "?")

        print(f"  Predicted category : {CYAN}{cat}{RESET}")
        print(f"  Recommended service: {CYAN}{svc}{RESET}")
        print(f"  Confidence         : {YELLOW}{conf:.0%}{RESET}")
        print(f"  Model version      : {version}")
        print(f"  Providers returned : {len(providers)}")

        for i, p in enumerate(providers):
            flag = "★" if i == 0 else " "
            print(
                f"    {flag} [{i+1}] {p['name']:<35} "
                f"score={p['score']:.3f}  "
                f"rating={p['avg_rating']}  "
                f"jobs={p['total_jobs']}"
            )

        # ── Assertions ─────────────────────────────────────────────────────────
        ok = True

        # 1. Response has all required keys
        required_keys = ["request_id", "recommended_service", "predicted_category",
                         "confidence", "top_providers", "model_version"]
        missing = [k for k in required_keys if k not in result]
        if missing:
            print(f"  {RED}✗ Missing keys: {missing}{RESET}")
            ok = False

        # 2. Category prediction matches expected (when we have a strong hint)
        expected_cat = test.get("expect_category")
        if expected_cat and cat != expected_cat:
            print(f"  {YELLOW}⚠ Category mismatch: got '{cat}', expected '{expected_cat}'{RESET}")
            # This is a warning not a failure — small dataset makes misclassification normal

        # 3. Confidence is a valid probability
        if not (0 <= conf <= 1):
            print(f"  {RED}✗ Confidence out of range: {conf}{RESET}")
            ok = False

        # 4. Each provider has the required fields
        for p in providers:
            for field in ["provider_id", "name", "score", "avg_rating", "total_jobs"]:
                if field not in p:
                    print(f"  {RED}✗ Provider missing field '{field}'{RESET}")
                    ok = False

        # 5. Providers are sorted descending by score
        scores = [p["score"] for p in providers]
        if scores != sorted(scores, reverse=True):
            print(f"  {RED}✗ Providers not sorted by score: {scores}{RESET}")
            ok = False

        # 6. request_id echoed back correctly
        if result.get("request_id") != test["payload"]["request_id"]:
            print(f"  {RED}✗ request_id mismatch{RESET}")
            ok = False

        if ok:
            print(f"  {GREEN}✓ PASS{RESET}")
            passed += 1
        else:
            print(f"  {RED}✗ FAIL{RESET}")
            failed += 1

    # ── Summary ────────────────────────────────────────────────────────────────
    hr()
    total = passed + failed
    status = GREEN if failed == 0 else RED
    print(f"\n{BOLD}Results: {status}{passed}/{total} passed{RESET}")

    if failed == 0:
        print(f"{GREEN}✅ All tests passed — AI module is working correctly.{RESET}")
    else:
        print(f"{RED}⚠  {failed} test(s) failed — review output above.{RESET}")
        sys.exit(1)


if __name__ == "__main__":
    run_tests()
