<?php
include_once '../includes/auth.php'; 
include('../config/db.php');

$success = null;
$message = '';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $ic = $_POST['ic'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $medical = $_POST['medical_history'];
    $allergies = $_POST['allergies'];
    $care_plan = $_POST['care_plan'];
    $assigned_staff = $_POST['assigned_staff'];

    $sql = "INSERT INTO residents (name, IC_number, age, gender, medical_history, allergies, care_plan, assigned_staff_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssi", $name, $ic, $age, $gender, $medical, $allergies, $care_plan, $assigned_staff);

    if ($stmt->execute()) {
        $success = true;
        $message = "Resident successfully added!";
    } else {
        $success = false;
        $message = "Failed to add resident.";
    }
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

            <label>Assigned Staff ID:</label>
            <input type="number" name="assigned_staff">

            <div class="bottom-right">
                <button type="submit" name="submit">✚ Add Resident</button>
            </div>
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
        window.location = 'add_resident.php';
    <?php endif; ?>
});
</script>
<?php endif; ?>

</body>
</html>
