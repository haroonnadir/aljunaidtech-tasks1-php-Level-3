<?php
session_start();
require 'config.php';

// Fetch approved blog posts
$stmt = $conn->prepare("SELECT id, title, content, created_at FROM blog_posts WHERE status = 'approved' ORDER BY created_at DESC");
$stmt->execute();
$stmt->bind_result($post_id, $title, $content, $created_at);

$posts = [];
while ($stmt->fetch()) {
    $posts[] = ['id' => $post_id, 'title' => $title, 'content' => $content, 'created_at' => $created_at];
}
$stmt->close();

// Function to limit content to 100 words
function limit_words($string, $word_limit = 100) {
    $words = explode(' ', $string);
    if (count($words) > $word_limit) {
        return implode(' ', array_slice($words, 0, $word_limit));
    }
    return $string;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Blog Posts</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Latest Blog Posts</h1>
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
</header>

<main>
    <h2>Recent Approved Posts</h2>

    <?php if (empty($posts)): ?>
        <p>No approved blog posts available.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <?php 
                    $content = $post['content'];
                    $word_count = str_word_count($content);
                    $preview = limit_words($content);
                ?>
                <p><?php echo nl2br(htmlspecialchars($preview)); ?>
                <?php if ($word_count > 100): ?>
                    ... <a href="view_post.php?id=<?php echo $post['id']; ?>">Read More</a>
                <?php endif; ?>
                </p>
                <small>Posted on <?php echo $post['created_at']; ?></small>
                <hr>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>