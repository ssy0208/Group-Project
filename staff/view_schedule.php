<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT staff_id FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Staff record not found for user ID: " . htmlspecialchars($user_id);
    exit;
}

$staff = $result->fetch_assoc();
$staff_id = $staff['staff_id'];

$shiftStmt = $conn->prepare("SELECT date, start_time, end_time, shift_type FROM shifts WHERE staff_id = ? ORDER BY date DESC, start_time DESC");
$shiftStmt->bind_param("i", $staff_id);
$shiftStmt->execute();
$shifts = $shiftStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Schedule</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
<a href="staff_menu.php" class="arrow-button">🡰</a>

<h2>Your Shift Schedule</h2>

<?php if ($shifts->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Shift Type</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = $shifts->fetch_assoc()):
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['start_time']) ?></td>
                <td><?= htmlspecialchars($row['end_time']) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['shift_type'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align: center; color: #999;">No shifts scheduled yet.</p>
<?php endif; ?>

</div>
</body>
</html>
