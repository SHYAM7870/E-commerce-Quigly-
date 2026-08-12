<?php
// ── Return / Refund Page Section ──
// Called when user clicks "Return / Refund" on an order card.
// State is passed via JS: window._returnPageOrderId, window._returnPageProductName, window._returnPageAddress
?>
<section id="return_refund" class="content-section" style="display:none;">
<style>
/* ══════════════════════════════════════════════
   RETURN & REFUND PAGE — Premium Design
   Matches Quigly's dark/light system
══════════════════════════════════════════════ */

#return_refund {
    padding: 32px 20px 64px;
    min-height: 100vh;
}

/* ── Page header ── */
.rrp-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 32px;
    flex-wrap: wrap;
}
.rrp-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    transition: .2s;
    flex-shrink: 0;
}
.rrp-back-btn:hover { background: #e2e8f0; color: #1e293b; transform: translateX(-2px); }
body.dark-mode .rrp-back-btn { background: #1e293b; border-color: #334155; color: #94a3b8; }
body.dark-mode .rrp-back-btn:hover { background: #334155; color: #f1f5f9; }

.rrp-title-block {}
.rrp-title {
    font-size: 24px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
body.dark-mode .rrp-title { color: #f1f5f9; }
.rrp-title i { color: #7c3aed; }
.rrp-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}
body.dark-mode .rrp-subtitle { color: #94a3b8; }

/* ── Two-col layout ── */
.rrp-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto;
}
@media (max-width: 900px) {
    .rrp-layout { grid-template-columns: 1fr; }
}

/* ── Card shell ── */
.rrp-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 28px;
}
body.dark-mode .rrp-card { background: #1e293b; border-color: #334155; }

.rrp-card-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rrp-card-title i { color: #7c3aed; font-size: 14px; }

/* ── Form fields ── */
.rrp-label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 6px;
    display: block;
}
body.dark-mode .rrp-label { color: #94a3b8; }

.rrp-field {
    margin-bottom: 20px;
}

.rrp-select, .rrp-textarea, .rrp-input {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 14px;
    font-family: inherit;
    background: #f8fafc;
    color: #334155;
    transition: .2s;
    box-sizing: border-box;
    appearance: none;
}
body.dark-mode .rrp-select,
body.dark-mode .rrp-textarea,
body.dark-mode .rrp-input {
    background: #0f172a;
    border-color: #334155;
    color: #f1f5f9;
}
.rrp-select:focus,
.rrp-textarea:focus,
.rrp-input:focus {
    border-color: #7c3aed;
    outline: none;
    box-shadow: 0 0 0 4px rgba(124,58,237,.1);
    background: #fff;
}
body.dark-mode .rrp-select:focus,
body.dark-mode .rrp-textarea:focus,
body.dark-mode .rrp-input:focus {
    background: #1e293b;
}
.rrp-textarea { min-height: 100px; resize: vertical; }

/* ── Option chips ── */
.rrp-chip-grid {
    display: grid;
    gap: 10px;
    margin-bottom: 0;
}
.rrp-chip-grid-2 { grid-template-columns: repeat(2, 1fr); }
.rrp-chip-grid-3 { grid-template-columns: repeat(3, 1fr); }
@media (max-width: 480px) {
    .rrp-chip-grid-3 { grid-template-columns: repeat(2, 1fr); }
}

.rrp-chip {
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    cursor: pointer;
    transition: .18s;
    position: relative;
}
.rrp-chip input[type=radio] { display: none; }
.rrp-chip:hover { border-color: #a78bfa; background: #faf5ff; }
body.dark-mode .rrp-chip { border-color: #334155; background: #0f172a; }
body.dark-mode .rrp-chip:hover { border-color: #7c3aed; background: #1e293b; }
.rrp-chip:has(input:checked) {
    border-color: #7c3aed;
    background: linear-gradient(135deg, #faf5ff, #ede9fe);
    box-shadow: 0 0 0 3px rgba(124,58,237,.08);
}
body.dark-mode .rrp-chip:has(input:checked) {
    background: linear-gradient(135deg, #1e1030, #1e293b);
    border-color: #7c3aed;
}
.rrp-chip:has(input:checked)::after {
    content: '✓';
    position: absolute;
    top: 8px;
    right: 10px;
    font-size: 11px;
    font-weight: 800;
    color: #7c3aed;
}
.rrp-chip-name {
    font-weight: 700;
    font-size: 13px;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}
body.dark-mode .rrp-chip-name { color: #e2e8f0; }
.rrp-chip:has(input:checked) .rrp-chip-name { color: #7c3aed; }
.rrp-chip-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 3px;
}

/* ── Proof upload zone ── */
.rrp-drop-zone {
    border: 2px dashed #a78bfa;
    border-radius: 16px;
    padding: 24px;
    background: #faf5ff;
    text-align: center;
    cursor: pointer;
    transition: .2s;
}
body.dark-mode .rrp-drop-zone {
    background: #1e1030;
    border-color: #6d28d9;
}
.rrp-drop-zone:hover { background: #ede9fe; }
body.dark-mode .rrp-drop-zone:hover { background: #2d1060; }
.rrp-drop-icon { font-size: 28px; color: #7c3aed; margin-bottom: 8px; }
.rrp-drop-title { font-weight: 700; color: #7c3aed; font-size: 14px; }
.rrp-drop-sub { font-size: 12px; color: #94a3b8; margin-top: 4px; }

.rrp-proof-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
}
.rrp-proof-thumb {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    flex-shrink: 0;
}
.rrp-proof-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.rrp-proof-remove {
    position: absolute;
    top: 3px; right: 3px;
    width: 20px; height: 20px;
    background: rgba(239,68,68,.85);
    border: none; border-radius: 50%;
    color: #fff; font-size: 10px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: .15s;
}
.rrp-proof-remove:hover { background: #dc2626; transform: scale(1.15); }

/* ── Submit button ── */
.rrp-submit-btn {
    width: 100%;
    height: 52px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: .2s;
    box-shadow: 0 8px 24px rgba(124,58,237,.35);
}
.rrp-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(124,58,237,.45); }
.rrp-submit-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* ── Feedback ── */
.rrp-feedback {
    padding: 14px 18px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 600;
    display: none;
    margin-top: 16px;
}
.rrp-feedback.success { background: rgba(16,185,129,.1); color: #059669; border: 1px solid rgba(16,185,129,.25); }
.rrp-feedback.error   { background: rgba(239,68,68,.1);  color: #dc2626; border: 1px solid rgba(239,68,68,.25); }

/* ── Summary card (right side) ── */
.rrp-summary-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
body.dark-mode .rrp-summary-item { border-bottom-color: #334155; }
.rrp-summary-item:last-child { border-bottom: none; }
.rrp-sum-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ede9fe, #ddd6fe);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #7c3aed;
    flex-shrink: 0;
}
body.dark-mode .rrp-sum-icon { background: rgba(124,58,237,.15); }
.rrp-sum-label { font-size: 11px; color: #94a3b8; font-weight: 600; margin-bottom: 2px; }
.rrp-sum-value { font-size: 14px; font-weight: 700; color: #1e293b; }
body.dark-mode .rrp-sum-value { color: #f1f5f9; }

/* ── Policy boxes ── */
.rrp-policy-box {
    background: linear-gradient(135deg, #faf5ff, #ede9fe);
    border: 1px solid #ddd6fe;
    border-radius: 16px;
    padding: 16px 18px;
    margin-top: 16px;
}
body.dark-mode .rrp-policy-box { background: rgba(124,58,237,.08); border-color: rgba(124,58,237,.2); }
.rrp-policy-title {
    font-size: 12px;
    font-weight: 800;
    color: #7c3aed;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.rrp-policy-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.rrp-policy-list li {
    font-size: 12px;
    color: #64748b;
    padding: 3px 0;
    display: flex;
    align-items: flex-start;
    gap: 7px;
    line-height: 1.4;
}
body.dark-mode .rrp-policy-list li { color: #94a3b8; }
.rrp-policy-list li::before {
    content: '✓';
    color: #7c3aed;
    font-weight: 800;
    font-size: 11px;
    margin-top: 1px;
    flex-shrink: 0;
}

/* ── Steps timeline ── */
.rrp-steps { display: flex; flex-direction: column; gap: 0; }
.rrp-step {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    position: relative;
}
.rrp-step-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(124,58,237,.3);
}
.rrp-step-line {
    width: 2px;
    background: linear-gradient(180deg, #7c3aed44, transparent);
    flex-shrink: 0;
    height: 28px;
    margin: 4px 0 4px 15px;
}
.rrp-step-content { padding-top: 6px; padding-bottom: 12px; }
.rrp-step-title { font-size: 13px; font-weight: 700; color: #1e293b; }
body.dark-mode .rrp-step-title { color: #f1f5f9; }
.rrp-step-desc { font-size: 12px; color: #94a3b8; margin-top: 2px; }

/* ── Success screen ── */
.rrp-success-screen {
    display: none;
    text-align: center;
    padding: 60px 20px;
}
.rrp-success-screen.show { display: block; }
.rrp-success-icon {
    width: 88px; height: 88px;
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 36px; color: #16a34a;
    box-shadow: 0 8px 32px rgba(22,163,74,.2);
}
.rrp-success-title { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
body.dark-mode .rrp-success-title { color: #f1f5f9; }
.rrp-success-desc { color: #64748b; font-size: 14px; margin-bottom: 28px; max-width: 400px; margin-left: auto; margin-right: auto; }
.rrp-success-ref {
    display: inline-block;
    background: linear-gradient(135deg, #faf5ff, #ede9fe);
    border: 1.5px solid #c4b5fd;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 700;
    color: #7c3aed;
    margin-bottom: 24px;
}

@media (max-width: 576px) {
    #return_refund { padding: 16px 12px 48px; }
    .rrp-card { padding: 18px; }
    .rrp-layout { gap: 16px; }
}
</style>

<!-- ══ Page wrapper ══ -->
<div style="max-width: 1100px; margin: 0 auto;">

    <!-- Header -->
    <div class="rrp-header">
        <button class="rrp-back-btn" onclick="showSection('orders')" title="Back to My Orders">
            <i class="fas fa-arrow-left"></i>
        </button>
        <div class="rrp-title-block">
            <h2 class="rrp-title"><i class="fas fa-rotate-left"></i> Return / Refund Request</h2>
            <p class="rrp-subtitle" id="rrpSubtitle">Submit your return or refund request</p>
        </div>
    </div>

    <!-- Success screen (shown after submit) -->
    <div class="rrp-success-screen" id="rrpSuccessScreen">
        <div class="rrp-success-icon"><i class="fas fa-check"></i></div>
        <div class="rrp-success-title">Request Submitted!</div>
        <p class="rrp-success-desc">Our team will review your request and get back to you within 2–3 business days.</p>
        <div class="rrp-success-ref" id="rrpSuccessRef">Return Request Filed</div>
        <br>
        <button class="btn rounded-pill px-4 py-2" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; font-weight: 700; border: none;" onclick="showSection('orders')">
            <i class="fas fa-box me-2"></i>Back to My Orders
        </button>
    </div>

    <!-- Main form layout (hidden on success) -->
    <div class="rrp-layout" id="rrpFormLayout">

        <!-- LEFT: Form -->
        <div>
            <form id="rrpForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_id" id="rrpOrderId">

                <!-- Request Type -->
                <div class="rrp-card" style="margin-bottom: 20px;">
                    <div class="rrp-card-title"><i class="fas fa-hand-point-right"></i> What do you need?</div>
                    <div class="rrp-chip-grid rrp-chip-grid-2">
                        <label class="rrp-chip">
                            <input type="radio" name="request_type" value="return" required>
                            <div class="rrp-chip-name"><i class="fas fa-undo"></i> Return</div>
                            <div class="rrp-chip-sub">Send the item back</div>
                        </label>
                        <label class="rrp-chip">
                            <input type="radio" name="request_type" value="refund">
                            <div class="rrp-chip-name"><i class="fas fa-money-bill-wave"></i> Refund</div>
                            <div class="rrp-chip-sub">Get your money back</div>
                        </label>
                        <label class="rrp-chip">
                            <input type="radio" name="request_type" value="replacement">
                            <div class="rrp-chip-name"><i class="fas fa-box"></i> Replacement</div>
                            <div class="rrp-chip-sub">Get a new item</div>
                        </label>
                        <label class="rrp-chip">
                            <input type="radio" name="request_type" value="exchange">
                            <div class="rrp-chip-name"><i class="fas fa-arrows-rotate"></i> Exchange</div>
                            <div class="rrp-chip-sub">Swap for another</div>
                        </label>
                    </div>
                </div>

                <!-- Preferred Resolution -->
                <div class="rrp-card" style="margin-bottom: 20px;">
                    <div class="rrp-card-title"><i class="fas fa-bullseye"></i> Preferred Resolution</div>
                    <div class="rrp-chip-grid rrp-chip-grid-3">
                        <label class="rrp-chip">
                            <input type="radio" name="preferred_resolution" value="full_refund" required>
                            <div class="rrp-chip-name">Full Refund</div>
                            <div class="rrp-chip-sub">100% amount back</div>
                        </label>
                        <label class="rrp-chip">
                            <input type="radio" name="preferred_resolution" value="replacement_item">
                            <div class="rrp-chip-name">New Item</div>
                            <div class="rrp-chip-sub">Same product again</div>
                        </label>
                        <label class="rrp-chip">
                            <input type="radio" name="preferred_resolution" value="store_credit">
                            <div class="rrp-chip-name">Store Credit</div>
                            <div class="rrp-chip-sub">Balance added</div>
                        </label>
                    </div>
                </div>

                <!-- Reason + Details -->
                <div class="rrp-card" style="margin-bottom: 20px;">
                    <div class="rrp-card-title"><i class="fas fa-clipboard-list"></i> Reason & Details</div>

                    <div class="rrp-field">
                        <label class="rrp-label" for="rrpReason">Reason for return</label>
                        <select name="reason" id="rrpReason" class="rrp-select" required>
                            <option value="">— Select a reason —</option>
                            <option value="broken_product">Broken / Damaged product</option>
                            <option value="defective_item">Defective item</option>
                            <option value="wrong_item">Wrong item received</option>
                            <option value="missing_parts">Missing parts / accessories</option>
                            <option value="size_issue">Size / fit issue</option>
                            <option value="not_as_described">Not as described</option>
                            <option value="late_delivery">Late delivery</option>
                            <option value="changed_mind">Changed my mind</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Proof upload (shown for broken/defective) -->
                    <div id="rrpProofBox" style="display:none;" class="rrp-field">
                        <label class="rrp-label">Proof Photos <span style="color:#94a3b8; font-weight:400;">(optional, up to 5)</span></label>
                        <div class="rrp-drop-zone" id="rrpDropZone"
                             onclick="document.getElementById('rrpProofInput').click()"
                             ondragover="event.preventDefault(); this.style.background='#ede9fe';"
                             ondragleave="this.style.background='';"
                             ondrop="rrpHandleDrop(event)">
                            <div class="rrp-drop-icon"><i class="fas fa-camera"></i></div>
                            <div class="rrp-drop-title">Click or drag photos here</div>
                            <div class="rrp-drop-sub">JPG, PNG, WEBP · Max 5MB each</div>
                            <input type="file" id="rrpProofInput" name="proof_images[]"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   multiple style="display:none;"
                                   onchange="rrpHandleFiles(this.files)">
                        </div>
                        <div class="rrp-proof-grid" id="rrpProofGrid"></div>
                    </div>

                    <div class="rrp-field" style="margin-bottom:0;">
                        <label class="rrp-label" for="rrpDetails">Additional details <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                        <textarea name="details" id="rrpDetails" class="rrp-textarea"
                                  placeholder="Describe the issue in more detail — what happened, when, any other context…"></textarea>
                    </div>
                </div>

                <!-- Pickup Address -->
                <div class="rrp-card" style="margin-bottom: 24px;">
                    <div class="rrp-card-title"><i class="fas fa-map-marker-alt"></i> Pickup Address</div>
                    <div class="rrp-field" style="margin-bottom:0;">
                        <label class="rrp-label" for="rrpPickup">Address for item pickup / return</label>
                        <textarea name="pickup_address" id="rrpPickup" class="rrp-textarea"
                                  style="min-height:70px;"
                                  placeholder="Enter your full pickup address…" required></textarea>
                    </div>
                </div>

                <!-- Feedback message -->
                <div class="rrp-feedback" id="rrpFeedback"></div>

                <!-- Submit -->
                <button type="submit" class="rrp-submit-btn" id="rrpSubmitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Return Request
                </button>
            </form>
        </div>

        <!-- RIGHT: Summary + Info -->
        <div>
            <!-- Order Summary -->
            <div class="rrp-card" style="margin-bottom: 20px;">
                <div class="rrp-card-title"><i class="fas fa-receipt"></i> Order Summary</div>
                <div class="rrp-summary-item">
                    <div class="rrp-sum-icon"><i class="fas fa-hashtag"></i></div>
                    <div>
                        <div class="rrp-sum-label">Order ID</div>
                        <div class="rrp-sum-value" id="rrpSumOrderId">—</div>
                    </div>
                </div>
                <div class="rrp-summary-item">
                    <div class="rrp-sum-icon"><i class="fas fa-box"></i></div>
                    <div>
                        <div class="rrp-sum-label">Product</div>
                        <div class="rrp-sum-value" id="rrpSumProduct">—</div>
                    </div>
                </div>
                <div class="rrp-summary-item">
                    <div class="rrp-sum-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="rrp-sum-label">Processing Time</div>
                        <div class="rrp-sum-value">2–3 business days</div>
                    </div>
                </div>
            </div>

            <!-- Process Steps -->
            <div class="rrp-card" style="margin-bottom: 20px;">
                <div class="rrp-card-title"><i class="fas fa-route"></i> How it works</div>
                <div class="rrp-steps">
                    <div class="rrp-step">
                        <div class="rrp-step-dot">1</div>
                        <div class="rrp-step-content">
                            <div class="rrp-step-title">Submit Request</div>
                            <div class="rrp-step-desc">Fill the form and submit</div>
                        </div>
                    </div>
                    <div class="rrp-step-line"></div>
                    <div class="rrp-step">
                        <div class="rrp-step-dot">2</div>
                        <div class="rrp-step-content">
                            <div class="rrp-step-title">Team Review</div>
                            <div class="rrp-step-desc">We review in 2–3 business days</div>
                        </div>
                    </div>
                    <div class="rrp-step-line"></div>
                    <div class="rrp-step">
                        <div class="rrp-step-dot">3</div>
                        <div class="rrp-step-content">
                            <div class="rrp-step-title">Pickup Scheduled</div>
                            <div class="rrp-step-desc">We'll contact you to arrange pickup</div>
                        </div>
                    </div>
                    <div class="rrp-step-line"></div>
                    <div class="rrp-step">
                        <div class="rrp-step-dot">4</div>
                        <div class="rrp-step-content">
                            <div class="rrp-step-title">Resolution</div>
                            <div class="rrp-step-desc">Refund or replacement dispatched</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy -->
            <div class="rrp-card">
                <div class="rrp-card-title"><i class="fas fa-shield-alt"></i> Return Policy</div>
                <div class="rrp-policy-box">
                    <div class="rrp-policy-title"><i class="fas fa-info-circle"></i> Key Points</div>
                    <ul class="rrp-policy-list">
                        <li>Returns accepted within 7 days of delivery</li>
                        <li>Item must be in original condition &amp; packaging</li>
                        <li>Proof photos required for damaged items</li>
                        <li>Refund processed within 5–7 business days</li>
                        <li>Free pickup for defective items</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    let _rrpProofFiles = [];

    // ── Open page: called from order card ──
    window.openReturnPage = function(orderId, productName, address) {
        _rrpProofFiles = [];
        document.getElementById('rrpProofGrid').innerHTML = '';
        document.getElementById('rrpProofBox').style.display = 'none';
        document.getElementById('rrpProofInput').value = '';

        document.getElementById('rrpOrderId').value   = orderId;
        document.getElementById('rrpPickup').value    = address || '';
        document.getElementById('rrpSumOrderId').textContent = '#' + orderId;
        document.getElementById('rrpSumProduct').textContent = productName || '—';
        document.getElementById('rrpSubtitle').textContent   = 'Order #' + orderId + ' — ' + (productName || '');

        // Reset form
        document.getElementById('rrpForm').reset();
        document.getElementById('rrpOrderId').value = orderId;
        document.getElementById('rrpPickup').value  = address || '';

        const fb = document.getElementById('rrpFeedback');
        fb.style.display = 'none';
        fb.className = 'rrp-feedback';

        const btn = document.getElementById('rrpSubmitBtn');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Return Request';

        // Hide success, show form
        document.getElementById('rrpSuccessScreen').classList.remove('show');
        document.getElementById('rrpFormLayout').style.display = '';

        showSection('return_refund');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Proof image handling ──
    window.rrpHandleFiles = function(files) {
        Array.from(files).forEach(file => {
            if (_rrpProofFiles.length >= 5) return;
            if (!file.type.startsWith('image/')) return;
            _rrpProofFiles.push(file);
            _rrpRenderThumbs();
        });
        document.getElementById('rrpProofInput').value = '';
    };

    window.rrpHandleDrop = function(e) {
        e.preventDefault();
        document.getElementById('rrpDropZone').style.background = '';
        rrpHandleFiles(e.dataTransfer.files);
    };

    function _rrpRenderThumbs() {
        const grid = document.getElementById('rrpProofGrid');
        grid.innerHTML = '';
        _rrpProofFiles.forEach((file, idx) => {
            const wrap = document.createElement('div');
            wrap.className = 'rrp-proof-thumb';
            const img = document.createElement('img'); img.alt = 'proof';
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'rrp-proof-remove';
            btn.innerHTML = '<i class="fas fa-times"></i>';
            btn.onclick = () => { _rrpProofFiles.splice(idx, 1); _rrpRenderThumbs(); };
            wrap.appendChild(img); wrap.appendChild(btn);
            grid.appendChild(wrap);
        });
    }

    // Show proof box for damage reasons
    document.getElementById('rrpReason').addEventListener('change', function() {
        const show = ['broken_product','defective_item'].includes(this.value);
        document.getElementById('rrpProofBox').style.display = show ? 'block' : 'none';
        if (!show) { _rrpProofFiles = []; document.getElementById('rrpProofGrid').innerHTML = ''; }
    });

    // ── Form submit ──
    document.getElementById('rrpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('rrpSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…';

        const fb = document.getElementById('rrpFeedback');
        fb.style.display = 'none';

        const fd = new FormData(this);
        fd.delete('proof_images[]');
        _rrpProofFiles.forEach(f => fd.append('proof_images[]', f));

        // Build action path dynamically from base
        const base = window.location.pathname.replace(/\/[^\/]*$/, '');
        const actionUrl = base + '/admin/actions/return_request_action.php';

        fetch(actionUrl, { method: 'POST', body: fd })
            .then(res => res.text())
            .then(text => {
                let data;
                try { data = JSON.parse(text); }
                catch(err) {
                    console.error('Return action non-JSON:', text);
                    fb.className = 'rrp-feedback error';
                    fb.textContent = '✗ Server error — check console for details.';
                    fb.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Return Request';
                    return;
                }
                if (data.status === 'ok') {
                    document.getElementById('rrpFormLayout').style.display = 'none';
                    const ordId = document.getElementById('rrpOrderId').value;
                    document.getElementById('rrpSuccessRef').textContent = 'Request submitted for Order #' + ordId;
                    document.getElementById('rrpSuccessScreen').classList.add('show');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    const msgs = {
                        duplicate          : '✗ You already have an active return request for this order.',
                        not_eligible       : '✗ This order must be Delivered before a return can be requested.',
                        missing_fields     : '✗ Please fill in all required fields (reason and pickup address).',
                        not_logged_in      : '✗ Session expired. Please log in again and retry.',
                        db_error           : '✗ Database error while saving your request. Please try again.',
                        invalid_order_item : '✗ Could not find the order item. Please try again.',
                        invalid_method     : '✗ Invalid request method.',
                    };
                    fb.className = 'rrp-feedback error';
                    const errCode = data.error || 'unknown';
                    fb.textContent = msgs[errCode] || ('✗ Something went wrong (' + errCode + '). Please try again.');
                    fb.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Return Request';
                }
            })
            .catch(() => {
                fb.className = 'rrp-feedback error';
                fb.textContent = '✗ Could not reach server. Check your connection.';
                fb.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Return Request';
            });
    });
})();
</script>
</section>