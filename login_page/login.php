<?php

session_start();
require __DIR__ . "/../src/php/function.php";
require __DIR__ . "/../vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("995546464258-pourag7abmebi6np06rn8apefktg6iq8.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/SastraDewata/SastraDewata/login_page/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $login = login_verif($email, $password);

  if ($login["status"] === true) {

    $_SESSION['user_id'] = $login["data"]["id"];
    $_SESSION['email']   = $login["data"]["email"];
    $_SESSION['name']    = $login["data"]["name"];
    $_SESSION['role']    = $login["data"]["role"];

    if ($login["data"]["role"] === "admin") {
      header("Location: ../admin/index.php");
    } else {
      header("Location: ../author/index.php");
    }
    exit;
  } else {

    if (isset($login["message"]) && $login["message"] === "non active") {
      echo "<script>alert('Maaf akun anda sudah non aktif');</script>";
    } else {
      echo "<script>alert('Email atau password salah');</script>";
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
    <!-- LEFT CONTENT -->
    <a href="../index.php"
      class="group absolute top-4 left-6 backdrop-blur-md p-3 rounded-full shadow-lg border border-gray-300 hover:bg-yellow-800 transition-all duration-200 ease-in-out">

      <img src="../assets/icon/arrow-left.svg"
        class="w-5 h-5 transform group-hover:scale-110 transition duration-200"
        alt="back">
    </a>

    <div
      class="bg-yellow-900 w-1/2 h-full flex justify-center items-center flex-col">
      <div class="w-2/3 text-gray-200">
        <div class="flex flex-col">
          <h1 class="text-2xl font-medium tracking-wider leading-7">
            Welcome Back To <br />
            Sastra Dewata
          </h1>

          <p>Login Into Your Account</p>
        </div>

        <form action="" method="post" class="flex flex-col gap-2 mt-4">
          <div class="flex flex-col">
            <label for="email">Your Email</label>
            <input
              class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
              type="email"
              name="email"
              id="email" />
          </div>

          <div class="flex flex-col">
            <label for="password">Your Password</label>
            <input
              class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
              type="password"
              name="password"
              id="password"
              pattern="^\S+$" />
          </div>
          <div class="flex justify-between items-center w-full">
            <!-- checkbox + label -->
            <label for="remember" class="flex items-center gap-2 select-none">
              <span></span>
            </label>

            <!-- link lupa password -->
            <a
              href="forgot-password.php"
              class="text-sm text-blue-400 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-300 rounded">
              Forgot password?
            </a>
          </div>
          <div
            class="flex justify-center h-10 mt-3 bg-yellow-700 rounded-xl hover:bg-yellow-600 hover:text-gray-50 transition-all duration-300 ease">
            <button class="w-full" type="submit" name="submit">Login</button>
          </div>
        </form>
      </div>

      <!-- If don't have acc -->
      <p class="text-gray-200 mt-2">
        Don't have an account?
        <a class="text-blue-400 hover:underline" href="register.php">Sign Up</a>
      </p>

      <a href="<?= $url ?>"
        class="flex items-center justify-center gap-3 mt-2 w-2/3 h-10
          bg-gray-100 rounded-xl border border-gray-300
          hover:bg-gray-200 hover:shadow-md
          transition-all duration-200 ease-in-out">

        <img class="h-4 w-4" src="../assets/icon/google.png" alt="Google" />
        <span class="text-sm font-medium text-gray-700">
          Google
        </span>
      </a>

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