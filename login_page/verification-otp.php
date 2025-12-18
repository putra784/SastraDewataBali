<?php
session_start();
require '../src/php/function.php'; // jika Anda punya file koneksi/query

if (isset($_POST['verify'])) {
    $otp = implode('', $_POST['otp']);

    $query = "
        SELECT * FROM user 
        WHERE reset_token = '$otp'
        LIMIT 1
    ";

    $result = query($query);

    if (!empty($result)) {
        $user = $result[0];

        $_SESSION['reset_user_id'] = $user['id'];

        query("UPDATE user SET reset_token=NULL, reset_expired=NULL WHERE id=" . $user['id']);

        header("Location: update-password.php");
        exit;
    } else {
        $error = "Kode OTP salah atau sudah kadaluarsa.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verification Code | Sastra Dewata</title>
    <link rel="stylesheet" href="../src/output.css" />
</head>

<body>
    <div
        class="relative flex justify-between m-10 items-center h-[calc(100vh-5rem)] rounded-3xl overflow-hidden">

        <!-- BACK BUTTON -->
        <a href="forgot-password.php"
            class="group absolute top-4 left-6 backdrop-blur-md p-3 rounded-full shadow-lg border border-gray-300 hover:bg-yellow-800 transition-all duration-200 ease-in-out">
            <img src="../assets/icon/arrow-left.svg"
                class="w-5 h-5 transform group-hover:scale-110 transition duration-200"
                alt="back">
        </a>

        <!-- LEFT CONTENT -->
        <div class="bg-yellow-900 w-1/2 h-full flex justify-center items-center flex-col">
            <div class="w-2/3 text-gray-200">

                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium tracking-wider leading-7">Verification Code</h1>
                    <p>Enter the 6-digit code we've sent to your email.</p>

                    <?php if (isset($error)): ?>
                        <p class="text-red-400 mt-2"><?= $error ?></p>
                    <?php endif; ?>
                </div>

                <form action="" method="post" class="flex flex-col gap-6 mt-6">

                    <!-- OTP BOXES -->
                    <div class="flex justify-between">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <input type="text"
                                maxlength="1"
                                name="otp[]"
                                class="otp-box text-center text-xl w-12 h-12 rounded-xl bg-gray-50 text-gray-900 border border-gray-200 shadow focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                        <?php endfor; ?>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="flex justify-center h-10 bg-yellow-700 rounded-xl hover:bg-yellow-600 hover:text-gray-50 transition-all duration-300 ease cursor-pointer">
                        <button class="w-full" type="submit" name="verify">Verify</button>
                    </div>
                </form>
            </div>

            <!-- CHANGE EMAIL -->
            <p class="text-gray-200 mt-4">
                Didn't get the code?
                <a class="text-blue-400 hover:underline" href="forgot-password.php">Resend</a>
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

    <!-- OTP AUTO-FOCUS SCRIPT -->
    <script>
        const inputs = document.querySelectorAll('.otp-box');

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace" && input.value === "" && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>

</html>