<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $role);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blog System</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Dashboard Styling */
main {
    width: 50%;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h2 {
    color: #333;
}

p {
    font-size: 18px;
}

    </style>
</head>
<body>

    <header>
        <h1>Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <?php if ($role === 'admin'): ?>
                <a href="admin/manage_posts.php">Manage Posts</a>
                <a href="admin/manage_users.php">Manage Users</a>
            <?php else: ?>
                <a href="my_posts.php">My Posts</a>
                <a href="create_post.php">Create Post</a>
            <?php endif; ?>            
            <a href="Profile.php">Profile Management </a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        
        <?php if ($role === 'admin'): ?>
            <p>You are logged in as <strong>Admin</strong>. You can manage posts and users.</p>
        <?php else: ?>
            <p>You are logged in as a <strong>Registered User</strong>. You can submit blog posts for approval.</p>
        <?php endif; ?>
    </main>

</body>
</html>
