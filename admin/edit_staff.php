<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_staff.php");
    exit();
}

$staff_id = $_GET['id'];

$stmt = $conn->prepare("SELECT s.*, u.username FROM staff s JOIN users u ON s.user_id = u.user_id WHERE s.staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Staff not found.";
    exit();
}

$data = $result->fetch_assoc();

$updateSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_update'])) {
    $name = $_POST['name'];
    $ic = $_POST['ic'];
    $position = $_POST['position'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $update = $conn->prepare("UPDATE staff SET name=?, IC_number=?, position=?, contact_number=?, address=? WHERE staff_id=?");
    $update->bind_param("sssssi", $name, $ic, $position, $contact, $address, $staff_id);

    if ($update->execute()) {
        $updateSuccess = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel='stylesheet' href='../css/style.css'>
    <title>Edit Staff</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
    <div class="staff-form-container">
        <a href="manage_staff.php" class="arrow-button">🡰</a>
        <h2>Edit Staff</h2>
        <form method="POST" id="editForm">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

            <label>IC Number:</label>
            <input type="text" name="ic" value="<?= $data['IC_number'] ?>" required>

            <label>Position:</label>
            <select name="position" required>
                <option value="caregiver" <?= $data['position'] == 'caregiver' ? 'selected' : '' ?>>Caregiver</option>
                <option value="nurse" <?= $data['position'] == 'nurse' ? 'selected' : '' ?>>Nurse</option>
                <option value="doctor" <?= $data['position'] == 'doctor' ? 'selected' : '' ?>>Doctor</option>
            </select>

            <label>Contact Number:</label>
            <input type="text" name="contact" value="<?= $data['contact_number'] ?>">

            <label>Address:</label>
            <input type="text" name="address" value="<?= $data['address'] ?>">

            <input type="hidden" name="confirm_update" value="1">
            <div class="bottom-right">
                <button type="button" id="confirmUpdateBtn">Update Staff</button>
            </div>
        </form>
    </div>

    <?php if ($updateSuccess): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Staff updated successfully!',
        confirmButtonColor: '#d4af37'
    }).then(() => {
        window.location = 'manage_staff.php';
    });
    </script>
    <?php endif; ?>

    <script>
    document.getElementById("confirmUpdateBtn").addEventListener("click", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to update this staff's information?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d4af37",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, update it."
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("editForm").submit();
            }
        });
    });
    </script>
</div>
</body>
</html>
