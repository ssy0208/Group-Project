<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$adminName = $_SESSION['username'] ?? 'Admin';
$showWelcome = $_SESSION['show_welcome'] ?? false;
unset($_SESSION['show_welcome']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel='stylesheet' href='../css/style.css'>
    <meta charset="UTF-8">
    <title>Admin Menu</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Lottie player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class='container'>
    <h2 style="text-align:center;">Admin Dashboard</h2>

    <div class="menu-grid">
        <a href="manage_staff.php" class="menu-card"><span class="icon">🖳</span><br>Staff</a>
        <a href="manage_resident.php" class="menu-card"><span class="icon">🏠︎</span><br>Resident</a>
        <a href="manage_shift.php" class="menu-card"><span class="icon">⏱</span><br>Shift</a>
        <a href="manage_logs_health.php" class="menu-card"><span class="icon">🗒</span><br>Health Logs</a>
    </div>

    <footer class="logout">
        <a href="#" onclick="confirmLogout()">Logout</a>
    </footer>
</div>

<!-- Confirmation Logout Script -->
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, logout.'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../logout.php';
        }
    });
}
</script>

<?php if ($showWelcome): ?>
<!-- One-time Welcome Animation -->
<script>
window.addEventListener("load", function () {
  Swal.fire({
    title: '<div style="margin-bottom: 0;">Welcome!</div>',
    html: `
      <div style="display: flex; flex-direction: column; align-items: center; gap: 5px; margin-top: -10px;">
        <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_jbrw3hcz.json"
                       background="transparent" speed="1"
                       style="width: 200px; height: 200px; margin: 0;" autoplay>
        </lottie-player>
        <p style="margin: 0; font-size: 1.3rem;">You are logged in as <strong><?php echo $adminName; ?></strong>.</p>
      </div>
    `,
    showConfirmButton: false,
    timer: 2600
  });
});
</script>
<?php endif; ?>

</body>
</html>
