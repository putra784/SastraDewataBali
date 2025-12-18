<?php
session_start();
require '../src/php/function.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validasi ID post
if (!isset($_GET['id'])) {
    header("Location: drafts.php");
    exit;
}

$post_id = (int) $_GET['id'];

// Update status post ke pending (hanya milik user tersebut)
$stmt = $conn->prepare("
    UPDATE post 
    SET status = 'pending' 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();

// Redirect ke halaman pending
header("Location: pending.php");
exit;
