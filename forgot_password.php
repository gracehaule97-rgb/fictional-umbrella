<?php
session_start();
include 'config.php';

$message = '';
$error = '';

if (isset($_POST['send_reset'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Save token to database
        mysqli_query($conn, "UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE email='$email'");
        
        // Create reset link
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
        
        $message = "✅ Password reset link generated! <br><br> 
                    <strong>Click the link below to reset your password:</strong><br>
                    <a href='$reset_link'>$reset_link</a><br><br>
                    <small class='text-muted'>This link expires in 1 hour.</small>";
    } else {
        $error = "❌ Email address not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - ICT Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark text-center">
                    <h4>🔐 Forgot Password?</h4>
                    <small>We'll send you a link to reset your password</small>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="Enter your registered email">
                        </div>
                        <button type="submit" name="send_reset" class="btn btn-warning w-100">Send Reset Link</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php">← Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>