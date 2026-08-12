<section id="checkout" class="content-section checkout-premium" style="display:none;">
    <div class="container py-5">
        <div class="checkout-hero mb-4">
            <div class="checkout-hero-left">
                <span class="checkout-kicker"><i class="fas fa-lock"></i> Secure Checkout</span>
                <h2 class="checkout-title">Complete Your Order</h2>
                <p class="checkout-subtitle">Fast delivery, secure payment, and a smooth checkout experience.</p>
            </div>
            <div class="checkout-hero-right">
                <div class="checkout-stat"><i class="fas fa-shield-alt"></i><span>100% Safe</span></div>
                <div class="checkout-stat"><i class="fas fa-truck-fast"></i><span>Quick Delivery</span></div>
                <div class="checkout-stat"><i class="fas fa-headset"></i><span>24/7 Support</span></div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="checkout-card premium-card">
                    <div class="card-heading">
                        <div>
                            <span class="card-eyebrow">Delivery details</span>
                            <h4 class="card-title">Shipping Information</h4>
                        </div>
                        <div class="card-badge"><i class="fas fa-user-check"></i> Verified checkout</div>
                    </div>

                    <div class="checkout-input-grid">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-user"></i>
                                <input type="text" id="checkoutName" class="form-control checkout-input"
                                    value="<?= htmlspecialchars($data['name'] ?? '') ?>"
                                    placeholder="Enter your full name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-phone"></i>
                                    <input type="tel" id="checkoutPhone" class="form-control checkout-input"
                                    value="<?= htmlspecialchars($data['number'] ?? '') ?>"
                                    placeholder="Enter your phone number" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <div class="input-icon-wrap textarea-wrap">
                            <i class="fas fa-location-dot"></i>
                            <textarea id="checkoutAddress" class="form-control checkout-input" rows="4"
                                placeholder="House no, Street, City, State, PIN" required></textarea>
                        </div>
                    </div>

                    <div class="payment-section">
                        <div class="section-head">
                            <div>
                                <span class="card-eyebrow">Payment method</span>
                                <h4 class="card-title">Choose How You Want to Pay</h4>
                            </div>
                        </div>

                        <div class="payment-options">
                            <label class="payment-option active" data-payment="COD">
                                <input type="radio" name="payment_ui" checked>
                                <span class="payment-content">
                                    <span class="payment-icon cod"><i class="fas fa-money-bill-wave"></i></span>
                                    <span class="payment-text">
                                        <strong>Cash On Delivery</strong>
                                        <small>Pay when your order arrives</small>
                                    </span>
                                    <span class="payment-check"><i class="fas fa-check"></i></span>
                                </span>
                            </label>

                            <label class="payment-option" data-payment="UPI">
                                <input type="radio" name="payment_ui">
                                <span class="payment-content">
                                    <span class="payment-icon upi"><i class="fas fa-qrcode"></i></span>
                                    <span class="payment-text">
                                        <strong>UPI / QR Scan</strong>
                                        <small>Pay instantly with PhonePe, GPay, Paytm</small>
                                    </span>
                                    <span class="payment-check"><i class="fas fa-check"></i></span>
                                </span>
                            </label>

                            <label class="payment-option" data-payment="CARD">
                                <input type="radio" name="payment_ui">
                                <span class="payment-content">
                                    <span class="payment-icon card"><i class="fas fa-credit-card"></i></span>
                                    <span class="payment-text">
                                        <strong>Debit / Credit Card</strong>
                                        <small>Secure card payment</small>
                                    </span>
                                    <span class="payment-check"><i class="fas fa-check"></i></span>
                                </span>
                            </label>
                        </div>
                        <select id="checkoutPayment" class="d-none">
                            <option value="COD" selected>Cash On Delivery</option>
                            <option value="UPI">UPI</option>
                            <option value="CARD">CARD</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="checkout-summary-card premium-card">
                    <div class="card-heading">
                        <div>
                            <span class="card-eyebrow">Order summary</span>
                            <h4 class="card-title">Your Items</h4>
                        </div>
                        <div class="card-badge"><i class="fas fa-lock"></i> Secure payment</div>
                    </div>

                    <div class="upi-showcase">
                        <div class="upi-left">
                            <div class="upi-logo-circle">
                                <i class="fas fa-indian-rupee-sign"></i>
                            </div>
                            <div>
                                <h5>UPI Accepted Here</h5>
                                <p>Scan with any UPI app for fast payment.</p>
                            </div>
                        </div>
                        <div class="upi-tags">
                            <span><i class="fab fa-google-pay"></i> GPay</span>
                            <span><i class="fas fa-mobile-screen"></i> PhonePe</span>
                            <span><i class="fas fa-wallet"></i> Paytm</span>
                        </div>
                    </div>

                    <div id="checkoutItems" class="checkout-items-list mb-3"></div>

                    <div class="summary-lines">
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <strong id="checkoutSubtotal">₹0.00</strong>
                        </div>
                        <div class="summary-line">
                            <span>Shipping</span>
                            <strong>₹5.00</strong>
                        </div>
                        <div class="summary-line total">
                            <span>Total</span>
                            <strong id="checkoutTotal">₹0.00</strong>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 checkout-btn-main" onclick="placeOrder()">
                        <i class="fas fa-lock me-2"></i> Place Order Securely
                    </button>

                    <button type="button" class="btn btn-outline-secondary w-100 mt-2 checkout-btn-secondary"
                        onclick="showSection('cart')">
                        <i class="fas fa-arrow-left me-2"></i> Back to Cart
                    </button>

                    <div class="checkout-note">
                        <i class="fas fa-shield-heart"></i>
                        <span>Your information is protected with encrypted checkout.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="upiPopup" class="upi-popup-overlay d-none">
        <div class="upi-popup-card">
            <button type="button" class="upi-close-btn" onclick="closeUpiPopup()">
                <i class="fas fa-times"></i>
            </button>

            <div class="upi-popup-head">
                <span class="upi-popup-badge"><i class="fas fa-qrcode"></i> UPI Payment</span>
                <h3>Scan & Pay</h3>
                <p>Use any UPI app to complete payment instantly.</p>
            </div>

            <div class="upi-qr-box">
                <img src="assets/images/QR/my_qr.jpeg" alt="UPI QR Code">
            </div>

            <div class="upi-id-box">
                <span>UPI ID</span>
                <strong>7863911670@ybl</strong>
            </div>

            <div class="upi-app-row">
                <span><i class="fab fa-google-pay"></i> GPay</span>
                <span><i class="fas fa-mobile-screen"></i> PhonePe</span>
                <span><i class="fas fa-wallet"></i> Paytm</span>
            </div>

            <button type="button" class="btn btn-primary w-100 upi-done-btn" onclick="closeUpiPopup()">
                <i class="fas fa-check me-2"></i> Done
            </button>
        </div>
    </div>
</section>
<script>
    function openUpiPopup() {
        const popup = document.getElementById('upiPopup');
        if (popup) popup.classList.remove('d-none');
    }
    function closeUpiPopup() {
        const popup = document.getElementById('upiPopup');
        if (popup) popup.classList.add('d-none');
    }

    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', () => {
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
            option.classList.add('active');

            const payment = option.getAttribute('data-payment');
            const hiddenSelect = document.getElementById('checkoutPayment');
            if (hiddenSelect) hiddenSelect.value = payment;

            const radio = option.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            if (payment === 'UPI') {
                openUpiPopup();
            } else {
                closeUpiPopup();
            }
        });
    });

    document.addEventListener('click', function (e) {
        const popup = document.getElementById('upiPopup');
        if (popup && e.target === popup) {
            closeUpiPopup();
        }
    });
</script>