<?php
session_start();
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['password'] === $password) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['show_welcome'] = true;

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin/admin_menu.php");
                    break;
                case 'staff':
                    header("Location: staff/staff_menu.php");
                    break;
                case 'it':
                    header("Location: it/it_menu.php");
                    break;
                default:
                    echo "<script>alert('Unrecognized role.'); window.location='index.php';</script>";
            }
            exit();
        }
    }
    echo "<script>alert('Login failed. Please check your username or password.'); window.location='index.php';</script>";
} else {
    header("Location: index.php");
    exit();
}
?>
