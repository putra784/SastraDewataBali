<?php
require '../src/php/function.php';

if (isset($_POST["submit"])) {
    $register = register_user($_POST);

    if ($register["status"] === true) {
        echo "<script>
                alert('Registrasi berhasil!');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<script>alert('".$register["message"]."');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register | Sastra Dewata</title>
    <link rel="stylesheet" href="../src/output.css" />
  </head>
  <body>
    <div
      class="flex justify-between m-10 items-center h-[calc(100vh-5rem)] rounded-3xl overflow-hidden"
    >
      <!-- LEFT CONTENT -->
      <div class="w-1/2 h-full bg-emerald-200">
        <img
          class="w-full h-full object-cover"
          src="../assets/image/pantai-amed.png"
          alt=""
        />
      </div>

      <!-- RIGHT CONTENT -->
      <div
        class="bg-yellow-900 w-1/2 h-full flex justify-center items-center flex-col"
      >
        <div class="w-2/3 text-gray-200">
          <div class="flex flex-col">
            <h1 class="text-2xl font-medium tracking-wider leading-7">
              Start Exploring Balinese <br />Culture Today
            </h1>

            <p>Fills Your Credential</p>
          </div>

          <form action="" method="post" class="flex flex-col gap-2 mt-4">
            <div class="flex flex-col">
              <label for="username">Your Name</label>
              <input
                class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                type="text"
                name="username"
                id="username"
                placeholder="Your Name"
              />
            </div>

            <div class="flex flex-col">
              <label for="email">Your Email</label>
              <input
                class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                type="email"
                name="email"
                id="email"
                placeholder="Your Email"
              />
            </div>

            <div class="flex flex-col">
              <label for="password">Your Password</label>
              <input
                class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                type="password"
                name="password"
                id="password"
                pattern="^\S+$"
                placeholder="Your Password"
              />
            </div>

            <div class="flex flex-col">
              <label for="password2">Konfirmasi Password </label>
              <input
                class="text-gray-800 h-10 rounded-xl px-3 bg-gray-50 backdrop-blur-md shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 border border-white/20"
                type="password"
                name="password2"
                id="password2"
                placeholder="Konfirm Password"
              />
            </div>

            <div
              class="flex justify-center h-10 mt-3 bg-yellow-700 rounded-xl hover:bg-yellow-600 hover:text-gray-50 transition-all duration-300 ease"
            >
              <button class="w-full" type="submit" name="submit">
                Register
              </button>
            </div>
          </form>
        </div>

        <p class="text-gray-200 mt-2">
          Have an account?
          <a class="text-blue-300 hover:underline" href="login.php"
            >Login Here</a
          >
        </p>
      </div>
    </div>
  </body>
</html>