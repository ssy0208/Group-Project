<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include('../config/db.php');

$success = null;
$message = '';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $ic = $_POST['ic'];
    $position = $_POST['position'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $ic;
    $role = 'staff';
    $status = 'active';

    $insertUser = "INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)";
    $stmtUser = $conn->prepare($insertUser);
    $stmtUser->bind_param("ssss", $username, $password, $role, $status);

    if ($stmtUser->execute()) {
        $user_id = $conn->insert_id;

        $insertStaff = "INSERT INTO staff (user_id, name, IC_number, position, contact_number, address)
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmtStaff = $conn->prepare($insertStaff);
        $stmtStaff->bind_param("isssss", $user_id, $name, $ic, $position, $contact, $address);

        if ($stmtStaff->execute()) {
            $success = true;
            $message = "Staff successfully added!";
        } else {
            $success = false;
            $message = "Failed to add staff.";
        }
    } else {
        $success = false;
        $message = "Failed to add user. Username might already exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel='stylesheet' href='../css/style.css'>
<title>Add Staff</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
    <div class="staff-form-container">
        <a href="manage_staff.php" class="arrow-button">🡰</a>
        <h2>Add New Staff</h2>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>IC Number:</label>
            <input type="text" name="ic" required>

            <label>Position:</label>
            <select name="position" required>
                <option value="caregiver">Caregiver</option>
                <option value="nurse">Nurse</option>
                <option value="doctor">Doctor</option>
            </select>

            <label>Phone Number:</label>
            <input type="text" name="contact" required>

            <label>Address:</label>
            <input type="text" name="address" required>

            <label>Username:</label>
            <input type="text" name="username" required>

            <button type="submit" name="submit">Add Staff</button>
        </form>
    </div>
</div>

<?php if ($success !== null): ?>
<script>
Swal.fire({
    icon: '<?= $success ? "success" : "error" ?>',
    title: '<?= $success ? "Success!" : "Error!" ?>',
    text: '<?= $message ?>',
    confirmButtonColor: '#d4af37'
}).then(() => {
    <?php if ($success): ?>
        window.location = 'manage_staff.php';
    <?php endif; ?>
});
</script>
<?php endif; ?>
</body>
</html>
