<?php
session_start();
require '../config.php'; // Ensure this file properly connects to the database

// Redirect to login if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$stmt->bind_result($user_id, $name, $email, $role, $created_at);

$users = [];
while ($stmt->fetch()) {
    $users[] = ['id' => $user_id, 'name' => $name, 'email' => $email, 'role' => $role, 'created_at' => $created_at];
}
$stmt->close();

// Handle user management actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $user_id = intval($_POST['user_id']);

    if ($action == 'change_role' && isset($_POST['new_role'])) {
        $new_role = $_POST['new_role'];
        if (in_array($new_role, ['admin', 'user', 'new_user'])) {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action == 'delete_user') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the manage users page
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Blog System</title>
    <link rel="stylesheet" href="../css/style.css">
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Blog System</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Table Styling */
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    text-align: center;
}

/* header {
    background-color: #007bff;
    color: white;
    padding: 15px;
    text-align: center;
} */

nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 16px;
}

nav a:hover {
    text-decoration: underline;
}

/* Table Styling */
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #ddd;
}

/* Button Styling */
button {
    padding: 8px 12px;
    font-size: 14px;
    margin: 5px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    transition: background 0.3s ease-in-out;
}

button.update-role {
    background-color: #28a745;
    color: white;
}

button.update-role:hover {
    background-color: #218838;
}

button.delete-user {
    background-color: #e74c3c;
    color: white;
}

button.delete-user:hover {
    background-color: #c0392b;
}

/* Select Dropdown */
select {
    padding: 6px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    transition: border 0.3s ease-in-out;
}

select:focus {
    border-color: #007bff;
    outline: none;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    table {
        width: 100%;
    }

    th, td {
        font-size: 14px;
        padding: 10px;
    }

    button, select {
        font-size: 12px;
        padding: 6px;
    }
}

    </style>
</head>
<body>

<header>
    <h1>Manage Users</h1>
    <nav>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>All Users</h2>

    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <form action="manage_users.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="new_role">
                                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    <option value="new_user" <?php echo ($user['role'] == 'new_user') ? 'selected' : ''; ?>>New User</option>
                                </select>
                                <button type="submit" name="action" value="change_role" class="update-role">Update Role</button>
                            </form>
                        </td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td>
                            <form action="manage_users.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="action" value="delete_user" class="delete-user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
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
