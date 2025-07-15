<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$search = $_GET['search'] ?? '';
$deleted = false;

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $delete = $conn->prepare("DELETE FROM residents WHERE resident_id = ?");
    $delete->bind_param("i", $deleteId);
    if ($delete->execute()) {
        $deleted = true;
    }
}

$sql = "SELECT * FROM residents WHERE name LIKE ? ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <title>Manage Resident</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($deleted): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Deleted!',
    text: 'Resident deleted successfully!',
    confirmButtonColor: '#d4af37'
});
</script>
<?php endif; ?>

<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
<div class="manage-container">
    <div class="top-bar">
        <a href="staff_menu.php" class="arrow-button">🡰</a>
    </div>

    <h2>Manage Resident</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search resident name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍︎</button>
    </form>

    <table>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>IC Number</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Action</th>
        </tr>

        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['IC_number'] ?></td>
            <td><?= $row['age'] ?? '-' ?></td>
            <td><?= ucfirst($row['gender']) ?></td>
            <td class="action-buttons">
                <a href="edit_resident.php?id=<?= $row['resident_id'] ?>" class="edit-btn"><span class='icon-btn'>✎ Edit</span></a>
                <a href="#" class="delete-btn" onclick="confirmDelete(<?= $row['resident_id'] ?>)"><span class='icon-btn'>🗑 Delete</span></a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<div class="bottom-right">
    <a href="add_resident.php" class="button">✚ Add New Resident</a>
</div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This resident's record will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it.'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "manage_resident.php?delete=" + id;
        }
    });
}
</script>
</body>
</html>
