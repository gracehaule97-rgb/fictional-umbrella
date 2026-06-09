<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $role = 'user';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            $sql = "INSERT INTO users (name, email, password, role, phone_number) 
                    VALUES ('$name', '$email', '$password', '$role', '$phone')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "✅ Registration Successful! You can now <a href='login.php'>Login here</a>";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Municipal ICT Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Beautiful gradient background */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Card styling */
        .register-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Smooth animation */
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
        
        /* Header styling */
        .card-header-custom {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        
        /* Icon circle */
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
        
        /* Form styling */
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
        
        /* Button styling */
        .btn-register {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(17,153,142,0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        /* Alert styling */
        .alert-custom {
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        /* Link styling */
        .login-link {
            color: #11998e;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card register-card shadow">
                <!-- Beautiful Green Gradient Header -->
                <div class="card-header-custom">
                    <div class="icon-circle">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h4>Create Account</h4>
                    <small>Join the Municipal ICT Helpdesk</small>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-custom">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-custom">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
                        </div>
                        
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
                            <input type="password" name="password" class="form-control" required placeholder="Create a password" minlength="4">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number
                            </label>
                            <input type="tel" name="phone_number" class="form-control" placeholder="Enter your phone number (optional)">
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-register w-100">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small>Already have an account? <a href="login.php" class="login-link">Login here</a></small>
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