<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Quigly | Premium Login
    </title>

    <!-- BOOTSTRAP -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FONT AWESOME -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- GOOGLE FONT -->

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: #020617;
        }

        /* =========================
           MAIN WRAPPER
        ========================= */

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* =========================
           LEFT SIDE
        ========================= */

        .auth-left {
            width: 55%;
            position: relative;

            background:
                linear-gradient(rgba(2, 6, 23, .45),
                    rgba(2, 6, 23, .75)),

                url('https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1600&auto=format&fit=crop');

            background-size: cover;
            background-position: center;

            display: flex;
            align-items: center;

            padding: 5rem;

            color: #fff;

            overflow: hidden;
        }

        .auth-left::before {
            content: '';

            position: absolute;

            width: 700px;
            height: 700px;

            border-radius: 50%;

            background: rgba(99, 102, 241, .15);

            top: -250px;
            right: -250px;

            filter: blur(40px);
        }

        .left-content {
            position: relative;
            z-index: 2;
            max-width: 620px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: .7rem;

            padding: 14px 22px;

            border-radius: 50px;

            background: rgba(255, 255, 255, .12);

            backdrop-filter: blur(12px);

            font-weight: 700;

            margin-bottom: 2.5rem;

            border: 1px solid rgba(255, 255, 255, .12);
        }

        .brand-badge i {
            font-size: 1.1rem;
        }

        .left-content h1 {

            font-size: 4.2rem;

            line-height: 1.08;

            font-weight: 900;

            margin-bottom: 1.8rem;
        }

        .left-content p {

            font-size: 1.15rem;

            line-height: 1.9;

            color: rgba(255, 255, 255, .78);

            margin-bottom: 2.5rem;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {

            display: flex;
            align-items: center;
            gap: .9rem;

            font-weight: 600;

            color: #f8fafc;
        }

        .feature-item i {
            color: #818cf8;
        }

        /* =========================
           RIGHT SIDE
        ========================= */

        .auth-right {

            width: 45%;

            background: #020617;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 2rem;
        }

        .login-card {

            width: 100%;
            max-width: 470px;

            background: #0f172a;

            border-radius: 34px;

            padding: 3rem;

            border: 1px solid rgba(255, 255, 255, .06);

            box-shadow:
                0 25px 60px rgba(0, 0, 0, .45);

            position: relative;
            overflow: hidden;
        }

        .login-card::before {

            content: '';

            position: absolute;

            width: 220px;
            height: 220px;

            background: rgba(99, 102, 241, .14);

            border-radius: 50%;

            top: -120px;
            right: -120px;

            filter: blur(20px);
        }

        .mobile-brand {

            width: 86px;
            height: 86px;

            margin: auto;
            margin-bottom: 1.5rem;

            border-radius: 28px;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #7c3aed);

            display: flex;
            align-items: center;
            justify-content: center;

            color: #fff;

            font-size: 2rem;

            box-shadow:
                0 20px 35px rgba(99, 102, 241, .35);
        }

        .login-title {

            color: #fff;

            font-weight: 800;

            font-size: 2.2rem;

            margin-bottom: .7rem;
        }

        .login-subtitle {

            color: #94a3b8;

            margin-bottom: 2rem;
        }

        /* =========================
           FORM
        ========================= */

        .form-label {

            color: #f8fafc;

            font-weight: 700;

            margin-bottom: .7rem;
        }

        .input-box {
            position: relative;
        }

        .input-box i {

            position: absolute;

            top: 50%;
            left: 20px;

            transform: translateY(-50%);

            color: #94a3b8;

            z-index: 2;
        }

        .form-control {

            height: 60px;

            border-radius: 20px;

            padding-left: 55px;

            background: #1e293b;

            border: 1px solid #334155;

            color: #fff;

            font-size: 1rem;

            transition: .25s;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {

            background: #1e293b;

            color: #fff;

            border-color: #6366f1;

            box-shadow:
                0 0 0 4px rgba(99, 102, 241, .18);
        }

        .forgot-link {

            text-decoration: none;

            color: #818cf8;

            font-weight: 600;

            transition: .2s;
        }

        .forgot-link:hover {
            color: #a5b4fc;
        }

        /* =========================
           LOGIN BUTTON
        ========================= */

        .btn-login {

            width: 100%;
            height: 60px;

            border: none;

            border-radius: 20px;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #7c3aed);

            color: #fff;

            font-weight: 700;

            font-size: 1rem;

            transition: .3s;

            margin-top: .5rem;
        }

        .btn-login:hover {

            transform: translateY(-3px);

            box-shadow:
                0 18px 35px rgba(99, 102, 241, .35);
        }

        /* =========================
           DIVIDER
        ========================= */

        .divider {

            display: flex;
            align-items: center;

            margin: 2rem 0;

            color: #94a3b8;

            font-size: .85rem;

            font-weight: 700;
        }

        .divider::before,
        .divider::after {

            content: '';

            flex: 1;

            height: 1px;

            background: #334155;
        }

        .divider span {
            padding: 0 14px;
        }

        /* =========================
           SOCIAL BUTTONS
        ========================= */

        .social-buttons {

            display: flex;

            gap: 1rem;
        }

        .social-btn {

            flex: 1;

            height: 56px;

            border-radius: 18px;

            border: 1px solid #334155;

            background: #111827;

            color: #fff;

            font-weight: 700;

            transition: .25s;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
        }

        .social-btn:hover {

            transform: translateY(-2px);

            background: #1e293b;
        }

        .google-btn:hover {
            border-color: #ef4444;
        }

        .apple-btn:hover {
            border-color: #818cf8;
        }

        /* =========================
           SIGNUP
        ========================= */

        .signup-link {

            text-align: center;

            margin-top: 2rem;

            color: #94a3b8;

            font-size: .96rem;
        }

        .signup-link a {

            text-decoration: none;

            color: #818cf8;

            font-weight: 700;
        }

        .signup-link a:hover {
            color: #a5b4fc;
        }

        .auth-alert {

            width: 100%;

            border-radius: 20px;

            padding: 16px 18px;

            margin-bottom: 1.5rem;

            display: flex;

            align-items: flex-start;

            gap: 14px;

            border: 1px solid rgba(239, 68, 68, .25);

            background: rgba(239, 68, 68, .08);

            backdrop-filter: blur(12px);

            color: #fecaca;

            animation: fadeSlide .35s ease;
        }

        .auth-alert i {

            font-size: 1.2rem;

            margin-top: 2px;

            color: #ef4444;
        }

        .auth-alert strong {

            display: block;

            font-size: .98rem;

            font-weight: 700;

            margin-bottom: 4px;

            color: #fff;
        }

        .auth-alert p {

            margin: 0;

            font-size: .9rem;

            line-height: 1.5;

            color: #cbd5e1;
        }

        @keyframes fadeSlide {

            from {

                opacity: 0;

                transform:
                    translateY(-10px);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0);
            }
        }

        /* =========================
           MOBILE
        ========================= */

        @media(max-width:992px) {

            .auth-left {
                display: none;
            }

            .auth-right {
                width: 100%;
            }

        }

        @media(max-width:576px) {

            .auth-right {
                padding: 1.2rem;
            }

            .login-card {

                padding: 2rem 1.5rem;

                border-radius: 28px;
            }

            .login-title {
                font-size: 1.8rem;
            }

        }
    </style>

</head>

<body>

    <div class="auth-wrapper">

        <!-- LEFT -->

        <div class="auth-left">

            <div class="left-content">

                <div class="brand-badge">

                    <i class="fa fa-shopping-bag"></i>

                    Quigly

                </div>

                <h1>

                    Premium Shopping
                    Experience Starts
                    Here

                </h1>

                <p>

                    Access your orders, wishlist,
                    cart and personalized recommendations
                    in one secure place.

                </p>

                <div class="feature-list">

                    <div class="feature-item">

                        <i class="fa fa-check-circle"></i>

                        Secure Authentication

                    </div>

                    <div class="feature-item">

                        <i class="fa fa-bolt"></i>

                        Fast Checkout Experience

                    </div>

                    <div class="feature-item">

                        <i class="fa fa-shield"></i>

                        Safe Payment Protection

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="auth-right">

            <div class="login-card">

                <div class="text-center">

                    <div class="mobile-brand">

                        <i class="fa fa-shopping-cart"></i>

                    </div>

                    <div class="login-title">

                        Welcome Back

                    </div>

                    <div class="login-subtitle">

                        Login to continue your shopping journey

                    </div>

                </div>

                <!-- FORM -->
                <?php if (isset($_GET['success'])) { ?>
                    <div class="auth-alert alert-success">
                        <i class="fa fa-check-circle me-2"></i>
                        <div>
                            <strong>Success</strong>
                            <p><?= htmlspecialchars($_GET['success']); ?></p>
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['error'])) { ?>

                    <div class="auth-alert">

                        <i class="fa fa-ban"></i>

                        <div>

                            <strong>
                                Account Access Restricted
                            </strong>

                            <p>

                                <?=
                                    htmlspecialchars(
                                        $_GET['error']
                                    );
                                ?>

                            </p>

                        </div>

                    </div>

                <?php } ?>
                <form action="admin/actions/login_action.php" method="POST">

                    <!-- EMAIL -->

                    <div class="mb-4">

                        <label class="form-label">

                            Email Address

                        </label>

                        <div class="input-box">

                            <i class="fa fa-envelope"></i>

                            <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                required>

                        </div>

                    </div>

                    <!-- PASSWORD -->

                    <div class="mb-3">

                        <label class="form-label">

                            Password

                        </label>

                        <div class="input-box">

                            <i class="fa fa-lock"></i>

                            <input type="password" name="password" class="form-control"
                                placeholder="Enter your password" required>

                        </div>

                    </div>

                    <div class="d-flex justify-content-end mb-4">

                        <button type="button" class="forgot-link btn btn-link p-0 border-0" data-bs-toggle="modal"
                            data-bs-target="#forgotPasswordModal">
                            Forgot Password?
                        </button>

                    </div>

                    <!-- BUTTON -->

                    <button type="submit" class="btn-login">

                        <i class="fa fa-sign-in"></i>

                        Login

                    </button>

                </form>
                <!-- SIGNUP -->

                <div class="signup-link">

                    Don't have an account?

                    <a href="register.php">

                        Create Account

                    </a>

                </div>

            </div>

        </div>

    </div>
    <script>

        if (
            window.location.search.includes('error=')
        ) {

            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );
        }

    </script>
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:24px;overflow:hidden;background:#0f172a;color:#fff;border:1px solid rgba(255,255,255,.08);">
                <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.08);">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-white-50 mb-3">Enter your email and we will send a password reset link.</p>

                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required
                                placeholder="Enter your registered email">
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="forgotSubmitBtn">Send Reset Link</button>
                    </form>

                    <div id="forgotMsg" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('forgotPasswordForm');
            const msg = document.getElementById('forgotMsg');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                msg.innerHTML = '<div class="alert alert-info mb-0">Sending reset link...</div>';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';

                try {
                    const res = await fetch('forgot_password_action.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();

                    if (data.status === 'success') {
                        msg.innerHTML = '<div class="alert alert-success mb-0">Completed successfully. Please check your email.</div>';
                        form.reset();

                        setTimeout(() => {
                            const modalEl = document.getElementById('forgotPasswordModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }, 1500);
                    } else {
                        msg.innerHTML = '<div class="alert alert-danger mb-0">' + data.message + '</div>';
                    }
                } catch (err) {
                    msg.innerHTML = '<div class="alert alert-danger mb-0">Something went wrong. Please try again.</div>';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Reset Link';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>