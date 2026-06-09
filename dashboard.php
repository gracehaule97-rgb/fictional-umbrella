<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login first!";
    exit();
}

// TOTAL TICKETS
$total = mysqli_query($conn, "SELECT COUNT(*) as total FROM tickets");
$total_data = mysqli_fetch_assoc($total);

// PENDING
$pending = mysqli_query($conn, "SELECT COUNT(*) as pending FROM tickets WHERE status='Pending'");
$pending_data = mysqli_fetch_assoc($pending);

// RESOLVED
$resolved = mysqli_query($conn, "SELECT COUNT(*) as resolved FROM tickets WHERE status='Resolved'");
$resolved_data = mysqli_fetch_assoc($resolved);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Helpdesk</a>
    <div>
      <a class="nav-link d-inline text-white" href="dashboard.php">Dashboard</a>
      <a class="nav-link d-inline text-white" href="submit_ticket.php">Submit</a>
      <a class="nav-link d-inline text-white" href="view_tickets.php">Tickets</a>
      <a class="nav-link d-inline text-white me-3" href="profile.php">Profile</a>
      <a class="nav-link d-inline text-white" href="view_tickets.php">my request</a>
    
      <?php if ($_SESSION['role'] == 'admin') { ?>
    <a class="nav-link d-inline text-white" href="admin.php">Admin</a>
<?php } ?>
      <a class="nav-link d-inline text-white" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<hr>


<h2 class="mt-3 text-center">Dashboard</h2>

<div class="container mt-4">
  <div class="row">

    <div class="col-md-4">
      <div class="card bg-secondary text-white mb-3">
        <div class="card-body">
          <h5>Total Tickets</h5>
          <h2><?php echo $total_data['total']; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card bg-warning text-dark mb-3">
        <div class="card-body">
          <h5>Pending</h5>
          <h2><?php echo $pending_data['pending']; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card bg-success text-white mb-3">
        <div class="card-body">
          <h5>Resolved</h5>
          <h2><?php echo $resolved_data['resolved']; ?></h2>
        </div>
      </div>
    </div>

  </div>

</div>

</body>
</html>