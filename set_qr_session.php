<?php
session_start();

if (!isset($_SESSION['qr_generated']) || $_SESSION['qr_generated'] == false) {
    $_SESSION['qr_generated'] = true;
    echo "success"; 
} else {
    echo "exists"; 
}
?>
