<?php
session_start();
require 'src/php/function.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About | Sastra Dewata</title>
  <link href="src/output.css" rel="stylesheet" />
</head>

<body>
  <!-- Header -->
  <header class="fixed top-0 left-0 w-full bg-yellow-900/90 backdrop-blur-md text-white shadow-md z-50">
    <nav class="bg-yellow-900 shadow-md">
      <div class="max-w-7xl mx-10 px-6">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="about.php">
              <img
                src="assets/icon/logo.png"
                class="w-14 h-14 shadow-lg rounded-full cursor-pointer"
                alt="Logo" />
            </a>
          </div>

          <!-- Menu Desktop -->
          <div class="hidden md:flex space-x-8 items-center">
            <a
              href="index.php"
              class="text-white relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-gray-100 after:transition-all hover:after:w-full">Home</a>
            <a
              href="about.php"
              class="text-white relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-gray-100 after:transition-all hover:after:w-full">About</a>

            <!-- Dropdown Category -->
            <div id="categoryWrapper" class="relative cursor-pointer">

              <!-- Button -->
              <div class="flex items-center gap-1">
                <span
                  id="categoryBtn"
                  class="text-white cursor-pointer relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-gray-100 after:transition-all hover:after:w-full">
                  Category
                </span>
                <img
                  id="drop-icon"
                  class="h-4 translate-y-[2px] transform transition-transform duration-300"
                  src="assets/icon/chevron-down.svg"
                  alt="drop-icon" />
              </div>

              <!-- Menu -->
              <div
                id="categoryMenu"
                class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded-md hidden">

                <a
                  href="category.php?category=Balinese Nature"
                  class="text-gray-800 block px-4 py-2 hover:bg-gray-100 hover:rounded-t-md">
                  Balinese Nature
                </a>

                <a
                  href="category.php?category=Tradition"
                  class="text-gray-800 block px-4 py-2 hover:bg-gray-100">
                  Tradition
                </a>

                <a
                  href="category.php?category=Palm Leaf"
                  class="text-gray-800 block px-4 py-2 hover:bg-gray-100 hover:rounded-b-md">
                  Palm Leaf
                </a>

              </div>
            </div>

            <a
              href="contact.php"
              class="text-white relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-gray-100 after:transition-all hover:after:w-full">Contact</a>

            <!-- isLogin? -->
            <?php if (isset($_SESSION["user_id"])): ?>
              <?php
              // Ambil role user (misalnya dari session)
              $role = $_SESSION["role"] ?? 'author'; // default jika tidak ada
              ?>

              <!-- Dropdown Menu Wrapper -->
              <div class="relative">
                <!-- User Profile Button -->
                <button id="dropdownButton" class="p-2 bg-white text-yellow-900 rounded-full font-medium">
                  <img src="assets/icon/user.svg" alt="">
                </button>

                <!-- Dropdown Content -->
                <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg overflow-hidden">

                  <!-- Dashboard -->
                  <a
                    href="<?= ($role === 'admin') ? 'admin/index.php' : 'author/index.php' ?>"
                    class="block px-4 py-2 hover:bg-gray-100 text-yellow-900">
                    Dashboard
                  </a>

                  <!-- Profile -->
                  <a
                    href="<?= ($role === 'admin') ? 'admin/profile.php' : 'author/profile.php' ?>"
                    class="block px-4 py-2 hover:bg-gray-100 text-yellow-900">
                    Profile
                  </a>

                  <!-- Logout -->
                  <form action="src/php/logout.php" method="POST">
                    <button
                      type="submit"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                      <img
                        src="assets/icon/log-out.svg"
                        class="w-4 opacity-70" />
                      Logout
                    </button>
                  </form>
                </div>
              </div>

            <?php else: ?>
              <a
                href="login_page/login.php"
                class="px-4 py-1 bg-white text-yellow-900 rounded-lg hover:bg-gray-100 font-medium">
                Login
              </a>
            <?php endif; ?>

          </div>

          <!-- Mobile toggle -->
          <div class="md:hidden">
            <button id="menuBtn" class="text-3xl">&#9776;</button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div id="mobileMenu" class="hidden md:hidden px-6 pb-4 space-y-2">
        <a href="index.php" class="block text-gray-700 hover:text-blue-600">Home</a>
        <a href="about.php" class="block text-gray-700 hover:text-blue-600">About</a>

        <!-- Mobile Category Dropdown -->
        <details class="cursor-pointer">
          <summary class="text-gray-700 hover:text-blue-600">
            Category
          </summary>
          <div class="pl-4 mt-2 space-y-2">
            <a href="#" class="block text-gray-600 hover:text-blue-600">Alam Bali</a>
            <a href="#" class="block text-gray-600 hover:text-blue-600">Tradisi</a>
            <a href="#" class="block text-gray-600 hover:text-blue-600">Lontar</a>
          </div>
        </details>

        <a href="contact.php" class="block text-gray-700 hover:text-blue-600">Contact</a>

        <a
          href="login_page/login.php"
          class="block w-fit px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Login</a>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section>
    <!-- Vision Mission -->
    <div class="flex items-center flex-col px-14 mt-6 h-auto">
      <h1 class="text-center text-6xl font-bold leading-tight">
        Empowering Culture, the <br />
        Modern Way.
      </h1>
      <p class="text-xl mt-6">
        We turn culture sharing the fun way, helping culture preservation and
        <br />
        inspiring more people to appreciate the beauty of Balinese traditions.
      </p>

      <div class="flex flex-col items-center justify-center mt-6">
        <img class="w-2/3" src="assets/image/tari-bali.png" alt="" />
        <div class="flex w-2/3 gap-4 mt-4">
          <div>
            <img src="assets/image/ogoh-ogoh.png" alt="" />
            <h1 class="text-4xl font-bold">Vision</h1>
            <p class="mt-2">
              Changing the way culture is shared, making it fun, engaging, and
              meaninful.
            </p>
          </div>

          <div>
            <img src="assets/image/gwk.png" alt="" />
            <h1 class="text-4xl font-bold">Mission</h1>
            <p class="mt-2">
              Preserving balinese culture in the midst of globalization.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Foundation -->
    <div class="flex flex-col mt-10 items-center h-auto px-14 bg-yellow-900">
      <h1 class="text-white text-center font-medium text-3xl mt-6">
        The Foundation of Our Success Driving <br />
        innovation & Awareness
      </h1>
      <div class="flex w-2/3 my-4 gap-4">
        <div class="w-1/2">
          <img
            class="h-full rounded-3xl"
            src="assets/image/music.png"
            alt="" />
        </div>
        <div class="w-1/2">
          <img class="h-full" src="assets/image/art.png" alt="" />
        </div>
      </div>
      <div class="w-2/3 mb-8">
        <img src="assets/image/heritage.png" alt="" />
      </div>
    </div>

    <!-- Dream Team -->
    <div class="mt-10 w-full flex flex-col items-center">
      <h1 class="text-center text-4xl font-bold leading-tight tracking-wider">
        Meet The Dream Team!
      </h1>
      <p class="text-lg mt-4 tracking-widest">
        “We keep traditions alive by living them, loving them, and sharing
        them”
      </p>
      <div class="flex gap-4 w-2/3 my-4">
        <div class="w-[30%]">
          <img src="assets/image/arya.png" alt="" />
        </div>
        <div class="w-[30%]">
          <img src="assets/image/putra.png" alt="" />
        </div>
        <div class="w-[30%]">
          <img src="assets/image/abrar.png" alt="" />
        </div>
      </div>
      <p>
        Bali ipsum budaya amet, tradisi spiritus hidup dalam setiap tarian dan
        <br />
        gamelan. We share culture with joy, preserving heritage through every
        <br />
        story told. Niskala lorem harmony antara alam dan manusia, connecting
        <br />
        generations through art and devotion.
      </p>
    </div>
  </section>

  <!-- Footer -->
  <footer
    class="mt-12 w-full flex flex-col bg-yellow-900 h-72 justify-between pb-6">
    <div class="flex justify-between mx-14 items-center mt-10">
      <!-- Left Footer -->
      <div class="flex flex-col text-white">
        <a
          class="flex mr-auto underline text-2xl hover:text-gray-200 transition-all duration-200"
          href="#">Sastra Dewata</a>
        <p class="mt-4">230 Jalan Sudirman, Denpasar QJR (BALI) H8721R</p>
        <div class="flex mt-4 gap-4">
          <div>
            <p>Phone Number</p>
            <a class="hover:underline" href="#">+62 0895-2389-2321</a>
          </div>
          <div>
            <p>Email</p>
            <a class="hover:underline" href="#">teamprogneta@gmail.com</a>
          </div>
        </div>
        <div></div>
      </div>

      <!-- Right Footer -->
      <div class="flex text-white justify-between w-1/2">
        <div class="flex flex-col">
          <a
            class="font-normal text-lg underline hover:text-gray-200"
            href="#">Navigate</a>
          <a href="#">Homepage</a>
          <a href="#">Categories</a>
          <a href="#">Contact</a>
          <a href="#">FAQ</a>
        </div>

        <div class="flex flex-col">
          <a
            class="font-normal text-lg underline hover:text-gray-200"
            href="#">Navigate</a>
          <a href="#">Homepage</a>
          <a href="#">Categories</a>
          <a href="#">Contact</a>
          <a href="#">FAQ</a>
        </div>

        <div class="flex flex-col">
          <a
            class="font-normal text-lg underline hover:text-gray-200"
            href="#">Legal</a>
          <a href="#">Term of Services</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Cookies</a>
        </div>
      </div>
    </div>

    <!-- Line Footer -->
    <div class="h-0.5 bg-white w-auto mx-14"></div>

    <!-- Tag Footer -->
    <div class="flex items-center w-full justify-center">
      <h2 class="font-normal text-lg text-white">
        @ 2025 Sastra Dewata. All Rights reserved
      </h2>
    </div>
  </footer>

  <div id="cursor" class="pointer-events-none fixed top-0 left-0 w-4 h-4 rounded-full border-2 border-gray-800 z-[9999]"></div>
  <div id="trail"></div>

  <script src="src/js/script.js"></script>
</body>

</html>