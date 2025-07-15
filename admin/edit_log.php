<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_logs_health.php");
    exit();
}

$log_id = $_GET['id'];

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM health_logs WHERE log_id = ?");
$stmt->bind_param("i", $log_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Health log not found.";
    exit();
}

$log = $result->fetch_assoc();
$updateSuccess = false;

// Get dropdown options
$residents = $conn->query("SELECT resident_id, name FROM residents ORDER BY name ASC");
$staffs = $conn->query("SELECT staff_id, name FROM staff ORDER BY name ASC");

// Update logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_update'])) {
    $resident_id = $_POST['resident_id'];
    $staff_id = $_POST['staff_id'];
    $date = $_POST['date'];
    $notes = $_POST['notes'] ?? null;
    $treatment = $_POST['treatment_given'] ?? null;

    $update = $conn->prepare("UPDATE health_logs SET resident_id=?, staff_id=?, date=?, notes=?, treatment_given=? WHERE log_id=?");
    $update->bind_param("iisssi", $resident_id, $staff_id, $date, $notes, $treatment, $log_id);

    if ($update->execute()) {
        $updateSuccess = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
    <title>Edit Health Log</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class="container">
    <div class="staff-form-container">
        <a href="manage_logs_health.php" class="arrow-button">🡰</a>
        <h2>Edit Health Log</h2>
        <form method="POST" id="editLogForm">
            <label>Resident:</label>
            <select name="resident_id" required>
                <option value="">-- Select Resident --</option>
                <?php while ($r = $residents->fetch_assoc()): ?>
                    <option value="<?= $r['resident_id'] ?>" <?= $r['resident_id'] == $log['resident_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Staff:</label>
            <select name="staff_id" required>
                <option value="">-- Select Staff --</option>
                <?php while ($s = $staffs->fetch_assoc()): ?>
                    <option value="<?= $s['staff_id'] ?>" <?= $s['staff_id'] == $log['staff_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Date:</label>
            <input type="date" name="date" value="<?= $log['date'] ?>" required>

            <label>Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($log['notes']) ?></textarea>

            <label>Treatment Given:</label>
            <textarea name="treatment_given"><?= htmlspecialchars($log['treatment_given']) ?></textarea>

            <input type="hidden" name="confirm_update" value="1">
            <div class="bottom-right">
                <button type="button" id="confirmUpdateBtn">Update Health Log</button>
            </div>
        </form>
    </div>

    <?php if ($updateSuccess): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Health log updated successfully!',
        confirmButtonColor: '#d4af37'
    }).then(() => {
        window.location = 'manage_logs_health.php';
    });
    </script>
    <?php endif; ?>

    <script>
    document.getElementById("confirmUpdateBtn").addEventListener("click", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to update this health log?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d4af37',
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, update it."
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("editLogForm").submit();
            }
        });
    });
    </script>
</div>
</body>
</html>
