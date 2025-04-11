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

// Fetch post data for the given ID
$stmt = $conn->prepare("SELECT title, content FROM blog_posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $content);
$stmt->fetch();
$stmt->close();

// If post doesn't exist or doesn't belong to the user, redirect to posts page
if (!$title) {
    header("Location: my_posts.php");
    exit();
}

// Handle form submission to update the post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required!";
    } else {
        // Update the blog post in the database
        $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $content, $post_id, $user_id);
        
        if ($stmt->execute()) {
            $success = "Post updated successfully!";
        } else {
            $error = "Error updating the post. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Blog System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Form Styling */
form {
    display: flex;
    flex-direction: column;
    max-width: 700px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

label {
    font-weight: bold;
    margin: 10px 0 5px;
}

input, textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px;
    margin-top: 15px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 18px;
}

button:hover {
    background-color: #0056b3;
}

textarea {
    resize: vertical;
    min-height: 150px;
}

    </style>
</head>
<body>

    <header>
        <h1>Edit Your Post</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="my_posts.php">My Posts</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Edit Post</h2>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="edit_post.php?id=<?php echo $post_id; ?>" method="POST">
            <label for="title">Post Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>

            <label for="content">Post Content:</label>
            <textarea name="content" id="content" rows="10" required><?php echo htmlspecialchars($content); ?></textarea>

            <button type="submit">Update Post</button>
        </form>
    </main>

</body>
</html>
