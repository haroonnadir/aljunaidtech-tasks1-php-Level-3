<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = $_GET['id'];
$stmt = $conn->prepare("SELECT title, content, created_at FROM blog_posts WHERE id = ? AND status = 'approved'");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $created_at);
$stmt->fetch();
$stmt->close();

if (!$title) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
    <h1><?php echo htmlspecialchars($title); ?></h1>

</header>

<main>
    <article>
        <p><?php echo nl2br(htmlspecialchars($content)); ?></p>
        <small>Posted on <?php echo $created_at; ?></small>
    </article>
    <p><a href="index.php">&larr; Back to all posts</a></p>
</main>

</body>
</html>