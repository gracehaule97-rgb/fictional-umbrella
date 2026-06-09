<?php
session_start();
include 'config.php';

// 1. Security: Only let Techs or Admins in
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'tech' && $_SESSION['role'] != 'admin')) {
    echo "Access Denied!";
    exit();
}

$my_id = $_SESSION['user_id'];

// 2. Fetch ONLY tickets assigned to THIS technician
$sql = "SELECT * FROM tickets WHERE assigned_to = '$my_id' AND status != 'Resolved' ORDER BY priority DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="nav">
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
    </div>
    <hr>

    <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
    <h3>Your Assigned Tasks</h3>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Issue</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php 
        // Fixed the "double while" error here
        while ($row = mysqli_fetch_assoc($result)) { 
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><span class="priority-<?php echo $row['priority']; ?>"><?php echo $row['priority']; ?></span></td>
            <td class="status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
            <td>
                <a href="ticket_details.php?id=<?php echo $row['id']; ?>">View & Chat</a>
            </td>
        </tr>
        <?php } ?>

        <?php if(mysqli_num_rows($result) == 0): ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:10px;">You have no active tasks!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>