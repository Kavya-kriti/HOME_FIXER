@extends('layouts.customer')

@section('title', 'New Service Request')
@section('page-title', 'New Request')

@push('styles')
<style>
    .request-form-wrap {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── Steps header ────────────────────────────────────────────── */
    .steps-bar {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 1.75rem;
    }

    .step {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .8rem;
        color: var(--ink-3);
        font-family: 'Syne', sans-serif;
    }

    .step.active { color: var(--accent); }
    .step.done   { color: var(--green);  }

    .step-num {
        width: 24px; height: 24px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem;
        font-weight: 700;
        background: var(--bg);
        border: 1.5px solid var(--border);
        color: var(--ink-3);
        flex-shrink: 0;
    }

    .step.active .step-num { background: var(--accent); border-color: var(--accent); color: #fff; }
    .step.done   .step-num { background: var(--green);  border-color: var(--green);  color: #fff; }

    .step-line {
        flex: 1;
        height: 1.5px;
        background: var(--border);
        margin: 0 .5rem;
        max-width: 60px;
    }

    /* ── Form card ───────────────────────────────────────────────── */
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .form-section {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .form-section:last-child { border-bottom: none; }

    .form-section-title {
        font-family: 'Syne', sans-serif;
        font-size: .85rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .form-section-title svg { width: 15px; height: 15px; color: var(--accent); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

    .form-group { display: flex; flex-direction: column; gap: .35rem; }

    label {
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--ink-2);
    }

    .opt-label { font-size: .7rem; font-weight: 400; text-transform: none; letter-spacing: 0; color: var(--ink-3); }

    .input-wrap { position: relative; }

    .input-icon {
        position: absolute;
        left: .8rem;
        top: 50%;
        transform: translateY(-50%);
        width: 15px; height: 15px;
        color: var(--ink-3);
        pointer-events: none;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="time"],
    textarea,
    select {
        width: 100%;
        padding: .65rem .875rem .65rem 2.2rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Epilogue', sans-serif;
        font-size: .9rem;
        color: var(--ink);
        background: var(--bg);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        appearance: none;
    }

    textarea { padding-left: .875rem; resize: vertical; min-height: 110px; line-height: 1.6; }
    select { padding-left: 2.2rem; cursor: pointer; }

    input:focus, textarea:focus, select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(224,123,57,.1);
        background: var(--surface);
    }

    input.is-error { border-color: var(--red); }
    input.is-error:focus { box-shadow: 0 0 0 3px rgba(185,28,28,.1); }

    .field-error {
        font-size: .775rem;
        color: var(--red);
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    .field-error svg { width: 12px; height: 12px; }

    .char-count {
        font-size: .72rem;
        color: var(--ink-3);
        text-align: right;
        margin-top: .2rem;
    }

    /* ── Service category pills ──────────────────────────────────── */
    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: .75rem;
    }

    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .85rem;
        border: 1.5px solid var(--border);
        border-radius: 99px;
        font-size: .8rem;
        cursor: pointer;
        transition: border-color .15s, background .15s, color .15s;
        color: var(--ink-2);
        background: transparent;
        font-family: 'Epilogue', sans-serif;
    }

    .cat-pill:hover { border-color: var(--accent); color: var(--accent); }
    .cat-pill.selected { border-color: var(--accent); background: rgba(224,123,57,.08); color: var(--accent-dark); font-weight: 500; }

    /* ── Budget range display ────────────────────────────────────── */
    .budget-display {
        text-align: center;
        font-family: 'Syne', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: .5rem;
    }

    /* ── AI info box ─────────────────────────────────────────────── */
    .ai-info {
        background: linear-gradient(135deg, #1A1714 0%, #2D2824 100%);
        border-radius: var(--radius);
        padding: 1.4rem;
        color: #fff;
        box-shadow: var(--shadow-md);
    }

    .ai-info-head {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin-bottom: .75rem;
    }

    .ai-chip {
        display: flex;
        align-items: center;
        gap: .35rem;
        background: var(--accent);
        color: #fff;
        padding: .25rem .6rem;
        border-radius: 99px;
        font-family: 'Syne', sans-serif;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .ai-chip svg { width: 11px; height: 11px; }

    .ai-info-title {
        font-family: 'Syne', sans-serif;
        font-size: .95rem;
        font-weight: 600;
        margin-bottom: .4rem;
    }

    .ai-info-body {
        font-size: .82rem;
        color: rgba(255,255,255,.55);
        line-height: 1.6;
    }

    .ai-features {
        display: flex;
        flex-direction: column;
        gap: .5rem;
        margin-top: 1rem;
    }

    .ai-feature {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .8rem;
        color: rgba(255,255,255,.65);
    }

    .ai-feature svg { width: 14px; height: 14px; color: var(--accent-dim); flex-shrink: 0; }

    /* ── Submit area ─────────────────────────────────────────────── */
    .submit-bar {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-top: 1px solid var(--border);
        background: var(--bg);
    }

    .submit-note { font-size: .8rem; color: var(--ink-3); }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1.75rem;
        background: var(--sidebar-bg);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-family: 'Syne', sans-serif;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity .15s;
    }

    .btn-submit:hover { opacity: .85; }
    .btn-submit svg { width: 16px; height: 16px; }

    @media (max-width: 900px) {
        .request-form-wrap { grid-template-columns: 1fr; }
        .form-row, .form-row-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Step indicator --}}
<div class="steps-bar">
    <div class="step active">
        <span class="step-num">1</span>
        Describe Issue
    </div>
    <div class="step-line"></div>
    <div class="step">
        <span class="step-num">2</span>
        AI Matching
    </div>
    <div class="step-line"></div>
    <div class="step">
        <span class="step-num">3</span>
        Confirm Provider
    </div>
</div>

<div class="request-form-wrap">

    {{-- ── Main form ──────────────────────────────────────────────── --}}
    <form method="POST" action="{{ route('customer.request.store') }}" id="requestForm">
        @csrf

        <div class="form-card">

            {{-- Section 1: What's the issue --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i data-feather="alert-circle"></i>
                    What's the problem?
                </div>

                {{-- Category filter pills --}}
                <div style="margin-bottom:.75rem;">
                    <label style="margin-bottom:.5rem; display:block;">Filter by category <span class="opt-label">(optional)</span></label>
                    <div class="category-pills" id="catPills">
                        <button type="button" class="cat-pill selected" data-cat="">All</button>
                        @foreach($categories as $cat)
                            <button type="button" class="cat-pill {{ $selectedCategory === $cat->slug ? 'selected' : '' }}"
                                    data-cat="{{ $cat->slug }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Service dropdown --}}
                <div class="form-group" style="margin-bottom:1rem;">
                    <label for="service_id">Specific service <span class="opt-label">(or let AI decide)</span></label>
                    <div class="input-wrap">
                        <i data-feather="layers" class="input-icon"></i>
                        <select name="service_id" id="service_id"
                                class="{{ $errors->has('service_id') ? 'is-error' : '' }}">
                            <option value="">— Let AI recommend the best service —</option>
                            @foreach($categories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach($cat->services as $svc)
                                        <option value="{{ $svc->id }}"
                                                data-cat="{{ $cat->slug }}"
                                                {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                            {{ $svc->name }} — from ₹{{ number_format($svc->base_price) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Title --}}
                <div class="form-group" style="margin-bottom:1rem;">
                    <label for="title">Request title</label>
                    <div class="input-wrap">
                        <i data-feather="type" class="input-icon"></i>
                        <input type="text" name="title" id="title"
                               value="{{ old('title') }}"
                               placeholder="e.g. Kitchen sink leaking under the pipe joint"
                               class="{{ $errors->has('title') ? 'is-error' : '' }}"
                               required>
                    </div>
                    @error('title')
                        <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description">Detailed description</label>
                    <textarea name="description" id="description"
                              placeholder="Describe the issue in detail — when it started, how severe it is, any relevant context..."
                              class="{{ $errors->has('description') ? 'is-error' : '' }}"
                              maxlength="2000"
                              oninput="updateCharCount(this)"
                              required>{{ old('description') }}</textarea>
                    <div class="char-count"><span id="charCount">0</span> / 2000</div>
                    @error('description')
                        <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Section 2: Location --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i data-feather="map-pin"></i>
                    Service location
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label for="address">Full address</label>
                    <div class="input-wrap">
                        <i data-feather="home" class="input-icon"></i>
                        <input type="text" name="address" id="address"
                               value="{{ old('address') }}"
                               placeholder="House no., street, locality"
                               class="{{ $errors->has('address') ? 'is-error' : '' }}"
                               required>
                    </div>
                    @error('address')
                        <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <div class="input-wrap">
                            <i data-feather="map" class="input-icon"></i>
                            <input type="text" name="city" id="city"
                                   value="{{ old('city', 'Ranchi') }}"
                                   placeholder="Ranchi"
                                   class="{{ $errors->has('city') ? 'is-error' : '' }}"
                                   required>
                        </div>
                        @error('city')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pincode">PIN code</label>
                        <div class="input-wrap">
                            <i data-feather="hash" class="input-icon"></i>
                            <input type="text" name="pincode" id="pincode"
                                   value="{{ old('pincode') }}"
                                   placeholder="834001"
                                   maxlength="10"
                                   class="{{ $errors->has('pincode') ? 'is-error' : '' }}"
                                   required>
                        </div>
                        @error('pincode')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Hidden lat/lng for future GPS support --}}
                <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
            </div>

            {{-- Section 3: Budget & Schedule --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i data-feather="calendar"></i>
                    Budget &amp; schedule <span class="opt-label" style="font-family:inherit; font-size:.78rem; margin-left:.3rem;">(optional)</span>
                </div>

                <div class="form-row" style="margin-bottom:1rem;">
                    <div class="form-group">
                        <label for="budget_min">Min budget (₹)</label>
                        <div class="input-wrap">
                            <i data-feather="trending-down" class="input-icon"></i>
                            <input type="number" name="budget_min" id="budget_min"
                                   value="{{ old('budget_min') }}"
                                   placeholder="500"
                                   min="0" step="50"
                                   class="{{ $errors->has('budget_min') ? 'is-error' : '' }}">
                        </div>
                        @error('budget_min')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="budget_max">Max budget (₹)</label>
                        <div class="input-wrap">
                            <i data-feather="trending-up" class="input-icon"></i>
                            <input type="number" name="budget_max" id="budget_max"
                                   value="{{ old('budget_max') }}"
                                   placeholder="2000"
                                   min="0" step="50"
                                   class="{{ $errors->has('budget_max') ? 'is-error' : '' }}">
                        </div>
                        @error('budget_max')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="preferred_date">Preferred date</label>
                        <div class="input-wrap">
                            <i data-feather="calendar" class="input-icon"></i>
                            <input type="date" name="preferred_date" id="preferred_date"
                                   value="{{ old('preferred_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="{{ $errors->has('preferred_date') ? 'is-error' : '' }}">
                        </div>
                        @error('preferred_date')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="preferred_time">Preferred time</label>
                        <div class="input-wrap">
                            <i data-feather="clock" class="input-icon"></i>
                            <input type="time" name="preferred_time" id="preferred_time"
                                   value="{{ old('preferred_time') }}"
                                   class="{{ $errors->has('preferred_time') ? 'is-error' : '' }}">
                        </div>
                        @error('preferred_time')
                            <span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit bar --}}
            <div class="submit-bar">
                <span class="submit-note">
                    <i data-feather="cpu" style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:3px;"></i>
                    AI will analyse your request instantly
                </span>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i data-feather="cpu"></i>
                    Get AI Recommendations
                </button>
            </div>

        </div>
    </form>

    {{-- ── Sidebar info panel ──────────────────────────────────── --}}
    <div>
        <div class="ai-info">
            <div class="ai-info-head">
                <span class="ai-chip"><i data-feather="cpu"></i> AI Powered</span>
            </div>
            <div class="ai-info-title">How our AI helps you</div>
            <div class="ai-info-body">
                Describe your problem in plain language. Our machine learning engine analyses
                your description, location, and budget to surface the best-matched local experts.
            </div>
            <div class="ai-features">
                <div class="ai-feature"><i data-feather="check-circle"></i>Reads your issue description</div>
                <div class="ai-feature"><i data-feather="check-circle"></i>Filters by your location &amp; budget</div>
                <div class="ai-feature"><i data-feather="check-circle"></i>Ranks providers by rating &amp; experience</div>
                <div class="ai-feature"><i data-feather="check-circle"></i>Returns results in under 2 seconds</div>
            </div>
        </div>

        <div class="card" style="margin-top:1rem; padding:1.1rem;">
            <div style="font-family:'Syne',sans-serif; font-size:.82rem; font-weight:600; margin-bottom:.6rem;">
                Tips for better matches
            </div>
            <ul style="font-size:.8rem; color:var(--ink-2); line-height:1.8; padding-left:1rem;">
                <li>Be specific: "ceiling fan" vs just "electrical"</li>
                <li>Mention severity — minor drip vs major burst</li>
                <li>Set a budget range for more relevant results</li>
                <li>Pick a preferred date if you have one in mind</li>
            </ul>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Category pill filtering — shows/hides services in the dropdown
document.querySelectorAll('#catPills .cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('#catPills .cat-pill').forEach(p => p.classList.remove('selected'));
        pill.classList.add('selected');
        const cat = pill.dataset.cat;
        const sel = document.getElementById('service_id');
        Array.from(sel.options).forEach(opt => {
            if (!opt.value) return; // keep the default "let AI decide" option
            opt.hidden = cat && opt.dataset.cat !== cat;
        });
        sel.value = ''; // Reset selection when filter changes
    });
});

// Pre-select the category from URL query param
const preselect = '{{ $selectedCategory ?? "" }}';
if (preselect) {
    const btn = document.querySelector(`[data-cat="${preselect}"]`);
    if (btn) btn.click();
}

// Character count for textarea
function updateCharCount(el) {
    document.getElementById('charCount').textContent = el.value.length;
}
updateCharCount(document.getElementById('description'));

// Show loading state on submit
document.getElementById('requestForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Analysing with AI...';
});
</script>
@endpush
