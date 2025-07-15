<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$success = false;
$deleteSuccess = false;

if (isset($_POST['submit'])) {
    $staff_id = $_POST['staff_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $shift_type = $_POST['shift_type'];

    $stmt = $conn->prepare("INSERT INTO shifts (staff_id, date, start_time, end_time, shift_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $staff_id, $date, $start_time, $end_time, $shift_type);

    if ($stmt->execute()) {
        header("Location: manage_shift.php?added=1");
        exit();
    }
}

if (isset($_POST['delete_multiple_btn']) && isset($_POST['selected_shifts'])) {
    $selectedShifts = $_POST['selected_shifts'];
    $placeholders = implode(',', array_fill(0, count($selectedShifts), '?'));
    $types = str_repeat('i', count($selectedShifts));

    $stmt = $conn->prepare("DELETE FROM shifts WHERE shift_id IN ($placeholders)");
    $stmt->bind_param($types, ...$selectedShifts);

    if ($stmt->execute()) {
        header("Location: manage_shift.php?deleted=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <title>Add Shift</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>

<div class="staff-form-container">
    <a href="admin_menu.php" class="arrow-button">🡰</a>
    <h2>Add New Shift</h2>

    <?php if (isset($_GET['added'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Shift added successfully!',
            confirmButtonColor: '#d4af37'
        });
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Selected shifts deleted successfully!',
            confirmButtonColor: '#d4af37'
        });
    </script>
    <?php endif; ?>

    <form method="POST">
        <label>Staff Name:</label>
        <select name="staff_id" required>
            <option value="">-- Select Staff --</option>
            <?php
            $staffResult = $conn->query("SELECT staff_id, name FROM staff ORDER BY name ASC");
            while ($staff = $staffResult->fetch_assoc()) {
                echo "<option value='{$staff['staff_id']}'>" . htmlspecialchars($staff['name']) . "</option>";
            }
            ?>
        </select>

        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Start Time:</label>
        <input type="time" name="start_time" required>

        <label>End Time:</label>
        <input type="time" name="end_time" required>

        <label>Shift Type:</label>
        <select name="shift_type" required>
            <option value="">-- Select Shift --</option>
            <option value="morning">Morning</option>
            <option value="evening">Evening</option>
            <option value="night">Night</option>
        </select>

        <div class="bottom-right">
            <button type="submit" name="submit">✚ Add Shift</button>
        </div>
    </form>

    <h3 style="text-align: center;">All Shifts</h3>

    <form method="POST" id="deleteForm">
    <table style="width:100%; border-collapse: collapse; margin-top: 20px;">
        <tr style="background-color: #007bff; color: white;">
            <th>Select</th>
            <th>No.</th>
            <th>Staff Name</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Type</th>
        </tr>
        <?php
        $getShifts = $conn->query("SELECT s.shift_id, s.date, s.start_time, s.end_time, s.shift_type, st.name 
                                   FROM shifts s 
                                   JOIN staff st ON s.staff_id = st.staff_id 
                                   ORDER BY s.date DESC, s.start_time DESC");
        if ($getShifts->num_rows > 0) {
            $no = 1;
            while ($row = $getShifts->fetch_assoc()) {
                echo "<tr>
                    <td><input type='checkbox' name='selected_shifts[]' value='{$row['shift_id']}'></td>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>{$row['date']}</td>
                    <td>{$row['start_time']}</td>
                    <td>{$row['end_time']}</td>
                    <td>" . ucfirst($row['shift_type']) . "</td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No shifts found.</td></tr>";
        }
        ?>
    </table>

    <?php if ($getShifts->num_rows > 0): ?>
        <div class="bottom-right">
            <button type="button" onclick="confirmDelete()">🗑 Delete Selected Shifts</button>
            <input type="hidden" name="delete_multiple_btn" value="1">
        </div>
    <?php endif; ?>
    </form>
</div>

<script>
function confirmDelete() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete selected shifts?",
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

</div>
</body>
</html>
