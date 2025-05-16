<?php
session_start();
if (session_destroy()) {
    header('Location: index.php');
} else {
    header('Location: index.php?error=logout_failed');
}
exit;
?>