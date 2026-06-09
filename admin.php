<?php
session_start();
include 'config.php';

// 1. SECURITY CHECKS
if (!isset($_SESSION['user_id'])) {
    echo "Please login first!";
    exit();
}
if ($_SESSION['role'] != 'admin') {
    echo "Access Denied! Admins only.";
    exit();
}

// 2. PROCESS UPDATES (Status or Assignment) 
// We do this BEFORE fetching the list so the table shows the NEW data.

// Update Status (Resolved)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "UPDATE tickets SET status='Resolved' WHERE id=$id");
    header("Location: admin.php"); // Refresh to clean the URL
    exit();
}

// Update Assignment
if (isset($_POST['assign_ticket'])) {
    $t_id = $_POST['ticket_id'];
    $admin_id = $_POST['tech_id'];
    
    $assign_query = "UPDATE tickets SET assigned_to = '$admin_id' WHERE id = '$t_id'";
    mysqli_query($conn, $assign_query);
    header("Location: admin.php?msg=Assigned");
    exit();
}

// 3. FETCH DATA FOR THE TABLE
// Fetch all technicians/admins for the dropdown
$tech_result = mysqli_query($conn, "SELECT id, name FROM users WHERE role='admin' OR role='tech'");
$tech_list = [];
while($t = mysqli_fetch_assoc($tech_result)) {
    $tech_list[] = $t;
}

// Fetch all tickets to display
$sql = "SELECT * FROM tickets ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="nav" style="background: #f4f4f4; padding: 10px; border-bottom: 2px solid #ccc;">
    <a href="submit_ticket.php">Submit Ticket</a> |
    <a href="view_tickets.php">My Requests</a> |
    <a href="profile.php">Profile</a> |

    <?php if($_SESSION['role'] == 'admin'): ?>
        <a href="admin.php">Admin Panel</a> |
    <?php endif; ?>

    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'tech'): ?>
        <a href="tech_dashboard.php">My Tasks</a> |
    <?php endif; ?>

    <a href="logout.php">Logout</a>
    <span style="float:right;">Logged in as: <strong><?php echo $_SESSION['user_name']; ?></strong></span>
</div>
<br>
<hr>

<h2>Admin - Manage Tickets</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Priority</th> <th>Status</th>
        <th>Assign Technician</th> <th>Action</th>
    </tr>

 <?php
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['title']."</td>
            <td>".$row['description']."</td>
            <td>".$row['priority']."</td>
            <td>".$row['status']."</td>
            
            <td>
                <form method='POST' style='margin:0; padding:0; background:none; box-shadow:none; width:auto;'>
                    <input type='hidden' name='ticket_id' value='".$row['id']."'>
                    <select name='tech_id' onchange='this.form.submit()'>
                        <option value=''>Assign Tech...</option>";
                        
                        mysqli_data_seek($tech_result, 0); 
                        while($tech = mysqli_fetch_assoc($tech_result)) {
                            $selected = ($row['assigned_to'] == $tech['id']) ? "selected" : "";
                            echo "<option value='".$tech['id']."' $selected>".$tech['name']."</option>";
                        }
                        
    echo "          </select>
                    <input type='hidden' name='assign_ticket' value='1'>
                </form>
            </td>

            <td>
                <a href='ticket_details.php?id=".$row['id']."'>Open Chat</a> | 
                <a href='admin.php?id=".$row['id']."' onclick='return confirm(\"Mark as resolved?\")'>Resolve</a>
            </td>
          </tr>";
}
?>
</table>

</body>
</html>