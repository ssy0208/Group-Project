<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$search = $_GET['search'] ?? '';
$deleteSuccess = false;

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Get user_id before deleting
    $getUser = $conn->prepare("SELECT user_id FROM staff WHERE staff_id = ?");
    $getUser->bind_param("i", $deleteId);
    $getUser->execute();
    $result = $getUser->get_result();
    if ($result->num_rows > 0) {
        $userId = $result->fetch_assoc()['user_id'];

        // Delete staff
        $deleteStaff = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
        $deleteStaff->bind_param("i", $deleteId);
        $deleteStaff->execute();

        // Delete user
        $deleteUser = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $deleteUser->bind_param("i", $userId);
        $deleteUser->execute();

        $deleteSuccess = true;
    }
}

$sql = "SELECT s.*, u.username, u.role FROM staff s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.name LIKE ? AND u.role = 'staff'
        ORDER BY s.name ASC";

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
    <title>Manage Staff</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>

<div class="manage-container">
    <a href="admin_menu.php" class="arrow-button">🡰</a>
    <h2>Manage Staff</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search staff name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍︎</button>
    </form>

    <table>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Identity Card</th>
            <th>Contact Number</th>
            <th>Role</th>
            <th>Action</th>
        </tr>

        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['IC_number'] ?></td>
            <td><?= $row['contact_number'] ?></td>
            <td><?= ucfirst($row['position']) ?></td>
            <td class="action-buttons">
                <a href="edit_staff.php?id=<?= $row['staff_id'] ?>" class="edit-btn"><span class='icon-btn'>✎ Edit</span></a>
                <a href="#" onclick="confirmDelete(<?= $row['staff_id'] ?>)" class="delete-btn"><span class='icon-btn'>🗑 Delete</span></a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<div class="bottom-right">
    <a href="add_staff.php" class="button">✚ Add New Staff</a>
</div>

</div>

<?php if ($deleteSuccess): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Deleted!',
    text: 'Staff deleted successfully.',
    confirmButtonColor: '#d4af37'
}).then(() => {
    window.location.href = 'manage_staff.php';
});
</script>
<?php endif; ?>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this staff?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it.'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'manage_staff.php?delete=' + id;
        }
    });
}
</script>
</body>
</html>
