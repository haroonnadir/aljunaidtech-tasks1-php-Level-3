<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required!";
    } else {
        $user_id = $_SESSION['user_id'];
        $status = 0; // Default status is "Pending"

        // Insert blog post into database
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, user_id, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssii", $title, $content, $user_id, $status);
        
        if ($stmt->execute()) {
            $success = "Post submitted successfully. It is now pending approval.";
        } else {
            $error = "Error submitting the post. Please try again.";
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
    <title>Create Post - Blog System</title>
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
        <h1>Create a New Post</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="my_posts.php">My Posts</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Submit Your Blog Post</h2>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="create_post.php" method="POST">
            <label for="title">Post Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="content">Post Content:</label>
            <textarea name="content" id="content" rows="10" required></textarea>

            <button type="submit">Submit Post</button>
        </form>
    </main>

</body>
</html>
