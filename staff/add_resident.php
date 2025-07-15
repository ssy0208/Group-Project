<?php
include_once '../includes/auth.php'; 
include('../config/db.php');

$addSuccess = null;

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $ic = $_POST['ic'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $medical = $_POST['medical_history'];
    $allergies = $_POST['allergies'];
    $care_plan = $_POST['care_plan'];

    $sql = "INSERT INTO residents (name, IC_number, age, gender, medical_history, allergies, care_plan)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissss", $name, $ic, $age, $gender, $medical, $allergies, $care_plan);

    $addSuccess = $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <title>Add Resident</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($addSuccess === true): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Resident successfully added!',
    confirmButtonColor: '#d4af37'
}).then(() => {
    window.location = 'manage_resident.php';
});
</script>
<?php elseif ($addSuccess === false): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'Failed to add resident.',
    confirmButtonColor: '#dc3545'
});
</script>
<?php endif; ?>

<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
    <div class="resident-form-container">
        <a href="manage_resident.php" class="arrow-button">🡰</a>
        <h2>Add New Resident</h2>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>IC Number:</label>
            <input type="text" name="ic" required>

            <label>Age:</label>
            <input type="number" name="age">

            <label>Gender:</label>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label>Medical History:</label>
            <input type="text" name="medical_history">

            <label>Allergies:</label>
            <input type="text" name="allergies">

            <label>Care Plan:</label>
            <input type="text" name="care_plan">

            <div class="bottom-right">
                <button type="submit" name="submit">✚ Add Resident</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
