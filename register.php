<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quigly | Create Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
            background: #020617;
            color: #fff;
        }

        .auth-wrapper {
            min-height: 100vh;
        }

        .left-side {
            background:
                linear-gradient(rgba(2, 6, 23, .82), rgba(2, 6, 23, .92)),
                url('https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1600&auto=format&fit=crop');

            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
        }

        .left-content {
            max-width: 520px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 50px;
            background: rgba(255, 255, 255, .08);
            margin-bottom: 40px;
            font-weight: 700;
        }

        .left-content h1 {
            font-size: 4rem;
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 25px;
        }

        .left-content p {
            color: rgba(255, 255, 255, .72);
            line-height: 1.9;
            font-size: 1.05rem;
            margin-bottom: 35px;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }

        .feature i {
            color: #8b5cf6;
        }

        .right-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            background: #020617;
        }

        .auth-card {
            width: 100%;
            max-width: 520px;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(15, 23, 42, .95);
            border: 1px solid rgba(255, 255, 255, .06);
            box-shadow: 0 25px 50px rgba(0, 0, 0, .45);
        }

        .top-box {
            padding: 35px;
            text-align: center;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            margin: auto auto 18px;
            border-radius: 24px;
            background: rgba(255, 255, 255, .16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .card-body {
            padding: 35px !important;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-control {
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            color: #fff;
            padding: 0 18px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, .08);
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, .18);
            color: #fff;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .btn-auth {
            height: 56px;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            transition: .3s;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, #10b981);
        }

        .btn-dark {
            background: linear-gradient(135deg, #111827, #1f2937);
        }

        .loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            width: 45px;
            height: 45px;
            margin: auto auto 10px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, .1);
            border-top: 4px solid #8b5cf6;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        .hidden {
            display: none;
        }

        .alert {
            border-radius: 16px;
        }

        #timer {
            background: #2563eb;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
        }

        .login-link {
            color: #a78bfa;
            text-decoration: none;
            font-weight: 700;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @media(max-width:991px) {

            .left-side {
                display: none;
            }

            .right-side {
                padding: 20px;
            }

            .card-body {
                padding: 25px !important;
            }
        }

        .image-upload-card {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 16px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(37, 99, 235, .16), rgba(124, 58, 237, .16));
            border: 1px solid rgba(139, 92, 246, .35);
            cursor: pointer;
            transition: .3s ease;
        }

        .image-upload-card:hover {
            transform: translateY(-2px);
            border-color: #8b5cf6;
            box-shadow: 0 12px 30px rgba(124, 58, 237, .18);
        }

        .upload-preview-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            position: relative;
        }

        .upload-preview-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .upload-icon {
            font-size: 22px;
            color: #c4b5fd;
            position: absolute;
        }

        .upload-text {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .upload-text strong {
            color: #fff;
            font-size: 14px;
            line-height: 1.2;
        }

        .upload-text span {
            color: #94a3b8;
            font-size: 12px;
        }

        .upload-action {
            padding: 10px 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            flex: 0 0 auto;
        }
    </style>
</head>

<body>

    <div class="container-fluid auth-wrapper">

        <div class="row min-vh-100">

            <div class="col-lg-6 left-side">

                <div class="left-content">

                    <div class="brand">
                        <i class="fa fa-shopping-bag"></i>
                        Quigly
                    </div>

                    <h1>
                        Join The Future
                        Of Smart Shopping
                    </h1>

                    <p>
                        Create your premium Quigly account and unlock faster checkout,
                        secure payments and instant order tracking.
                    </p>

                    <div class="features">

                        <div class="feature">
                            <i class="fa fa-check-circle"></i>
                            Instant Order Tracking
                        </div>

                        <div class="feature">
                            <i class="fa fa-bolt"></i>
                            Faster Checkout Experience
                        </div>

                        <div class="feature">
                            <i class="fa fa-shield"></i>
                            Secure OTP Verification
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-lg-6 right-side">

                <div class="auth-card">

                    <div class="top-box text-white">

                        <div class="icon-box">
                            <i class="fa fa-shopping-cart"></i>
                        </div>

                        <h3 class="fw-bold mb-2">Create Account</h3>

                        <div class="opacity-75">
                            Verify email first, then complete signup
                        </div>

                    </div>

                    <div class="card-body">

                        <div id="msg"></div>

                        <form id="step1Form">

                            <div class="mb-3">

                                <label class="form-label">
                                    <i class="fa fa-user-o me-1"></i>
                                    Full Name
                                </label>

                                <input type="text" id="name" class="form-control" placeholder="Enter your full name"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    <i class="fa fa-envelope-o me-1"></i>
                                    Email Address
                                </label>

                                <input type="email" id="email" class="form-control" placeholder="Enter your email"
                                    required>

                            </div>

                            <button type="button" id="sendOtpBtn" class="btn btn-primary btn-auth w-100">
                                Send OTP
                            </button>

                            <div class="loader" id="loader">

                                <div class="spinner"></div>

                                <p class="text-secondary">
                                    Sending secure OTP...
                                </p>

                            </div>

                        </form>

                        <div id="otpSection" class="hidden mt-4">

                            <div class="alert alert-primary d-flex justify-content-between align-items-center">

                                <div>
                                    <i class="fa fa-paper-plane"></i>
                                    OTP sent successfully
                                </div>

                                <div id="timer">05:00</div>

                            </div>

                            <div class="mb-3 mt-3">

                                <label class="form-label">
                                    Enter OTP
                                </label>

                                <input type="text" id="otp" class="form-control" placeholder="Enter 6 digit OTP">

                            </div>

                            <button type="button" onclick="verifyOTP()" class="btn btn-success btn-auth w-100">

                                Verify Email

                            </button>

                        </div>

                        <form action="admin/actions/signup_action.php" method="POST" enctype="multipart/form-data"
                            id="finalForm" class="hidden mt-4">

                            <input type="hidden" name="name" id="finalName">
                            <input type="hidden" name="email" id="finalEmail">

                            <div class="mb-3">

                                <label class="form-label">
                                    <i class="fa fa-phone"></i>
                                    Phone Number
                                </label>

                                <input type="number" name="number" class="form-control" placeholder="Enter phone number"
                                    required>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fa fa-image"></i>
                                    Profile Image
                                </label>

                                <input type="file" name="image" id="image" accept="image/*" class="d-none"
                                    onchange="previewImage(event)">

                                <label for="image" class="image-upload-card">
                                    <div class="upload-preview-wrap">
                                        <img id="imagePreview" src="" alt="Preview">
                                        <i id="uploadIcon" class="fa fa-image upload-icon"></i>
                                    </div>

                                    <div class="upload-text">
                                        <strong id="uploadTitle">Choose profile image</strong>
                                        <span id="uploadSub">PNG, JPG, WEBP</span>
                                    </div>

                                    <div class="upload-action">Browse</div>
                                </label>
                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    <i class="fa fa-lock"></i>
                                    Password
                                </label>

                                <input type="password" name="password" class="form-control"
                                    placeholder="Create password" required>

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    <i class="fa fa-key"></i>
                                    Confirm Password
                                </label>

                                <input type="password" name="confirm_password" class="form-control"
                                    placeholder="Confirm password" required>

                            </div>

                            <button type="submit" class="btn btn-dark btn-auth w-100">

                                <i class="fa fa-user-plus"></i>
                                Complete Signup

                            </button>

                        </form>

                        <div class="text-center mt-4">

                            Already registered?

                            <a href="login.php" class="login-link">
                                Login
                            </a>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timer = 300;
        let interval;

        const loader = document.getElementById("loader");
        const otpSection = document.getElementById("otpSection");
        const finalForm = document.getElementById("finalForm");

        document.getElementById("sendOtpBtn")
            .addEventListener("click", sendOTP);

        function sendOTP() {

            const name =
                document.getElementById("name").value.trim();

            const email =
                document.getElementById("email").value.trim();

            if (name === "" || email === "") {

                showMessage("Fill all fields", "danger");

                return;
            }

            timer = 300;
            loader.style.display = "block";


            fetch("send_otp.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                credentials: "same-origin",

                body: "email=" + encodeURIComponent(email)

            })

                .then(res => res.text())

                .then(data => {

                    loader.style.display = "none";

                    if (data.trim() === "success") {

                        otpSection.style.display = "block";

                        startTimer();

                        showMessage(
                            "OTP sent successfully",
                            "success"
                        );

                    } else {

                        showMessage(data, "danger");
                    }

                })

                .catch(() => {
                    console.log(encodeURIComponent(email))

                    loader.style.display = "none";

                    showMessage(
                        "Failed to send OTP",
                        "danger"
                    );

                });

        }

        function verifyOTP() {

            const otp =
                document.getElementById("otp").value.trim();

            if (otp === "") {

                showMessage("Enter OTP", "danger");

                return;
            }

            fetch("verify_otp.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                credentials: "same-origin",

                body: "otp=" + encodeURIComponent(otp)

            })

                .then(res => res.text())

                .then(data => {

                    if (data.trim() === "success") {

                        otpSection.style.display = "none";

                        finalForm.style.display = "block";

                        document.getElementById("finalName").value =
                            document.getElementById("name").value;

                        document.getElementById("finalEmail").value =
                            document.getElementById("email").value;

                        clearInterval(interval);

                        showMessage(
                            "Email verified successfully",
                            "success"
                        );

                    } else if (data.trim() === "expired") {

                        showMessage(
                            "OTP expired. Please refresh the page and request a new OTP.",
                            "danger"
                        );

                    } else {

                        showMessage(
                            "Invalid OTP. Please check and try again.",
                            "danger"
                        );

                    }

                });

        }

        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById("imagePreview");
            const icon = document.getElementById("uploadIcon");
            const title = document.getElementById("uploadTitle");
            const sub = document.getElementById("uploadSub");

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = "block";
                icon.style.display = "none";
                title.innerText = file.name;
                sub.innerText = "Selected image";
            }
        }

        function startTimer() {

            interval = setInterval(() => {

                timer--;

                let mins =
                    Math.floor(timer / 60);

                let secs =
                    timer % 60;

                document.getElementById("timer")
                    .innerText =
                    `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

                if (timer <= 0) {

                    clearInterval(interval);

                    document.getElementById("timer")
                        .innerText = "Expired";

                }

            }, 1000);

        }

        function showMessage(message, type) {

            document.getElementById("msg").innerHTML =
                `
    <div class="alert alert-${type}">
        ${message}
    </div>
    `;
        }
    </script>

</body>

</html>