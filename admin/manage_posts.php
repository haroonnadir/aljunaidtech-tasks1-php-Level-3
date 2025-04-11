

<?php
session_start();

// Redirect to login if user is not logged in or if the user is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php';

// Fetch all blog posts from the database
$stmt = $conn->prepare("SELECT id, title, status, created_at, user_id FROM blog_posts ORDER BY created_at DESC");
$stmt->execute();
$stmt->bind_result($post_id, $title, $status, $created_at, $user_id);

$posts = [];
while ($stmt->fetch()) {
    $posts[] = ['id' => $post_id, 'title' => $title, 'status' => $status, 'created_at' => $created_at, 'user_id' => $user_id];
}
$stmt->close();

// Handle post status changes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $post_id = $_POST['post_id'];

    if (in_array($action, ['approve', 'disapprove', 'pending'])) {
        // Update post status
        $stmt = $conn->prepare("UPDATE blog_posts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $post_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'delete') {
        // Delete post
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the manage posts page
    header("Location: manage_posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - Blog System</title>
    <link rel="stylesheet" href="../css/style.css">
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

        button {
            padding: 5px 10px;
            font-size: 14px;
            margin: 0 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        button.approve {
            background-color: #28a745;
            color: white;
        }

        button.disapprove {
            background-color: #dc3545;
            color: white;
        }

        button.pending {
            background-color: #ffc107;
            color: black;
        }

        button.delete {
            background-color: #e74c3c;
            color: white;
        }

        .status-approved {
            color: #28a745;
            font-weight: bold;
        }

        .status-disapproved {
            color: #dc3545;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <h1>Manage Blog Posts</h1>
        <nav>
            <a href="../dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>All Blog Posts</h2>

        <?php if (empty($posts)): ?>
            <p>No blog posts available.</p>
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
                    <?php foreach ($posts as $post): 
                        $status_class = 'status-' . $post['status'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td class="<?php echo $status_class; ?>"><?php echo ucfirst($post['status']); ?></td>
                            <td><?php echo $post['created_at']; ?></td>
                            <td>
                                <form action="manage_posts.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="approve">Approve</button>
                                    <button type="submit" name="action" value="disapprove" class="disapprove">Disapprove</button>
                                    <button type="submit" name="action" value="pending" class="pending">Set Pending</button>
                                    <button type="submit" name="action" value="delete" class="delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

</body>
</html>