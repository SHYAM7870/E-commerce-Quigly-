<footer class="site-footer">
    <style>
    /* ═══════════════════════════════════════════
       QUIGLY FOOTER — Premium Responsive v2
    ═══════════════════════════════════════════ */

    .site-footer *,
    .site-footer *::before,
    .site-footer *::after {
        box-sizing: border-box;
    }

    .site-footer {
        background: linear-gradient(180deg, #0b0d1e 0%, #060710 100%);
        border-top: 1px solid rgba(167, 139, 250, 0.12);
        padding: 72px 0 0;
        margin-top: 80px;
        font-family: 'Inter', sans-serif;
        color: rgba(255, 255, 255, 0.55);
        position: relative;
        overflow: hidden;
    }

    /* Ambient top glow */
    .site-footer::before {
        content: '';
        position: absolute;
        top: -120px;
        left: 50%;
        transform: translateX(-50%);
        width: 700px;
        height: 260px;
        background: radial-gradient(ellipse, rgba(124, 58, 237, 0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    /* ── Light mode ── */
    body.light-mode .site-footer {
        background: linear-gradient(180deg, #f1f5f9 0%, #e8edf5 100%);
        border-top: 1px solid rgba(0, 0, 0, 0.07);
        color: rgba(15, 23, 42, 0.55);
    }

    body.light-mode .site-footer::before {
        background: radial-gradient(ellipse, rgba(124, 58, 237, 0.07) 0%, transparent 70%);
    }

    /* ── Container ── */
    .site-footer .footer-container {
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* ── Top accent divider ── */
    .footer-accent-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 56px;
    }

    .footer-accent-bar .accent-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(124, 58, 237, 0.35), transparent);
    }

    .footer-accent-bar .accent-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
        box-shadow: 0 0 10px rgba(167, 139, 250, 0.6);
    }

    /* ── Main grid ── */
    .footer-grid {
        display: grid;
        grid-template-columns: 1.7fr 1fr 1fr 1.25fr;
        gap: 48px 40px;
        margin-bottom: 60px;
    }

    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 40px 32px;
        }
    }

    @media (max-width: 420px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }
    }

    /* ── Brand column ── */
    .footer-brand-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .footer-brand-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: linear-gradient(135deg, #7c3aed, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
        flex-shrink: 0;
    }

    .footer-brand-name {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
        font-size: 1.35rem;
        letter-spacing: -0.04em;
        background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .footer-tagline {
        font-size: 0.875rem;
        line-height: 1.75;
        color: rgba(255, 255, 255, 0.45);
        margin-bottom: 22px;
        max-width: 290px;
    }

    body.light-mode .footer-tagline {
        color: rgba(15, 23, 42, 0.5);
    }

    /* ── Social buttons ── */
    .footer-socials {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .social-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.09);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.55);
        font-size: 14px;
        transition: background 0.22s, border-color 0.22s, color 0.22s, transform 0.22s;
        text-decoration: none;
        flex-shrink: 0;
    }

    body.light-mode .social-btn {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: rgba(15, 23, 42, 0.5);
    }

    .social-btn:hover {
        background: rgba(124, 58, 237, 0.2);
        border-color: rgba(124, 58, 237, 0.45);
        color: #a78bfa;
        transform: translateY(-2px);
    }

    /* ── Trust badges ── */
    .footer-trust {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.4);
        padding: 5px 11px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 7px;
        white-space: nowrap;
    }

    body.light-mode .trust-badge {
        color: rgba(15, 23, 42, 0.48);
        background: rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.07);
    }

    .trust-badge i {
        color: #7c3aed;
        font-size: 0.8rem;
    }

    /* ── Column heading ── */
    .footer-col-title,
    .newsletter-title {
        font-family: 'Poppins', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .newsletter-title {
        margin-bottom: 8px;
    }

    .footer-col-title::after,
    .newsletter-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(124, 58, 237, 0.3), transparent);
    }

    body.light-mode .footer-col-title,
    body.light-mode .newsletter-title {
        color: rgba(15, 23, 42, 0.88);
    }

    /* ── Link list ── */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .footer-links a {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.48);
        text-decoration: none;
        transition: color 0.2s, padding-left 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    body.light-mode .footer-links a {
        color: rgba(15, 23, 42, 0.52);
    }

    .footer-links a::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(124, 58, 237, 0.45);
        flex-shrink: 0;
        transition: background 0.2s, transform 0.2s;
    }

    .footer-links a:hover {
        color: #a78bfa;
        padding-left: 3px;
    }

    .footer-links a:hover::before {
        background: #a78bfa;
        transform: scale(1.4);
    }

    /* ── Newsletter ── */
    .newsletter-desc {
        font-size: 0.845rem;
        color: rgba(255, 255, 255, 0.42);
        margin-bottom: 14px;
        line-height: 1.6;
    }

    body.light-mode .newsletter-desc {
        color: rgba(15, 23, 42, 0.48);
    }

    .newsletter-form {
        display: flex;
        gap: 8px;
    }

    .newsletter-input {
        flex: 1;
        min-width: 0;
        padding: 10px 13px;
        border-radius: 10px;
        font-size: 0.86rem;
        font-family: 'Inter', sans-serif;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
    }

    body.light-mode .newsletter-input {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        color: #0f172a;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.28);
    }

    body.light-mode .newsletter-input::placeholder {
        color: rgba(0, 0, 0, 0.28);
    }

    .newsletter-input:focus {
        border-color: rgba(124, 58, 237, 0.55);
        background: rgba(124, 58, 237, 0.04);
    }

    .newsletter-btn {
        padding: 10px 15px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #7c3aed, #2563eb);
        color: #fff;
        font-size: 0.86rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .newsletter-btn:hover {
        opacity: 0.88;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(124, 58, 237, 0.35);
    }

    .newsletter-btn:active {
        transform: translateY(0);
    }

    .newsletter-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    /* ── Payment strip ── */
    .footer-payments {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 18px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    body.light-mode .footer-payments {
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .payments-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.35);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-right: 4px;
        white-space: nowrap;
    }

    body.light-mode .payments-label {
        color: rgba(15, 23, 42, 0.38);
    }

    .payment-icon {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        height: 26px;
        padding-left: 15px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.09);
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    body.light-mode .payment-icon {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: rgba(15, 23, 42, 0.5);
    }

    /* ── Bottom bar ── */
    .footer-bottom {
        padding: 18px 0 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-copyright {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.3);
        line-height: 1.5;
    }

    body.light-mode .footer-copyright {
        color: rgba(15, 23, 42, 0.38);
    }

    .footer-legal {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .footer-legal a {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.3);
        text-decoration: none;
        transition: color 0.2s;
        white-space: nowrap;
    }

    body.light-mode .footer-legal a {
        color: rgba(15, 23, 42, 0.38);
    }

    .footer-legal a:hover {
        color: #a78bfa;
    }

    /* ── Responsive tweaks ── */
    @media (max-width: 1024px) {
        .site-footer {
            padding-top: 56px;
        }

        .footer-grid {
            margin-bottom: 48px;
        }
    }

    @media (max-width: 640px) {
        .site-footer {
            padding-top: 44px;
            margin-top: 56px;
        }

        .footer-accent-bar {
            margin-bottom: 36px;
        }

        .footer-grid {
            margin-bottom: 32px;
        }

        .footer-tagline {
            max-width: 100%;
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }

    @media (max-width: 380px) {
        .site-footer .footer-container {
            padding: 0 16px;
        }

        .newsletter-form {
            flex-direction: column;
        }

        .newsletter-btn {
            width: 100%;
            padding: 11px;
            text-align: center;
        }

        .footer-legal {
            flex-direction: column;
            gap: 8px;
        }
    }
    </style>

    <div class="footer-container">

        <!-- Decorative accent divider -->
        <div class="footer-accent-bar">
            <div class="accent-line"></div>
            <div class="accent-dot"></div>
            <div class="accent-line"></div>
        </div>

        <!-- Main 4-column grid -->
        <div class="footer-grid">

            <!-- ① Brand -->
            <div>
                <div class="footer-brand-row">
                    <div class="footer-brand-icon"><i class="fas fa-bolt"></i></div>
                    <span class="footer-brand-name">Quigly</span>
                </div>
                <p class="footer-tagline">Premium electronics, fashion &amp; more — delivered fast. Quality guaranteed,
                    every order.</p>
                <div class="footer-socials">
                    <a href="#" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn" title="Twitter / X"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-trust">
                    <div class="trust-badge"><i class="fas fa-shield-alt"></i>Secure Checkout</div>
                    <div class="trust-badge"><i class="fas fa-undo-alt"></i>Easy Returns</div>
                </div>
            </div>

            <!-- ② Quick Links -->
            <div>
                <div class="footer-col-title">Quick Links</div>
                <ul class="footer-links">
                    <li><a href="#" onclick="showSection('home')">Home</a></li>
                    <li><a href="#" onclick="showSection('products')">All Products</a></li>
                    <li><a href="#" onclick="showSection('categories')">Categories</a></li>
                    <li><a href="#" onclick="showSection('deals')">Today's Deals</a></li>
                    <li><a href="#" onclick="showSection('offers')">My Offers</a></li>
                </ul>
            </div>

            <!-- ③ Account -->
            <div>
                <div class="footer-col-title">Account</div>
                <ul class="footer-links">
                    <li><a href="#" onclick="showSection('profile')">My Profile</a></li>
                    <li><a href="#" onclick="showSection('orders')">My Orders</a></li>
                    <li><a href="#" onclick="showSection('favorites')">Wishlist</a></li>
                    <li><a href="#" onclick="showSection('cart')">My Cart</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>

            <!-- ④ Newsletter -->
            <div>
                <div class="newsletter-title">Stay Updated</div>
                <p class="newsletter-desc">Get exclusive deals and new arrivals straight to your inbox.</p>
                <div class="newsletter-form">
                    <input type="email" class="newsletter-input" placeholder="Your email address" id="newsletterEmail">
                    <button class="newsletter-btn" onclick="subscribeNewsletter()" title="Subscribe">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="newsletter-badges">
                    <div class="trust-badge"><i class="fas fa-truck-fast"></i>Fast Delivery</div>
                    <div class="trust-badge"><i class="fas fa-star"></i>Trusted Store</div>
                </div>
            </div>

        </div><!-- /footer-grid -->

        <!-- Payment methods strip -->
        <div class="footer-payments">
            <span class="payments-label">We accept</span>
            <span class="payment-icon"><i class="fab fa-cc-visa"></i></span>
            <span class="payment-icon"><i class="fab fa-cc-mastercard"></i></span>
            <span class="payment-icon"><i class="fab fa-cc-paypal"></i></span>
            <span class="payment-icon"><i class="fab fa-cc-amex"></i></span>
            <span class="payment-icon"><i class="fas fa-university"></i></span>
            <span class="payment-icon"><i class="fas fa-mobile-alt"></i></span>
        </div>

        <!-- Bottom bar -->
        <div class="footer-bottom">
            <span class="footer-copyright">&copy; <?php echo date('Y'); ?> Quigly. All rights reserved.</span>
            <div class="footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#" onclick="showSection('support')">Support</a>
            </div>
        </div>

    </div><!-- /footer-container -->

    <script>
    function subscribeNewsletter() {
        const email = document.getElementById('newsletterEmail');
        if (!email) return;
        const val = email.value.trim();
        if (!val || !val.includes('@')) {
            showToast('Invalid Email', 'Please enter a valid email address', 'error');
            return;
        }
        showToast('Subscribed! 🎉', "You're now on the Quigly mailing list", 'success');
        email.value = '';
    }
    </script>
</footer>
</div><!-- /main-content -->
</div><!-- /d-flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>