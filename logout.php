<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
    <!-- SweetAlert2 + Lottie -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        .swal2-popup {
            font-family: "Trebuchet MS", Helvetica, sans-serif !important;
        }
    </style>
</head>
<body>

<script>
window.addEventListener("load", function () {
  Swal.fire({
    title: '<div style="margin-bottom: 0;">Logged Out</div>',
    html: `
      <div style="display: flex; flex-direction: column; align-items: center; gap: 5px; margin-top: -10px;">
        <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_jbrw3hcz.json"
                       background="transparent" speed="1"
                       style="width: 200px; height: 200px; margin: 0;" autoplay>
        </lottie-player>
        <p style="margin: 0; font-size: 1.3rem;">You have logged out successfully.</p>
      </div>
    `,
    showConfirmButton: false,
    timer: 2600
  }).then(() => {
    window.location.href = "index.php"; // redirect after animation
  });
});
</script>

</body>
</html>
