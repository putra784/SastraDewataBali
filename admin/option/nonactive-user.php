<?php
session_start();
require '../../src/php/function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../user-handling.php");
    exit;
}

$userId = intval($_GET['id']);

$query = $conn->prepare("UPDATE user SET status = 'non active' WHERE id = ?");
$query->bind_param("i", $userId);
$query->execute();

header("Location: ../user-handling.php?status=updated");
exit;
