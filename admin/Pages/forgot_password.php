<?php
session_start();
$message = '';
$messageType = 'danger';

if (isset($_GET['error']) && trim($_GET['error']) !== '') {
    $message = $_GET['error'];
    $messageType = 'danger';
}
if (isset($_GET['success']) && trim($_GET['success']) !== '') {
    $message = $_GET['success'];
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quigly | Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{min-height:100vh;font-family:Inter,system-ui,sans-serif;background:#020617;color:#fff}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .cardx{width:100%;max-width:520px;background:#0f172a;border:1px solid rgba(255,255,255,.08);border-radius:30px;box-shadow:0 30px 70px rgba(0,0,0,.45);overflow:hidden}
        .head{padding:34px 30px;text-align:center;background:linear-gradient(135deg,#2563eb,#7c3aed)}
        .icon{width:82px;height:82px;border-radius:24px;margin:0 auto 16px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-size:30px}
        .body{padding:34px}
        .form-control{height:58px;border-radius:16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);color:#fff;padding:0 18px}
        .form-control:focus{background:rgba(255,255,255,.07);color:#fff;box-shadow:0 0 0 4px rgba(99,102,241,.18);border-color:#8b5cf6}
        .btn-auth{height:58px;border:none;border-radius:16px;font-weight:700;background:linear-gradient(135deg,#2563eb,#7c3aed)}
        .small-link{color:#a78bfa;text-decoration:none;font-weight:700}
        .small-link:hover{text-decoration:underline}
        .alert{border-radius:16px}
    </style>
</head>
<body>
<div class="wrap">
    <div class="cardx">
        <div class="head">
            <div class="icon"><i class="fa fa-unlock-alt"></i></div>
            <h2 class="fw-bold mb-2">Forgot Password</h2>
            <p class="mb-0 text-white-50">Enter your email and we will send a secure reset link.</p>
        </div>
        <div class="body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST" action="forgot_password_action.php">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-auth w-100">
                    <i class="fa fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            <div class="text-center mt-4">
                <a href="login.php" class="small-link">Back to login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
