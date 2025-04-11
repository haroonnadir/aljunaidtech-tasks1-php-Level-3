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

// Fetch user's blog posts
$stmt = $conn->prepare("SELECT id, title, status, created_at FROM blog_posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($post_id, $title, $status, $created_at);

$posts = [];
while ($stmt->fetch()) {
    $posts[] = ['id' => $post_id, 'title' => $title, 'status' => $status, 'created_at' => $created_at];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts - Blog System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Table Styling */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #007bff;
    color: #fff;
}

td a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
}

td a:hover {
    text-decoration: underline;
    color: #0056b3;
}

    </style>
</head>
<body>

    <header>
        <h1>My Blog Posts</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="create_post.php">Create Post</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Your Blog Posts</h2>

        <?php if (empty($posts)): ?>
            <p>You haven't posted anything yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo ($post['status'] == 1) ? "Approved" : "Pending"; ?></td>
                            <td><?php echo $post['created_at']; ?></td>
                            <td>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a> | 
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

</body>
</html>
