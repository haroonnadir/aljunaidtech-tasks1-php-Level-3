<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Get user ID
$user_id = $_SESSION['user_id'];

// Check if post ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my_posts.php");
    exit();
}

$post_id = $_GET['id'];

// Fetch the post to check if it belongs to the logged-in user
$stmt = $conn->prepare("SELECT user_id FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($post_user_id);
$stmt->fetch();
$stmt->close();

// Check if the post exists and if the logged-in user is the owner
if (!$post_user_id || $post_user_id != $user_id) {
    header("Location: my_posts.php");
    exit();
}

// Handle the deletion process
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Delete the blog post from the database
    $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: my_posts.php?message=Post deleted successfully.");
        exit();
    } else {
        $stmt->close();
        header("Location: my_posts.php?error=Error deleting post.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post - Blog System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Delete confirmation form styling */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

button {
    background-color: #e74c3c;
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 18px;
}

button:hover {
    background-color: #c0392b;
}

.cancel-btn {
    margin-top: 10px;
    padding: 10px;
    color: #007bff;
    text-decoration: none;
}

.cancel-btn:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

    <header>
        <h1>Delete Post</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="my_posts.php">My Posts</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Are you sure you want to delete this post?</h2>

        <p>This action is permanent and cannot be undone.</p>

        <form action="delete_post.php?id=<?php echo $_GET['id']; ?>" method="GET">
            <button type="submit">Yes, delete</button>
            <a href="my_posts.php" class="cancel-btn">Cancel</a>
        </form>
    </main>

</body>
</html>
