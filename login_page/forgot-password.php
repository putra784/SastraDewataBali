<?php

session_start();
require __DIR__ . "/../src/php/function.php";
require __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"] ?? '';

    $user = query("SELECT * FROM user WHERE username = '$email'");

    if (!$user) {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    } else {

        $otp = rand(100000, 999999);

        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;

        $expired = date("Y-m-d H:i:s", time() + 300);
        $_SESSION['otp_expired'] = $expired;

        query("
            UPDATE user 
            SET reset_token = '$otp',
                reset_expired = '$expired'
            WHERE username = '$email'
        ");

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'putrayasa.id15@gmail.com';
            $mail->Password   = 'ipga gluy xhez tuwc';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('putrayasa.id15@gmail.com', 'Sastra Dewata');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Your OTP Code - Sastra Dewata";
            $mail->Body = "

                <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#ffffff;font-family:Arial, sans-serif;padding:20px;'>
                <tr>
                    <td align='center'>
                    <table width='100%' cellpadding='20' cellspacing='0' style='max-width:500px;background-color:#713f12;border-radius:12px;color:#ffffff;'>
                        <tr>
                        <td align='center' style='border-bottom:2px solid #713f12;'>
                            <h2 style='margin:0;font-size:24px;font-weight:bold;color:#ffffff;'>Password Reset Request</h2>
                        </td>
                        </tr>
                        <tr>
                        <td style='font-size:16px;line-height:1.6;color:#ffffff;border: 2px solid #000000;'>
                            <p>Halo,</p>
                            <p>Gunakan kode OTP berikut untuk mengatur ulang password Anda:</p>

                            <div style='padding:16px;border-radius:8px;text-align:center;margin:20px 0;border'>
                            <span style='font-size:32px;font-weight:bold;color:#ffffff;'>$otp</span>
                            </div>

                            <p style='margin-top:10px;'>Kode ini berlaku selama <b>5 menit</b>.</p>
                        </td>
                        </tr>

                        <tr>
                        <td align='center'>
                            <hr style='border:none;border-top:1px solid #333;margin:16px 0;'>
                            <small style='color:#bfbfbf;'>© Sastra Dewata Password Reset System</small>
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                </table>
            ";

            $mail->send();

            header("Location: verification-otp.php");
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Gagal mengirim email! Error: {$mail->ErrorInfo}');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Sastra Dewata</title>
    <link rel="stylesheet" href="../src/output.css" />
</head>

<body>
    <div
        class="relative flex justify-between m-10 items-center h-[calc(100vh-5rem)] rounded-3xl overflow-hidden">
        <!-- LEFT CONTENT (Forgot Password Version) -->
        <a href="../login_page/login.php"
            class="group absolute top-4 left-6 backdrop-blur-md p-3 rounded-full shadow-lg border border-gray-300 hover:bg-yellow-800 transition-all duration-200 ease-in-out">
            <img src="../assets/icon/arrow-left.svg"
                class="w-5 h-5 transform group-hover:scale-110 transition duration-200"
                alt="back">
        </a>

        <div class="bg-yellow-900 w-1/2 h-full flex justify-center items-center flex-col">
            <div class="w-2/3 text-gray-200">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium tracking-wider leading-7">Forgot Password</h1>
                    <p>No worries, we'll send you a reset instructions.</p>
                </div>

                <form action="" method="post" class="flex flex-col gap-3 mt-6">
                    <div class="flex flex-col">
                        <label for="email">Your Email</label>
                        <input
                            class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                            type="email" name="email" id="email" required />
                    </div>

                    <div class="flex justify-center h-10 bg-yellow-700 rounded-xl hover:bg-yellow-600 hover:text-gray-50 transition-all duration-300 ease">
                        <button class="w-full" type="submit" name="submit">Proceed</button>
                    </div>
                </form>
            </div>

            <!-- Back to Login -->
            <p class="text-gray-200 mt-4">
                <a class="text-blue-400 hover:underline" href="login.php">Back to login</a>
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