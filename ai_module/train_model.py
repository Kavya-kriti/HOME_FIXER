"""
train_model.py
==============
Trains the HomeFixer AI recommendation model and saves it to model.pkl.

Run once (or whenever training data changes):
    python3 ai_module/train_model.py

What it trains:
  1. TF-IDF vectoriser  — converts issue descriptions to numeric feature vectors
  2. Naive Bayes classifier — predicts the service_id from the description
  3. Category label encoder — maps category slug ↔ integer

All three are bundled into a single pickle so recommender.py loads one file.
"""

import os
import sys
import json
import pickle
import logging
import pandas as pd
import numpy as np

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report

# ── Logging ───────────────────────────────────────────────────────────────────

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%H:%M:%S",
)
log = logging.getLogger(__name__)

# ── Paths ─────────────────────────────────────────────────────────────────────

BASE_DIR   = os.path.dirname(os.path.abspath(__file__))
DATA_PATH  = os.path.join(BASE_DIR, "data", "training_data.csv")
MODEL_PATH = os.path.join(BASE_DIR, "model.pkl")


# ── Text preprocessing ────────────────────────────────────────────────────────

def preprocess(text: str) -> str:
    """
    Lowercase and strip punctuation.
    Kept minimal — TF-IDF handles the heavy lifting.
    """
    import re
    text = text.lower().strip()
    text = re.sub(r"[^\w\s]", " ", text)   # remove punctuation
    text = re.sub(r"\s+", " ", text)        # collapse whitespace
    return text


# ── Training ──────────────────────────────────────────────────────────────────

def train():
    log.info("Loading training data from %s", DATA_PATH)
    df = pd.read_csv(DATA_PATH)

    if df.empty:
        log.error("Training data is empty — aborting.")
        sys.exit(1)

    log.info("Loaded %d samples covering %d categories and %d unique services.",
             len(df), df["category"].nunique(), df["service_id"].nunique())

    # Clean text
    df["description_clean"] = df["description"].apply(preprocess)

    # ── Pipeline 1: description → service_id ─────────────────────────────────
    # TF-IDF converts text to weighted word-frequency vectors.
    # MultinomialNB is fast, effective on text, and works well with small datasets.
    service_pipeline = Pipeline([
        ("tfidf", TfidfVectorizer(
            ngram_range=(1, 2),      # unigrams + bigrams ("water leak" is more useful than "water" alone)
            max_features=3000,
            min_df=1,                # include rare terms (small dataset)
            sublinear_tf=True,       # log-scale TF to dampen very frequent terms
            strip_accents="unicode",
        )),
        ("clf", MultinomialNB(alpha=0.3)),   # alpha < 1 = less smoothing, sharper predictions
    ])

    # ── Pipeline 2: description → category ───────────────────────────────────
    category_le = LabelEncoder()
    y_cat = category_le.fit_transform(df["category"])

    category_pipeline = Pipeline([
        ("tfidf", TfidfVectorizer(
            ngram_range=(1, 2),
            max_features=3000,
            min_df=1,
            sublinear_tf=True,
            strip_accents="unicode",
        )),
        ("clf", MultinomialNB(alpha=0.3)),
    ])

    # ── Train/test split for evaluation ──────────────────────────────────────
    X = df["description_clean"]
    y_svc = df["service_id"].astype(str)

    if len(X) >= 10:
        X_tr, X_te, y_tr_s, y_te_s, y_tr_c, y_te_c = train_test_split(
            X, y_svc, y_cat, test_size=0.2, random_state=42, stratify=y_cat
        )
    else:
        # Too few samples to split — just train on all
        X_tr = X_te = X
        y_tr_s = y_te_s = y_svc
        y_tr_c = y_te_c = y_cat

    # Fit both pipelines
    log.info("Fitting service_id classifier …")
    service_pipeline.fit(X_tr, y_tr_s)

    log.info("Fitting category classifier …")
    category_pipeline.fit(X_tr, y_tr_c)

    # ── Evaluation ────────────────────────────────────────────────────────────
    svc_preds = service_pipeline.predict(X_te)
    cat_preds = category_pipeline.predict(X_te)

    log.info("=== Service classifier report ===")
    print(classification_report(y_te_s, svc_preds, zero_division=0))

    log.info("=== Category classifier report ===")
    cat_labels = category_le.inverse_transform(y_te_c)
    cat_pred_labels = category_le.inverse_transform(cat_preds)
    print(classification_report(cat_labels, cat_pred_labels, zero_division=0))

    # ── Bundle and save ───────────────────────────────────────────────────────
    bundle = {
        "version":            "v1.0",
        "service_pipeline":   service_pipeline,
        "category_pipeline":  category_pipeline,
        "category_le":        category_le,
    }

    with open(MODEL_PATH, "wb") as f:
        pickle.dump(bundle, f)

    log.info("✅ Model saved to %s", MODEL_PATH)
    log.info("   Bundle keys: %s", list(bundle.keys()))


if __name__ == "__main__":
    train()
