<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $created_at);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Sanitize the email input
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format!";
        header("Location: Profile.php");
        exit();
    }

    if (!empty($new_name) && !empty($new_email)) {
        if ($new_password) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_name, $new_email, $new_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
        }
        $stmt->execute();
        $stmt->close();
        $_SESSION['name'] = $new_name;
        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: Profile.php");
        exit();
    } else {
        $_SESSION['message'] = "Please fill in all fields!";
        header("Location: Profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <style>
/* General Body and Page Setup */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Header */
header {
    background-color: #333;
    color: #fff;
    padding: 15px;
    text-align: center;
}

nav {
        display: flex;
        justify-content: center;
        padding: 10px;
}

nav a {
    color: #fff;
    text-decoration: none;
    margin: 0 10px;
}

nav a:hover {
    text-decoration: underline;
}

/* Profile Page Heading */
h1 {
    color: while;
    text-align: center;
    margin-top: 20px;
}

/* Profile Form */
form {
    background: #fff;
    max-width: 400px;
    margin: 20px auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

/* Form Input Fields */
input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

/* Submit Button */
button {
    background-color: #28a745;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: 0.3s;
}

button:hover {
    background-color: #218838;
}

/* Paragraph and Link Styling */
p {
    text-align: center;
    font-size: 16px;
    margin-top: 10px;
}

a {
    display: block;
    text-align: center;
    color: #007bff;
    text-decoration: none;
    font-size: 16px;
    margin-top: 15px;
}

a:hover {
    text-decoration: underline;
}

/* Mobile Responsiveness */
@media (max-width: 500px) {
    form {
        width: 90%;
    }
}

    </style>
</head>
<body>

<header>
    <h1>Dashboard</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin/manage_posts.php">Manage Posts</a>
            <a href="admin/manage_users.php">Manage Users</a>
        <?php else: ?>
            <a href="my_posts.php">My Posts</a>
            <a href="create_post.php">Create Post</a>
        <?php endif; ?>            
        <a href="Profile.php">Profile Management</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<h1>Profile</h1>

<?php if (isset($_SESSION['message'])): ?>
    <p style="color: green; font-weight: bold;"> <?php echo $_SESSION['message']; unset($_SESSION['message']); ?> </p>
<?php endif; ?>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

    <label>New Password (optional):</label>
    <input type="password" name="password"><br>

    <button type="submit">Update Profile</button>
</form>

<p>Member since: <?php echo $created_at; ?></p>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
