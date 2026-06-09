<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Helpdesk Home</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container text-center mt-5">
    <h1>Municipal ICT Helpdesk</h1>
    <p class="mt-3">Welcome👋 JISAJILI UPATE HUDUMA BORA🔥:</p>

    <div class="mt-4">
        <a href="login.php" class="btn btn-primary btn-lg m-2">LOGIN HERE</a>
        <a href="register.php" class="btn btn-success btn-lg m-2">REGISTER HERE</a>
    </div>
</div>

</body>
</html>