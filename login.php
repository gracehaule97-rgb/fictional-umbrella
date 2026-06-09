<?php
session_start();
include 'config.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Check Remember Me cookie
if (isset($_COOKIE['remember_token'])) {
    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_token']);
    $sql = "SELECT * FROM users WHERE remember_token='$token'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin.php");
        } elseif ($user['role'] == 'tech') {
            header("Location: tech_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            // Handle Remember Me
            if ($remember) {
                $remember_token = bin2hex(random_bytes(32));
                mysqli_query($conn, "UPDATE users SET remember_token='$remember_token' WHERE id='{$user['id']}'");
                setcookie('remember_token', $remember_token, time() + (86400 * 30), "/");
            }
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } elseif ($user['role'] == 'tech') {
                header("Location: tech_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Municipal ICT Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Beautiful gradient background - MATCHES REGISTER PAGE */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Card styling */
        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Smooth animation - MATCHES REGISTER PAGE */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header styling - MATCHES REGISTER PAGE */
        .card-header-custom {
            background: linear-gradient(135deg, #11998e 0%, #e2228c 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .card-header-custom h4 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-header-custom small {
            opacity: 0.9;
            font-size: 14px;
        }
        
        /* Icon circle - MATCHES REGISTER PAGE */
        .icon-circle {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .icon-circle i {
            font-size: 35px;
        }
        
        /* Form styling - MATCHES REGISTER PAGE */
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 3px rgba(17,153,142,0.1);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        /* Checkbox styling */
        .form-check-input:checked {
            background-color: #11998e;
            border-color: #11998e;
        }
        
        /* Button styling - MATCHES REGISTER PAGE */
        .btn-login {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            padding: 12px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(17,153,142,0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Alert styling */
        .alert-custom {
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        /* Link styling - MATCHES REGISTER PAGE */
        .register-link {
            color: #11998e;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link:hover {
            text-decoration: underline;
        }
        
        .forgot-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .forgot-link:hover {
            color: #11998e;
            text-decoration: underline;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider span {
            padding: 0 10px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card shadow">
                <!-- Beautiful Green Gradient Header - MATCHES REGISTER PAGE -->
                <div class="card-header-custom">
                    <div class="icon-circle">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h4>Welcome Back!</h4>
                    <small>Login to Municipal ICT Helpdesk</small>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-custom">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck">
                                <label class="form-check-label" for="rememberCheck">
                                    <i class="fas fa-memory"></i> Remember Me
                                </label>
                            </div>
                            <a href="forgot_password.php" class="forgot-link">
                                <i class="fas fa-question-circle"></i> Forgot Password?
                            </a>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-login w-100">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>New here?</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="register.php" class="register-link">
                            <i class="fas fa-user-plus"></i> Create New Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</body>
</html>