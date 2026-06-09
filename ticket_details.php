<?php
session_start();
include 'config.php';

// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// 1. Fetch Ticket + User Info (Including Phone)
$sql = "SELECT tickets.*, users.name, users.phone_number 
        FROM tickets 
        JOIN users ON tickets.user_id = users.id 
        WHERE tickets.id = $ticket_id";
$ticket_result = mysqli_query($conn, $sql);
$ticket = mysqli_fetch_assoc($ticket_result);

// If ticket doesn't exist, redirect
if (!$ticket) {
    echo "Ticket not found!";
    exit();
}

// 2. Handle sending a new message
if (isset($_POST['send_message'])) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $insert = "INSERT INTO ticket_comments (ticket_id, user_id, message) VALUES ('$ticket_id', '$user_id', '$msg')";
    mysqli_query($conn, $insert);
    header("Location: ticket_details.php?id=$ticket_id");
    exit();
}

// 3. Fetch all messages
$comments = mysqli_query($conn, "SELECT c.*, u.name FROM ticket_comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = $ticket_id ORDER BY c.created_at ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket #<?php echo $ticket['id']; ?> - Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-container {
            border: 1px solid #ccc;
            height: 400px;
            overflow-y: scroll;
            padding: 15px;
            background: #fff;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .message-left {
            text-align: left;
            margin-bottom: 15px;
        }
        .message-right {
            text-align: right;
            margin-bottom: 15px;
        }
        .message-bubble-left {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 15px;
            background: #f1f1f1;
            max-width: 70%;
        }
        .message-bubble-right {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 15px;
            background: #e1ffc7;
            max-width: 70%;
        }
        .message-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .message-time {
            font-size: 10px;
            color: gray;
            margin-top: 5px;
        }
        .ticket-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .priority-High { color: red; font-weight: bold; }
        .priority-Medium { color: orange; font-weight: bold; }
        .priority-Low { color: green; font-weight: bold; }
    </style>
</head>
<body>

<!-- Navigation Bar (Only Once) -->
<div class="nav" style="background: #f4f4f4; padding: 10px; border-bottom: 2px solid #ccc;">
    <a href="submit_ticket.php">Submit Ticket</a> |
    <a href="view_tickets.php">My Requests</a> |

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

<!-- Ticket Information Section -->
<div class="ticket-info">
    <h2>Ticket #<?php echo $ticket['id']; ?>: <?php echo htmlspecialchars($ticket['title']); ?></h2>
    <p><strong>Reported By:</strong> <?php echo htmlspecialchars($ticket['name']); ?></p>
    <p><strong>Contact Phone:</strong> <span style="color: blue; font-weight: bold;"><?php echo htmlspecialchars($ticket['phone_number']); ?></span></p>
    <p><strong>Priority:</strong> <span class="priority-<?php echo $ticket['priority']; ?>"><?php echo $ticket['priority']; ?></span></p>
    <p><strong>Status:</strong> <?php echo $ticket['status']; ?></p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
</div>

<!-- Chat Section -->
<h3>💬 Conversation</h3>
<div class="chat-container" id="chatBox">
    <?php while($c = mysqli_fetch_assoc($comments)): 
        $is_current_user = ($c['user_id'] == $user_id);
        $side_class = $is_current_user ? "message-right" : "message-left";
        $bubble_class = $is_current_user ? "message-bubble-right" : "message-bubble-left";
    ?>
        <div class="<?php echo $side_class; ?>">
            <div class="<?php echo $bubble_class; ?>">
                <div class="message-name"><?php echo htmlspecialchars($c['name']); ?></div>
                <?php echo nl2br(htmlspecialchars($c['message'])); ?>
                <div class="message-time"><?php echo $c['created_at']; ?></div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Message Form -->
<form method="POST" style="margin-top: 10px;">
    <textarea name="message" style="width: 100%; height: 80px; padding: 10px;" placeholder="Type your message here..." required></textarea>
    <br>
    <button type="submit" name="send_message" style="margin-top: 10px; padding: 10px 20px;">📤 Send Message</button>
</form>

<!-- Auto-scroll to bottom of chat -->
<script>
    var chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>