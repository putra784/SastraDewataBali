<?php
session_start();
require __DIR__ . "/../src/php/function.php";

// CEK APAKAH ADA EMAIL DARI PROSES OTP
if (!isset($_SESSION["reset_email"])) {
    header("Location: forgot-password.php");
    exit;
}

$email = $_SESSION["reset_email"];
$error = "";

// JALANKAN LOGIKA SAAT FORM DIKIRIM
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newPass = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    // CEK PASSWORD SAMA
    if ($newPass !== $confirmPass) {
        $error = "Password tidak sama!";
    } else {

        // HASH PASSWORD
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);

        // UPDATE PASSWORD (MySQLi)
        $stmt = $conn->prepare("
            UPDATE user 
            SET password = ?, reset_token = NULL, reset_expired = NULL 
            WHERE username = ?
        ");
        $stmt->bind_param("ss", $hashed, $email);
        $stmt->execute();

        // HAPUS SESSION
        unset($_SESSION["reset_email"]);

        // REDIRECT KE LOGIN
        header("Location: ../login_page/login.php?reset=success");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password | Sastra Dewata</title>
    <link rel="stylesheet" href="../src/output.css" />
</head>

<body>
    <div class="relative flex justify-between m-10 items-center h-[calc(100vh-5rem)] rounded-3xl overflow-hidden">

        <!-- BACK BUTTON -->
        <a href="verification-otp.php"
            class="group absolute top-4 left-6 backdrop-blur-md p-3 rounded-full shadow-lg border border-gray-300 hover:bg-yellow-800 transition-all duration-200 ease-in-out">
            <img src="../assets/icon/arrow-left.svg"
                class="w-5 h-5 transform group-hover:scale-110 transition duration-200"
                alt="back">
        </a>

        <!-- LEFT CONTENT -->
        <div class="bg-yellow-900 w-1/2 h-full flex justify-center items-center flex-col">
            <div class="w-2/3 text-gray-200">

                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium tracking-wider leading-7">Create New Password</h1>
                    <p>Make sure your new password is strong and easy for you to remember.</p>
                </div>

                <!-- ERROR MESSAGE -->
                <?php if (!empty($error)) : ?>
                    <div class="bg-red-500 text-white px-4 py-2 mb-3 rounded-xl">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <!-- FORM -->
                <form action="" method="post" class="flex flex-col gap-3 mt-6">

                    <div class="flex flex-col">
                        <label for="password">New Password</label>
                        <input
                            class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                            type="password" name="password" id="password" required />
                    </div>

                    <div class="flex flex-col">
                        <label for="confirm_password">Confirm Password</label>
                        <input
                            class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                            type="password" name="confirm_password" id="confirm_password" required />
                    </div>

                    <div class="flex justify-center h-10 bg-yellow-700 rounded-xl hover:bg-yellow-600 hover:text-gray-50 transition-all duration-300 ease">
                        <button class="w-full" type="submit">Save New Password</button>
                    </div>
                </form>
            </div>

            <!-- Back to Login -->
            <p class="text-gray-200 mt-4">
                <a class="text-blue-400 hover:underline" href="../login_page/login.php">Back to login</a>
            </p>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="w-1/2 h-full bg-emerald-200">
            <img
                class="w-full h-full object-cover"
                src="../assets/image/pantai-amed.png"
                alt="" />
        </div>
    </div>
</body>

</html>
