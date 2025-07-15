<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_resident.php");
    exit();
}

$resident_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM residents WHERE resident_id = ?");
$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Resident not found.";
    exit();
}

$data = $result->fetch_assoc();
$updateSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_update'])) {
    $name = $_POST['name'];
    $ic = $_POST['ic'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $medical = $_POST['medical_history'];
    $allergies = $_POST['allergies'];
    $care_plan = $_POST['care_plan'];

    $update = $conn->prepare("UPDATE residents SET name=?, IC_number=?, age=?, gender=?, medical_history=?, allergies=?, care_plan=? WHERE resident_id=?");
    $update->bind_param("ssissssi", $name, $ic, $age, $gender, $medical, $allergies, $care_plan, $resident_id);

    if ($update->execute()) {
        $updateSuccess = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <title>Edit Resident</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
    <div class="staff-form-container">
        <a href="manage_resident.php" class="arrow-button">🡰</a>
        <h2>Edit Resident</h2>
        <form method="POST" id="editForm">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>

            <label>IC Number:</label>
            <input type="text" name="ic" value="<?= $data['IC_number'] ?>" required>

            <label>Age:</label>
            <input type="number" name="age" value="<?= $data['age'] ?>" required>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="male" <?= $data['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $data['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            </select>

            <label>Medical History:</label>
            <textarea name="medical_history"><?= htmlspecialchars($data['medical_history']) ?></textarea>

            <label>Allergies:</label>
            <textarea name="allergies"><?= htmlspecialchars($data['allergies']) ?></textarea>

            <label>Care Plan:</label>
            <textarea name="care_plan"><?= htmlspecialchars($data['care_plan']) ?></textarea>

            <input type="hidden" name="confirm_update" value="1">
            <div class="bottom-right">
                <button type="button" id="confirmUpdateBtn">Update Resident</button>
            </div>
        </form>
    </div>

    <?php if ($updateSuccess): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Resident updated successfully!',
        confirmButtonColor: '#d4af37'
    }).then(() => {
        window.location = 'manage_resident.php';
    });
    </script>
    <?php endif; ?>

    <script>
    document.getElementById("confirmUpdateBtn").addEventListener("click", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to update this resident's information?",
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
