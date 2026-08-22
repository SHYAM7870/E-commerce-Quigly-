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

    <!-- ── Simulated Razorpay Modal ── -->
    <div id="razorpayMockOverlay" class="rzp-mock-overlay d-none">
        <div class="rzp-mock-card">
            <button type="button" class="rzp-close" onclick="closeRzpMockModal()">
                <i class="fas fa-times"></i>
            </button>

            <!-- Header -->
            <div class="rzp-mock-header">
                <div class="rzp-merchant-info">
                    <h4>Quigly Store</h4>
                    <p>Order Payment</p>
                    <div class="rzp-amount" id="rzpMockAmount">₹0.00</div>
                </div>
                <div class="rzp-logo-circle">Q</div>
            </div>

            <!-- Card Entry view -->
            <div id="rzpMockCardView" class="rzp-mock-body">
                <div class="section-title">Card Details (Demo Mode)</div>
                <div class="rzp-form-group">
                    <label>Card Number</label>
                    <input type="text" id="rzpCardNo" class="rzp-input" placeholder="4111 1111 1111 1111" maxlength="19" oninput="formatCardNo(this)">
                </div>
                <div class="rzp-input-row">
                    <div class="rzp-form-group">
                        <label>Expiry Date</label>
                        <input type="text" id="rzpCardExpiry" class="rzp-input" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)">
                    </div>
                    <div class="rzp-form-group">
                        <label>CVV</label>
                        <input type="password" id="rzpCardCvv" class="rzp-input" placeholder="123" maxlength="3">
                    </div>
                </div>
                <div class="rzp-form-group">
                    <label>Card Holder Name</label>
                    <input type="text" id="rzpCardName" class="rzp-input" placeholder="John Doe">
                </div>
                <button type="button" class="rzp-pay-btn" id="rzpPayBtn" onclick="processRzpMockPayment()">
                    Pay Securely
                </button>
            </div>

            <!-- Processing loading view -->
            <div id="rzpMockProcessingView" class="rzp-mock-body d-none">
                <div class="rzp-loading-container">
                    <div class="rzp-spinner"></div>
                    <h5 style="font-weight:700;margin-bottom:8px;">Processing Payment</h5>
                    <p style="font-size:12px;color:#7f8e9d;">Do not close or refresh this page.</p>
                </div>
            </div>

            <!-- Success view -->
            <div id="rzpMockSuccessView" class="rzp-mock-body d-none">
                <div class="rzp-success-container">
                    <div class="rzp-success-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <h5 style="font-weight:700;margin-bottom:8px;color:#2beb87;">Payment Successful!</h5>
                    <p style="font-size:12px;color:#7f8e9d;">Order is being created...</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="rzp-footer">
                <i class="fas fa-shield-alt"></i> Secured by <strong>Razorpay</strong>
            </div>
        </div>
    </div>

    <style>
        /* Razorpay Mock Popup Overlay CSS */
        .rzp-mock-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .rzp-mock-card {
            width: 360px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.3);
            overflow: hidden;
            color: #2b3940;
            position: relative;
            animation: rzpModalPop 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes rzpModalPop {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .rzp-mock-header {
            background: #17253a;
            color: #ffffff;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .rzp-mock-header .rzp-merchant-info h4 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 3px;
            letter-spacing: -0.2px;
        }
        .rzp-mock-header .rzp-merchant-info p {
            font-size: 11px;
            color: #98a4b3;
            margin: 0 0 6px;
        }
        .rzp-mock-header .rzp-merchant-info .rzp-amount {
            font-size: 18px;
            font-weight: 800;
            color: #2beb87;
        }
        .rzp-mock-header .rzp-logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #1372ec;
            color: #ffffff;
            font-weight: 900;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(19, 114, 236, 0.4);
        }
        .rzp-close {
            position: absolute;
            top: 12px;
            right: 14px;
            background: transparent;
            border: none;
            color: #7b8899;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.2s;
            z-index: 10;
        }
        .rzp-close:hover {
            color: #ffffff;
        }
        .rzp-mock-body {
            padding: 22px 24px;
            background: #f7f9fa;
        }
        .rzp-mock-body .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #7f8e9d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 16px;
        }
        .rzp-form-group {
            margin-bottom: 14px;
        }
        .rzp-form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #526375;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .rzp-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d2dce5;
            background: #ffffff;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #2b3940;
            outline: none;
            transition: border-color 0.2s;
        }
        .rzp-input:focus {
            border-color: #1372ec;
            background: #ffffff;
        }
        .rzp-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .rzp-pay-btn {
            width: 100%;
            padding: 12px;
            background: #1372ec;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(19, 114, 236, 0.25);
            transition: background-color 0.2s;
            margin-top: 10px;
        }
        .rzp-pay-btn:hover {
            background: #0f62cf;
        }
        .rzp-footer {
            padding: 12px;
            text-align: center;
            background: #ffffff;
            border-top: 1px solid #eef2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 10px;
            color: #92a1b0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .rzp-footer i {
            font-size: 12px;
            color: #1372ec;
        }

        /* Spinner / Success animations */
        .rzp-loading-container, .rzp-success-container {
            padding: 30px 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .rzp-spinner {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(19, 114, 236, 0.15);
            border-top-color: #1372ec;
            border-radius: 50%;
            animation: rzpSpin 1s infinite linear;
            margin-bottom: 18px;
        }
        @keyframes rzpSpin {
            to { transform: rotate(360deg); }
        }
        .rzp-success-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #2beb87;
            color: #ffffff;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(43, 235, 135, 0.3);
            margin-bottom: 18px;
            animation: rzpPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes rzpPop {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
    </style>
</section>
<script>
    function openRzpMockModal(total) {
        document.getElementById('rzpMockAmount').innerText = '₹' + (total + 5).toFixed(2);
        document.getElementById('rzpCardNo').value = '';
        document.getElementById('rzpCardExpiry').value = '';
        document.getElementById('rzpCardCvv').value = '';
        document.getElementById('rzpCardName').value = '';
        
        document.getElementById('rzpMockCardView').classList.remove('d-none');
        document.getElementById('rzpMockProcessingView').classList.add('d-none');
        document.getElementById('rzpMockSuccessView').classList.add('d-none');
        
        document.getElementById('razorpayMockOverlay').classList.remove('d-none');
    }

    function closeRzpMockModal() {
        document.getElementById('razorpayMockOverlay').classList.add('d-none');
        showToast('Payment Cancelled', 'You can try again or choose another payment method.', 'error');
    }

    function formatCardNo(el) {
        let val = el.value.replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < val.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += val[i];
        }
        el.value = formatted;
    }

    function formatExpiry(el) {
        let val = el.value.replace(/\D/g, '');
        if (val.length >= 2) {
            el.value = val.slice(0, 2) + '/' + val.slice(2, 4);
        } else {
            el.value = val;
        }
    }

    function processRzpMockPayment() {
        const cardNo = document.getElementById('rzpCardNo').value.trim();
        const expiry = document.getElementById('rzpCardExpiry').value.trim();
        const cvv = document.getElementById('rzpCardCvv').value.trim();
        const name = document.getElementById('rzpCardName').value.trim();
        
        if (cardNo.length < 15 || expiry.length < 5 || cvv.length < 3 || name === '') {
            showToast('Error', 'Please fill valid Card details.', 'error');
            return;
        }
        
        // Show Processing Screen
        document.getElementById('rzpMockCardView').classList.add('d-none');
        document.getElementById('rzpMockProcessingView').classList.remove('d-none');
        
        setTimeout(() => {
            // Show Success Screen
            document.getElementById('rzpMockProcessingView').classList.add('d-none');
            document.getElementById('rzpMockSuccessView').classList.remove('d-none');
            
            setTimeout(() => {
                // Hide modal and finish
                document.getElementById('razorpayMockOverlay').classList.add('d-none');
                
                // Call submitOrder
                const checkoutName = document.getElementById('checkoutName')?.value.trim();
                const checkoutPhone = document.getElementById('checkoutPhone')?.value.trim();
                const checkoutAddress = document.getElementById('checkoutAddress')?.value.trim();
                const totalText = document.getElementById('checkoutSubtotal').innerText;
                const total = parseFloat(totalText.replace(/[^\d\.]/g, ''));
                
                const mockPaymentId = 'pay_mock_' + Math.random().toString(36).substr(2, 9).toUpperCase();
                
                if (typeof submitOrder === 'function') {
                    submitOrder(checkoutName, checkoutPhone, checkoutAddress, 'CARD', total, mockPaymentId);
                } else {
                    // Fallback if defined differently
                    placeOrderWithId(checkoutName, checkoutPhone, checkoutAddress, 'CARD', total, mockPaymentId);
                }
            }, 1200);
        }, 1800);
    }

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
        
        const rzpPopup = document.getElementById('razorpayMockOverlay');
        if (rzpPopup && e.target === rzpPopup) {
            closeRzpMockModal();
        }
    });
</script>