<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$success = false;
$deleted = false;
$message = '';
$search = '';

if (isset($_POST['submit'])) {
    $resident_id = $_POST['resident_id'];
    $staff_id = $_POST['staff_id'];
    $date = $_POST['date'];
    $notes = $_POST['notes'] ?? null;
    $treatment_given = $_POST['treatment_given'] ?? null;

    $stmt = $conn->prepare("INSERT INTO health_logs (resident_id, staff_id, date, notes, treatment_given) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $resident_id, $staff_id, $date, $notes, $treatment_given);

    if ($stmt->execute()) {
        header("Location: manage_logs_health.php?added=1");
        exit();
    }
}

if (isset($_POST['delete_selected']) && !empty($_POST['selected_logs'])) {
    $selectedLogs = $_POST['selected_logs'];
    $placeholders = implode(',', array_fill(0, count($selectedLogs), '?'));
    $types = str_repeat('i', count($selectedLogs));

    $stmt = $conn->prepare("DELETE FROM health_logs WHERE log_id IN ($placeholders)");
    $stmt->bind_param($types, ...$selectedLogs);

    if ($stmt->execute()) {
        header("Location: manage_logs_health.php?deleted=1");
        exit();
    }
}

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$residents = $conn->query("SELECT resident_id, name FROM residents ORDER BY name ASC");
$staffs = $conn->query("SELECT staff_id, name FROM staff ORDER BY name ASC");

$sql = "SELECT hl.log_id, hl.date, hl.notes, hl.treatment_given, r.name AS resident_name, s.name AS staff_name
        FROM health_logs hl
        LEFT JOIN residents r ON hl.resident_id = r.resident_id
        LEFT JOIN staff s ON hl.staff_id = s.staff_id";

$params = [];
$types = '';

if ($search !== '') {
    $sql .= " WHERE r.name LIKE ? OR s.name LIKE ? OR hl.notes LIKE ? OR hl.treatment_given LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam, $searchParam];
    $types = "ssss";
}

$sql .= " ORDER BY hl.date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
    <title>Manage Health Logs</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class="container">
<div class="manage-container">
    <div class="top-bar">
        <a href="admin_menu.php" class="arrow-button">🡰</a>
    </div>

    <h2>Manage Health Logs</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search logs..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍︎</button>
    </form>

    <form method="POST" action="">
        <label>Resident:</label>
        <select name="resident_id" required>
            <option value="">-- Select Resident --</option>
            <?php while ($r = $residents->fetch_assoc()): ?>
                <option value="<?= $r['resident_id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Staff:</label>
        <select name="staff_id" required>
            <option value="">-- Select Staff --</option>
            <?php while ($s = $staffs->fetch_assoc()): ?>
                <option value="<?= $s['staff_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Notes:</label>
        <textarea name="notes" rows="4" cols="50"></textarea>

        <label>Treatment Given:</label>
        <textarea name="treatment_given" rows="4" cols="50"></textarea>

        <div class="bottom-right">
            <button type="submit" name="submit">✚ Add Health Log</button>
        </div>
    </form>

    <form method="POST" action="" id="deleteForm">
        <h3 style="text-align:center;">Existing Health Logs</h3>
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>No.</th>
                    <th>Resident</th>
                    <th>Staff</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th>Treatment Given</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($logs->num_rows > 0): 
                $i = 1;
                while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="selected_logs[]" value="<?= $log['log_id'] ?>"></td>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($log['resident_name']) ?></td>
                    <td><?= htmlspecialchars($log['staff_name']) ?></td>
                    <td><?= $log['date'] ?></td>
                    <td><?= nl2br(htmlspecialchars($log['notes'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($log['treatment_given'])) ?></td>
                    <td>
                        <a href='edit_log.php?id=<?= $log['log_id'] ?>'><span class='icon-btn'>✎</span></a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="8" style="text-align:center;">No health logs found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($logs->num_rows > 0): ?>
        <div class="bottom-right">
            <button type="button" onclick="confirmDelete()">🗑 Delete Selected Logs</button>
            <input type="hidden" name="delete_selected" value="1">
        </div>
        <?php endif; ?>
    </form>
</div>
</div>

<?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Health log added successfully!',
    confirmButtonColor: '#d4af37'
});
</script>
<?php endif; ?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Deleted!',
    text: 'Selected health logs deleted successfully!',
    confirmButtonColor: '#d4af37'
});
</script>
<?php endif; ?>

<script>
function confirmDelete() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete selected logs?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete."
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("deleteForm").submit();
        }
    });
}
</script>
</body>
</html>
