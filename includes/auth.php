<?php
session_start();

// Semak login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
