<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT staff_id FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    exit("Staff not found.");
}
$staff_id = $result->fetch_assoc()['staff_id'];

$logSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['checked'])) {
    $stmt = $conn->prepare("INSERT INTO health_logs (resident_id, staff_id, date, notes, treatment_given) VALUES (?, ?, NOW(), ?, ?)");
    $notes = "Routine check";
    $treatment = "-";

    foreach ($_POST['checked'] as $resident_id) {
        $stmt->bind_param("iiss", $resident_id, $staff_id, $notes, $treatment);
        $stmt->execute();
    }
    $logSuccess = true;
}

$residents = $conn->query("SELECT resident_id, name FROM residents ORDER BY name ASC");

$logs = $conn->prepare("
    SELECT r.name, h.date, h.notes, h.treatment_given 
    FROM health_logs h 
    JOIN residents r ON h.resident_id = r.resident_id 
    WHERE h.staff_id = ? 
    ORDER BY h.date DESC
");
$logs->bind_param("i", $staff_id);
$logs->execute();
$log_result = $logs->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <title>Log Health</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php if ($logSuccess): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Health logs submitted successfully.',
    confirmButtonColor: '#d4af37'
});
</script>
<?php endif; ?>

<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>

<a href="staff_menu.php" class="arrow-button">🡰</a>

<h2>Log Health Check</h2>

<form method="POST">
    <table>
        <tr>
            <th>No.</th>
            <th>Resident Name</th>
            <th>Checked?</th>
        </tr>
        <?php $no = 1; while ($row = $residents->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><input type="checkbox" name="checked[]" value="<?= $row['resident_id'] ?>"></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <div class="bottom-right">
        <button type="submit">Submit Health Log</button>
    </div>
</form>

<h2>Recent Health Logs</h2>
<?php if ($log_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>No.</th>
            <th>Resident</th>
            <th>Date</th>
            <th>Notes</th>
            <th>Treatment</th>
        </tr>
        <?php $no = 1; while ($log = $log_result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($log['name']) ?></td>
                <td><?= $log['date'] ?></td>
                <td><?= htmlspecialchars($log['notes']) ?></td>
                <td><?= htmlspecialchars($log['treatment_given']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">No logs yet.</p>
<?php endif; ?>

</div>
</body>
</html>
