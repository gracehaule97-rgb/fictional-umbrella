<?php
session_start();
include 'config.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$error = '';
$success = '';
$token_valid = false;
$user_id = null;

// Verify token
if ($token) {
    $check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token' AND reset_expires > NOW()");
    
    if (mysqli_num_rows($check) > 0) {
        $token_valid = true;
        $user = mysqli_fetch_assoc($check);
        $user_id = $user['id'];
    } else {
        $error = "❌ Invalid or expired reset link. Please request a new one.";
    }
}

// Process password reset
if (isset($_POST['reset_password']) && $token_valid) {
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    if ($new_password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } elseif (strlen($new_password) < 4) {
        $error = "❌ Password must be at least 4 characters!";
    } else {
        // Update password (plain text for compatibility with your existing system)
        mysqli_query($conn, "UPDATE users SET password='$new_password', reset_token=NULL, reset_expires=NULL WHERE id='$user_id'");
        $success = "✅ Password reset successful! <a href='login.php'>Click here to login</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - ICT Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4>🔄 Reset Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($token_valid && !$success): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required minlength="4">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn btn-success w-100">Reset Password</button>
                        </form>
                    <?php elseif (!$token_valid && !$success): ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $error; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>