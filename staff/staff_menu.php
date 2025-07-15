<?php
include_once '../includes/auth.php';
include_once '../config/db.php';

$staffName = $_SESSION['username'] ?? 'Staff';
$showWelcome = $_SESSION['show_welcome'] ?? false;
unset($_SESSION['show_welcome']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
    <meta charset="UTF-8">
    <title>Staff Menu</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Lottie player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>
<header><img src="../images/logo.png" alt="Logo" class="header-logo">Comfort Nursing Care Centre</header>
<div class="container">
    <h2 style="text-align:center;">Staff Dashboard</h2>

    <div class="menu-grid">
        <a href="manage_resident.php" class="menu-card"><span class="icon">🏠︎</span><br>Manage Resident</a>
        <a href="view_schedule.php" class="menu-card"><span class="icon">⏱</span><br>My Schedule</a>
        <a href="log_health.php" class="menu-card"><span class="icon">🗒</span><br>Health Logs</a>
    </div>

    <footer class="logout">
        <a href="#" onclick="confirmLogout()">Logout</a>
    </footer>
</div>

<!-- Logout confirmation -->
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Log Out?',
        text: 'Are you sure you want to log out?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out.',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../logout.php';
        }
    });
}
</script>

<?php if ($showWelcome): ?>
<!-- One-time Welcome animation -->
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
        <p style="margin: 0; font-size: 1.3rem;">You are logged in as <strong><?php echo $staffName; ?></strong>.</p>
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
