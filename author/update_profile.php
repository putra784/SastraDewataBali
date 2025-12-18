<?php

session_start();
require "../src/php/function.php"; // pastikan $conn tersedia (mysqli)

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if (isset($_POST['submit'])) {

    $name = htmlspecialchars(trim($_POST['name']));

    // UPLOAD AVATAR BARU
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {

        $fileName = $_FILES['avatar']['name'];
        $tmpName  = $_FILES['avatar']['tmp_name'];
        $fileSize = $_FILES['avatar']['size'];

        // Ambil ekstensi
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            die("Format gambar tidak valid!");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            die("Ukuran maksimal avatar adalah 2MB!");
        }

        // Nama file unik
        $newFileName = "avatar_" . $userId . "_" . time() . "." . $ext;

        // PATH RELATIF DARI author/update_profile.php
        $uploadDir  = "../assets/avatar/";
        $uploadPath = $uploadDir . $newFileName;

        // Pastikan folder ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Upload file
        if (!move_uploaded_file($tmpName, $uploadPath)) {
            die("Gagal mengupload avatar!");
        }

        // Update database (name + avatar)
        $sql = "UPDATE user SET name = ?, avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $newFileName, $userId);
        $stmt->execute();

    } else {
        // ===============================
        // JIKA TIDAK UPLOAD AVATAR
        // ===============================
        $sql = "UPDATE user SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
    }

    header("Location: profile_author.php?success=1");
    exit;
}

?>