<?php
session_start();
require '../../src/php/function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'], $_GET['status'])) {
    header("Location: ../user-handling.php");
    exit;
}

$userId = intval($_GET['id']);
$status = $_GET['status'];

if (!in_array($status, ['active', 'non_active'])) {
    header("Location: ../user-handling.php");
    exit;
}

// Update status
$query = $conn->prepare("UPDATE user SET status = ? WHERE id = ?");
$query->bind_param("si", $status, $userId);
$query->execute();

// Redirect
header("Location: ../user-handling.php?status=updated");
exit;