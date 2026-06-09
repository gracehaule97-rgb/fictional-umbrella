<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login first!";
    exit();
}

$user_id = $_SESSION['user_id'];

// --- SEARCH & FILTER LOGIC ---
$status_filter = ""; 
$search_query = "";
$search = "";

// Check for Status Filter
if (isset($_GET['status']) && $_GET['status'] != 'All') {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $status_filter = " AND status='$status'";
}

// Check for Search Keyword
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    // This searches for the keyword anywhere in the Title or Description
    $search_query = " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

// Final Combined SQL
$sql = "SELECT * FROM tickets WHERE user_id='$user_id' $status_filter $search_query ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Tickets</title>
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

<h2>All Tickets</h2>

<p>
    Filter: 
    <a href="view_tickets.php?status=All">All</a> | 
    <a href="view_tickets.php?status=Pending">Pending</a> | 
    <a href="view_tickets.php?status=Resolved">Resolved</a>
</p>

<form method="GET" action="view_tickets.php">
    <input type="text" name="search" placeholder="Search title..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Status</th>
        <th>Date</th>
        <th>Category</th>
    </tr>

    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<td><a href='ticket_details.php?id=".$row['id']."'>Chat/View</a></td>";
        echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['title']."</td>
                <td>".$row['description']."</td>
                <td>".$row['status']."</td>
                <td>".$row['created_at']."</td>
                <td><".$row['category']."</td>
              </tr>";
    }
    ?>

</table>

</body>
</html>