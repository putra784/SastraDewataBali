<?php
session_start();
require '../src/php/function.php';

// Pastikan user login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_page/login.php");
    exit;
}

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header("Location: drafts.php");
    exit;
}

$post_id = $_GET['id'];

// Hapus berdasarkan id dan user_id (supaya aman)
$result = deletePostById($post_id, $_SESSION["user_id"]);

if ($result) {
    echo "<script>
        alert('Post berhasil dihapus');
        window.location='index.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus Post');
        window.location='index.php';
    </script>";
}
?>
