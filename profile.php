<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get current user data
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $update_sql = "UPDATE users SET name='$name', phone_number='$phone' WHERE id='$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['user_name'] = $name;
        $success = " Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = " Error updating profile: " . mysqli_error($conn);
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check_sql = "SELECT * FROM users WHERE id='$user_id' AND password='$current_password'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) {
        $error = " Current password is incorrect!";
    } elseif (strlen($new_password) < 4) {
        $error = " New password must be at least 4 characters!";
    } elseif ($new_password != $confirm_password) {
        $error = " New passwords do not match!";
    } else {
        $update_pass = "UPDATE users SET password='$new_password' WHERE id='$user_id'";
        if (mysqli_query($conn, $update_pass)) {
            $success = " Password changed successfully!";
        } else {
            $error = " Error changing password: " . mysqli_error($conn);
        }
    }
}

// Get user's ticket statistics
$ticket_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved
    FROM tickets WHERE user_id='$user_id'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - ICT Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            border-radius: 15px;
            color: white;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .profile-avatar i {
            font-size: 50px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .info-card h5 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            width: 130px;
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        
        .nav-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .nav-custom a {
            color: #555;
            margin: 0 15px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .nav-custom a:hover {
            color: #667eea;
        }
        
        .nav-custom .active {
            color: #667eea;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">🏛️ Municipal ICT Helpdesk</a>
        <div>
            <a class="nav-link d-inline text-white me-3" href="dashboard.php">Dashboard</a>
            <a class="nav-link d-inline text-white me-3" href="submit_ticket.php">New Ticket</a>
            <a class="nav-link d-inline text-white me-3" href="view_tickets.php">My Tickets</a>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="nav-link d-inline text-white me-3" href="admin.php">Admin Panel</a>
            <?php } ?>
            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'tech') { ?>
                <a class="nav-link d-inline text-white me-3" href="tech_dashboard.php">My Tasks</a>
            <?php } ?>
            <a class="nav-link d-inline text-white me-3 active" href="profile.php">Profile</a>
            <a class="nav-link d-inline text-white" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Profile Header -->
    <div class="profile-header text-center">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
        <p class="mb-0">
            <i class="fas fa-envelope"></i> <?php echo $user['email']; ?> | 
            <i class="fas fa-user-tag"></i> Role: 
            <?php 
                if ($user['role'] == 'admin') echo "<span class='badge bg-danger'>Administrator</span>";
                elseif ($user['role'] == 'tech') echo "<span class='badge bg-info'>Technician</span>";
                else echo "<span class='badge bg-secondary'>User</span>";
            ?>
        </p>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column - Statistics -->
        <div class="col-md-4">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="stat-card">
                        <div class="stat-number text-primary"><?php echo $ticket_stats['total']; ?></div>
                        <div class="stat-label">Total Tickets Submitted</div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="stat-card">
                        <div class="stat-number text-warning"><?php echo $ticket_stats['pending']; ?></div>
                        <div class="stat-label">Open/Pending Tickets</div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="stat-card">
                        <div class="stat-number text-success"><?php echo $ticket_stats['resolved']; ?></div>
                        <div class="stat-label">Resolved Tickets</div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="info-card">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="submit_ticket.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Submit New Ticket
                    </a>
                    <a href="view_tickets.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> View My Tickets
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column - Profile Info & Password Change -->
        <div class="col-md-8">
            <!-- Update Profile Form -->
            <div class="info-card">
                <h5><i class="fas fa-user-edit"></i> Edit Profile Information</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <small class="text-muted">Email cannot be changed. Contact admin for assistance.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" placeholder="Enter your phone number">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-update">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="info-card">
                <h5><i class="fas fa-key"></i> Change Password</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="new_password" class="form-control" required minlength="4">
                        </div>
                        <small class="text-muted">Password must be at least 4 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check"></i></span>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-update">
                        <i class="fas fa-exchange-alt"></i> Change Password
                    </button>
                </form>
            </div>
            
            <!-- Account Information -->
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i> Account Information</h5>
                <div class="info-row">
                    <div class="info-label">Member Since:</div>
                    <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account Type:</div>
                    <div class="info-value">
                        <?php 
                            if ($user['role'] == 'admin') echo "Administrator (Full Access)";
                            elseif ($user['role'] == 'tech') echo "Technician (Can resolve tickets)";
                            else echo "Regular User (Can submit tickets)";
                        ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">User ID:</div>
                    <div class="info-value">#<?php echo $user['id']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>