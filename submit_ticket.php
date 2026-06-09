<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login first!";
    exit();
}
if (isset($_POST['submit'])) {

$user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $category = $_POST['category'];

    $sql = "INSERT INTO tickets (user_id, title, description, status,priority,category) 
            VALUES ('$user_id', '$title', '$description', 'Pending','$priority','category')";

    if (mysqli_query($conn, $sql)) {
        echo "Ticket submitted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Ticket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div>
    <a href="submit_ticket.php">Submit Ticket</a> |
    <a href="view_tickets.php">View Tickets</a> |
    <a href="profile.php">Profile</a> |
    <?php if ($_SESSION['role'] == 'admin') { ?>
        <a href="admin.php">Admin</a> |
    <?php } ?>
    <a href="logout.php">Logout</a>
</div>
<hr>

<h2>Submit Ticket</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Issue Title" required>
    <textarea name="description" placeholder="Describe your problem" required></textarea>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-control" required>
            <option value="Hardware">💻 Hardware</option>
            <option value="Software">📀 Software</option>
            <option value="Network">🌐 Network</option>
            <option value="Email">📧 Email</option>
            <option value="Printer">🖨️ Printer</option>
            <option value="Other"> Other</option>
        </select>
    </div><br><br>
    
    <select name="priority" class="form-control mb-3">
        <option value="Low">Low Priority</option>
        <option value="Medium" selected>Medium Priority</option>
        <option value="High">High Priority</option>
    </select><br><br>
    
    <button type="submit" name="submit">Submit Ticket</button>
</form>

</body>
</html>