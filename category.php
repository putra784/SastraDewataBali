<?php

session_start();
require 'src/php/function.php';

$currentCategoryRaw = isset($_GET['category']) ? trim($_GET['category']) : '';
$currentCategory = htmlspecialchars($currentCategoryRaw);

$galleryPosts = getGalleryPosts();

$categoryChoose = null;
if ($currentCategoryRaw !== '') {
  $categoryChoose = categoryPage();
}

if (is_object($posts) && $posts instanceof mysqli_result) {
  $posts = $posts->fetch_all(MYSQLI_ASSOC);
}

if ($categoryChoose !== null && is_object($categoryChoose) && $categoryChoose instanceof mysqli_result) {
  $categoryChoose = $categoryChoose->fetch_all(MYSQLI_ASSOC);
}

$category = $_GET['category'] ?? '';
$search   = $_GET['search'] ?? '';

if (!empty($search)) {
  $posts = searchPostsByCategory($search, $category);
} elseif (!empty($category)) {
  $posts = categoryPage();
} else {
  $posts = getGalleryPosts();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sastra Dewata</title>
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

  <section class="pb-20 min-h-screen mx-14">
    <!-- Search -->
    <form method="GET" action="" class="w-full max-w-md flex gap-2 items-center justify-center">
      <input
        type="text"
        name="search"
        placeholder="Search..."
        class="bg-gray-100 w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-yellow-900 focus:shadow-lg transition-all duration-300 hover:bg-white focus:bg-white"
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />

      <!-- Hidden kategori agar tidak hilang -->
      <input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category'] ?? '') ?>">

      <button type="submit">
        <img
          class="bg-gray-100 rounded-lg border border-gray-300 p-2 cursor-pointer hover:bg-white transition-all duration-200 ease-in"
          src="assets/icon/search.svg"
          alt="search-icon" />
      </button>
    </form>

    <!-- Title -->
    <h2 class="text-xl font-semibold text-gray-700 mb-6">
      Category:
      <span class="text-yellow-900">
        <?= $currentCategory !== '' ? ucwords(str_replace('-', ' ', $currentCategory)) : 'Gallery' ?>
      </span>
    </h2>

    <!-- Card Container -->
    <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4">

      <?php if (!empty($posts)) : ?>
        <?php foreach ($posts as $post) : ?>
          <a href="content.php?id=<?= htmlspecialchars($post['id']); ?>"
            class="group bg-white rounded-2xl p-4 flex gap-3 hover:-translate-y-1 border border-gray-300 transition-all duration-300">
            <div class="flex flex-col justify-between w-1/2">
              <span class="bg-yellow-900 px-3 py-1 text-[10px] text-white rounded-full w-fit mb-2">
                <?= htmlspecialchars($post['category']); ?>
              </span>
              <h1 class="font-semibold text-gray-800 text-sm sm:text-base mb-1 line-clamp-2">
                <?= htmlspecialchars($post['title']); ?>
              </h1>
              <p class="text-[11px] sm:text-xs text-gray-500 line-clamp-2">
                <?= htmlspecialchars(substr($post['summary'], 0, 70)); ?>...
              </p>
              <div class="flex items-center gap-2 mt-2">
                <img src="assets/icon/user.svg" class="w-4 opacity-80" />
                <span class="text-[11px] sm:text-xs text-gray-700">
                  <?= htmlspecialchars($post['name'] ?? 'Unknown'); ?>
                </span>
              </div>
            </div>
            <div class="w-1/2 h-32 sm:h-40 lg:h-44 overflow-hidden rounded-xl">
              <img src="assets/image/<?= htmlspecialchars($post['image']); ?>"
                class="w-full h-full object-cover group-hover:scale-105 duration-300"
                alt="<?= htmlspecialchars($post['title']); ?>" />
            </div>
          </a>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="text-center text-gray-400 col-span-full">Tidak ada post ditemukan.</p>
      <?php endif; ?>

    </div>
  </section>

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

  <div
    id="cursor"
    class="pointer-events-none fixed top-0 left-0 w-4 h-4 rounded-full border-2 border-gray-800 z-[9999]"></div>
  <div id="trail"></div>

  <!-- JS -->
  <script src="src/js/script.js"></script>
</body>

</html>