<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$validToken = false;
$error = '';

if (isset($_GET['error']) && trim($_GET['error']) !== '') {
    $error = $_GET['error'];
}

if ($token === '' || $email === '') {
    $error = 'Invalid or missing reset link.';
} else {
    $tokenHash = hash('sha256', $token);
    $stmt = $conn->prepare("SELECT id, expires_at, used_at FROM password_resets WHERE email = ? AND token_hash = ? LIMIT 1");
    $stmt->bind_param('ss', $email, $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!empty($row['used_at'])) {
            $error = 'This reset link has already been used.';
        } elseif (strtotime($row['expires_at']) < time()) {
            $error = 'This reset link has expired.';
        } else {
            $validToken = true;
        }
    } else {
        $error = 'Invalid reset link.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quigly | Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{min-height:100vh;font-family:Inter,system-ui,sans-serif;background:#020617;color:#fff}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .cardx{width:100%;max-width:520px;background:#0f172a;border:1px solid rgba(255,255,255,.08);border-radius:30px;box-shadow:0 30px 70px rgba(0,0,0,.45);overflow:hidden}
        .head{padding:34px 30px;text-align:center;background:linear-gradient(135deg,#059669,#10b981)}
        .icon{width:82px;height:82px;border-radius:24px;margin:0 auto 16px;background:rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-size:30px}
        .body{padding:34px}
        .form-control{height:58px;border-radius:16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);color:#fff;padding:0 18px}
        .form-control:focus{background:rgba(255,255,255,.07);color:#fff;box-shadow:0 0 0 4px rgba(16,185,129,.18);border-color:#10b981}
        .btn-auth{height:58px;border:none;border-radius:16px;font-weight:700;background:linear-gradient(135deg,#059669,#10b981)}
        .small-link{color:#a78bfa;text-decoration:none;font-weight:700}
        .small-link:hover{text-decoration:underline}
        .alert{border-radius:16px}
    </style>
</head>
<body>
<div class="wrap">
    <div class="cardx">
        <div class="head">
            <div class="icon"><i class="fa fa-key"></i></div>
            <h2 class="fw-bold mb-2">Reset Your Password</h2>
            <p class="mb-0 text-white-50">Create a new secure password for your account.</p>
        </div>
        <div class="body">
            <?php if (!$validToken): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <div class="text-center mt-3"><a href="forgot_password.php" class="small-link">Request a new link</a></div>
            <?php else: ?>
                <form method="POST" action="reset_password_action.php">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-auth w-100">
                        <i class="fa fa-refresh"></i> Update Password
                    </button>
                </form>
            <?php endif; ?>
            <div class="text-center mt-4">
                <a href="login.php" class="small-link">Back to login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
