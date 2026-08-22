<footer class="site-footer">
    <style>
    /* ═══════════════════════════════════════════
       QUIGLY SHARED FOOTER — Premium Adaptive v2
       Matches landing page footer look & feel
    ═══════════════════════════════════════════ */

    .site-footer *,
    .site-footer *::before,
    .site-footer *::after {
        box-sizing: border-box;
    }

    .site-footer {
        background: #07080e;
        border-top: 1px solid rgba(255, 255, 255, .06);
        padding: 64px 0 32px;
        margin-top: 80px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #94a3b8;
        position: relative;
        overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
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
        background: radial-gradient(ellipse, rgba(124, 58, 237, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    /* ── Light mode override ── */
    body.light-mode .site-footer {
        background: #f8fafc;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        color: #475569;
    }

    body.light-mode .site-footer::before {
        background: radial-gradient(ellipse, rgba(124, 58, 237, 0.04) 0%, transparent 70%);
    }

    /* ── Container ── */
    .site-footer .footer-container {
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* Decorative Divider Accent Bar */
    .footer-accent-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 48px;
    }

    .footer-accent-bar .accent-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(124, 58, 237, 0.25), transparent);
    }

    .footer-accent-bar .accent-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #7c3aed;
        box-shadow: 0 0 8px rgba(124, 58, 237, 0.5);
    }

    /* ── Main grid ── */
    .footer-grid {
        display: grid;
        grid-template-columns: 1.5fr repeat(3, 1fr);
        gap: 40px;
        margin-bottom: 48px;
    }

    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 32px;
        }
    }

    /* ── Brand column ── */
    .footer-brand-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .footer-brand-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: linear-gradient(135deg, #7c3aed, #4f46e5, #06b6d4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .footer-brand-name {
        font-size: 20px;
        font-weight: 800;
        color: #ffffff;
        transition: color 0.3s;
    }

    body.light-mode .footer-brand-name {
        color: #0f172a;
    }

    .footer-tagline {
        font-size: 13.5px;
        line-height: 1.6;
        color: #94a3b8;
        margin: 0 0 18px;
        max-width: 280px;
        transition: color 0.3s;
    }

    body.light-mode .footer-tagline {
        color: #475569;
    }

    /* ── Social buttons ── */
    .footer-socials {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .social-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, .06);
        background: rgba(255, 255, 255, .03);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    body.light-mode .social-btn {
        border-color: rgba(15, 23, 42, 0.08);
        background: rgba(15, 23, 42, .03);
        color: #475569;
    }

    .social-btn:hover {
        border-color: #7c3aed;
        color: #ffffff;
        background: #7c3aed;
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
        font-size: 10.5px;
        font-weight: 600;
        color: #94a3b8;
        padding: 5px 12px;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 6px;
        white-space: nowrap;
        letter-spacing: .3px;
        transition: all 0.2s ease;
    }

    body.light-mode .trust-badge {
        color: #475569;
        background: rgba(15, 23, 42, .03);
        border-color: rgba(15, 23, 42, .08);
    }

    .trust-badge:hover {
        border-color: #7c3aed;
        color: #7c3aed;
    }

    .trust-badge i {
        color: #7c3aed;
        font-size: 11px;
    }

    /* ── Column heading ── */
    .footer-col-title,
    .newsletter-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #ffffff;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
    }

    body.light-mode .footer-col-title,
    body.light-mode .newsletter-title {
        color: #0f172a;
    }

    /* ── Link list ── */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-links a {
        font-size: 13.5px;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
    }

    body.light-mode .footer-links a {
        color: #475569;
    }

    .footer-links a:hover {
        color: #7c3aed;
    }

    /* ── Newsletter description ── */
    .newsletter-desc {
        font-size: 13.5px;
        color: #94a3b8;
        margin-bottom: 14px;
        line-height: 1.6;
        transition: color 0.3s;
    }

    body.light-mode .newsletter-desc {
        color: #475569;
    }

    .newsletter-form {
        display: flex;
        gap: 8px;
    }

    .newsletter-input {
        flex: 1;
        min-width: 0;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13.5px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        outline: none;
        transition: all 0.2s ease;
    }

    body.light-mode .newsletter-input {
        background: #ffffff;
        border-color: rgba(15, 23, 42, 0.1);
        color: #0f172a;
    }

    .newsletter-input::placeholder {
        color: #64748b;
    }

    .newsletter-input:focus {
        border-color: #7c3aed;
    }

    .newsletter-btn {
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 8px;
        background: #7c3aed;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease;
    }

    .newsletter-btn:hover {
        background-color: #6d28d9;
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
        transition: border-color 0.3s;
    }

    body.light-mode .footer-payments {
        border-color: rgba(15, 23, 42, 0.08);
    }

    .payments-label {
        font-size: 10.5px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-right: 4px;
        white-space: nowrap;
        transition: color 0.3s;
    }

    body.light-mode .payments-label {
        color: #475569;
    }

    .payment-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 28px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        font-size: 14px;
        transition: border-color 0.3s, background-color 0.3s, color 0.3s;
    }

    body.light-mode .payment-icon {
        background: rgba(15, 23, 42, .03);
        border-color: rgba(15, 23, 42, .08);
        color: #475569;
    }

    /* ── Bottom bar ── */
    .footer-bottom {
        padding: 24px 0 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-copyright {
        font-size: 13px;
        color: #94a3b8;
        opacity: 0.85;
        transition: color 0.3s;
    }

    body.light-mode .footer-copyright {
        color: #475569;
    }

    .footer-legal {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .footer-legal a {
        font-size: 13px;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
        white-space: nowrap;
    }

    body.light-mode .footer-legal a {
        color: #475569;
    }

    .footer-legal a:hover {
        color: #7c3aed;
    }

    /* ── Responsive Mobile Accordion Tweaks (Under 768px) ── */
    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .footer-grid > div {
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            padding-bottom: 16px;
        }

        body.light-mode .footer-grid > div {
            border-bottom-color: rgba(15, 23, 42, 0.08);
        }

        .footer-grid > div:last-of-type {
            border-bottom: none;
        }

        /* Skip collapsing the first column (Brand details) */
        .footer-grid > div:first-of-type {
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            padding-bottom: 16px;
        }
        body.light-mode .footer-grid > div:first-of-type {
            border-bottom-color: rgba(15, 23, 42, 0.08);
        }

        .footer-col-title, .newsletter-title {
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 10px 0;
        }

        .footer-col-title::after, .newsletter-title::after {
            content: "▼";
            font-size: 9px;
            color: #94a3b8;
            transition: transform 0.3s ease;
            opacity: 0.65;
            flex: none;
            background: transparent;
            height: auto;
            width: auto;
        }

        body.light-mode .footer-col-title::after, 
        body.light-mode .newsletter-title::after {
            color: #475569;
        }

        .footer-grid > div.active .footer-col-title::after,
        .footer-grid > div.active .newsletter-title::after {
            transform: rotate(180deg);
        }

        .footer-links, .newsletter-desc, .newsletter-form, .newsletter-badges {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, margin-top 0.3s ease;
            margin-top: 0;
            gap: 8px;
            padding: 0;
        }

        /* Show when active */
        .footer-grid > div.active .footer-links {
            max-height: 250px;
            margin-top: 14px;
        }
        
        .footer-grid > div.active .newsletter-desc {
            max-height: 80px;
            margin-top: 10px;
        }
        
        .footer-grid > div.active .newsletter-form {
            max-height: 60px;
            margin-top: 10px;
            display: flex;
        }
        
        .footer-grid > div.active .newsletter-badges {
            max-height: 80px;
            margin-top: 10px;
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
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
                <p class="footer-tagline">Premium electronics, fashion &amp; more — delivered fast. Quality guaranteed, every order.</p>
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

    // ── Mobile Footer Accordion Toggling ──
    document.querySelectorAll('.footer-col-title, .newsletter-title').forEach(title => {
        title.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                const col = title.parentElement;
                
                // Toggle active state
                col.classList.toggle('active');
                
                // Collapse other accordion columns
                document.querySelectorAll('.footer-grid > div').forEach(otherCol => {
                    if (otherCol !== col) {
                        otherCol.classList.remove('active');
                    }
                });
            }
        });
    });
    </script>
</footer>
</div><!-- /main-content -->
</div><!-- /d-flex wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>