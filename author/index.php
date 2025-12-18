<?php

session_start();
require '../src/php/function.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_page/login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "
    SELECT 
        COUNT(CASE WHEN status = 'published' THEN 1 END) AS published,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending,
        COUNT(CASE WHEN status = 'draft' THEN 1 END) AS draft,
        COUNT(CASE WHEN status = 'scheduled' THEN 1 END) AS scheduled
    FROM `post`
    WHERE user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$countPost = $stmt->get_result()->fetch_assoc();

/* Fallback jika null */
$published = $countPost['published'] ?? 0;
$pending   = $countPost['pending'] ?? 0;
$draft     = $countPost['draft'] ?? 0;
$scheduled = $countPost['scheduled'] ?? 0;

$stmtUser = $conn->prepare("SELECT name, avatar FROM user WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="../src/output.css" />
  <style>
    .dropdown-enter {
      opacity: 0;
      transform: translateY(-6px);
    }

    .dropdown-enter-active {
      opacity: 1;
      transform: translateY(0);
      transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .dropdown-exit {
      opacity: 1;
      transform: translateY(0);
    }

    .dropdown-exit-active {
      opacity: 0;
      transform: translateY(-6px);
      transition: opacity 0.2s ease, transform 0.2s ease;
    }

    /* Sidebar collapsed */
    .sidebar-collapsed {
      width: 80px !important;
      padding: 20px 12px;
    }

    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .navigation-title {
      display: none;
    }

    .sidebar-collapsed .logo-text {
      display: none;
    }

    .sidebar-collapsed .logo-img {
      width: 45px;
    }

    .sidebar-collapsed .navigation-title {
      display: none;
    }

    .sidebar-collapsed #arrowIcon {
      display: none;
    }

    .rotate-180 {
      transform: rotate(180deg);
    }
  </style>
</head>

<body>
  <!-- Wrapper -->
  <div class="flex w-full h-screen bg-gray-200">
    <!-- SIDEBAR -->
    <div id="sidebar" class="w-[260px] bg-[#4e342e] text-white flex flex-col p-6 transition-all duration-300">

      <!-- Logo -->
      <div class="flex flex-col items-center gap-2 mb-10">
        <img src="../assets/icon/logo.png" class="w-28 logo-img" />
        <p class="text-lg tracking-widest font-semibold logo-text">SASTRA DEWATA</p>
      </div>

      <!-- Navigation Title -->
      <p class="uppercase text-sm tracking-wider mb-4 navigation-title">Main Navigation</p>

      <!-- Home -->
      <a href="index.php" class="flex items-center gap-3 bg-gray-300 text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
        <img src="../assets/icon/home.svg" class="w-5" />
        <span class="text-sm sidebar-text">Home</span>
      </a>


      <!-- Add Post -->
      <a
        href="add-post.php"
        class="flex items-center gap-3 bg-white text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
        <img src="../assets/icon/plus-square.svg" class="w-5" />
        <span class="text-sm sidebar-text">Add Post</span>
      </a>

      <!-- Post List -->
      <div class="mb-3">
        <button
          id="postDropdownBtn"
          class="w-full flex items-center justify-between bg-white text-black px-4 py-2 rounded-md hover:bg-gray-300 transition">
          <div class="flex items-center gap-3">
            <img src="../assets/icon/file-plus.svg" class="w-5" />
            <span class="text-sm sidebar-text">Posts</span>
          </div>

          <!-- Arrow icon -->
          <img
            id="arrowIcon"
            src="../assets/icon/chevron-down-black.svg"
            class="w-4 transition-transform" />
        </button>

        <!-- Dropdown Menu -->
        <div
          id="postDropdown"
          class="hidden flex flex-col mt-2 bg-gray-100 rounded-md overflow-hidden text-black">
          <a
            href="drafts.php"
            class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Drafts</a>
          <a
            href="uploaded-post.php"
            class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Uploaded Posts</a>
          <a
            href="pending.php"
            class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Pending Posts</a>
          <a href="schedule.php" class="px-4 py-2 text-sm hover:bg-gray-300">Schedule Posts</a>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">
      <!-- TOP BAR -->
      <div
        class="w-full flex justify-between items-center px-6 py-4 bg-white shadow relative">
        <!-- Left buttons -->
        <div class="flex items-center gap-4">
          <button id="toggleSidebar" class="p-2 rounded-md hover:bg-gray-100">
            <img src="../assets/icon/menu.svg" class="w-6" />
          </button>

          <a
            href="../about.php"
            class="px-4 py-1 text-sm bg-black text-white rounded-md hover:bg-gray-800 transition">
            View Site
          </a>
        </div>

        <!-- Right (Profile with Dropdown) -->
        <div class="relative pr-2 z-50">
          <button
            id="profileBtn"
            class="flex items-center hover:bg-gray-100 px-3 py-1 rounded-md transition">

            <div class="w-10 h-10 overflow-hidden rounded-md">
              <img
                src="<?= !empty($user['avatar'])
                        ? '../assets/avatar/' . htmlspecialchars($user['avatar'])
                        : '../assets/icon/user.svg' ?>"
                class="w-full h-full object-cover"
                alt="Avatar">
            </div>

            <p class="text-md font-medium ml-2">
              <?= htmlspecialchars($user["name"] ?? "Author") ?>
            </p>
          </button>

          <!-- DROPDOWN -->
          <div
            id="dropdownProfile"
            class="hidden absolute right-2 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
            <a
              href="profile_author.php"
              class="block px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2">
              <img src="../assets/icon/user.svg" class="w-4 opacity-70" />
              Profile Settings
            </a>

            <form action="../src/php/logout.php" method="POST">
              <button
                type="submit"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                <img
                  src="../assets/icon/log-out.svg"
                  class="w-4 opacity-70" />
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- DASHBOARD GRID -->
      <div class="flex gap-2 h-screen">
        <div
          class="flex flex-wrap justify-center items-center w-full min-h-full p-[20px]">
          <!-- Posts -->
          <a href="uploaded-post.php" class="bg-yellow-100 w-1/2 rounded-tl-xl p-6 flex flex-col text-black h-1/2 hover:shadow-2xl cursor-pointer">
            <div class="flex justify-between items-center">
              <p class="text-3xl font-light"><?= $published ?></p>
              <img src="../assets/icon/file-plus.svg" class="w-10 opacity-60" />
            </div>
            <p class="mt-auto tracking-wide">Uploaded Posts</p>
          </a>

          <!-- Pending Post -->
          <a href="pending.php" class="bg-green-100 w-1/2 rounded-tr-xl p-6 flex flex-col text-black h-1/2 hover:shadow-2xl cursor-pointer">
            <div class="flex justify-between items-center">
              <p class="text-3xl font-light"><?= $pending ?></p>
              <img src="../assets/icon/clock.svg" class="w-10 opacity-60" />
            </div>
            <p class="mt-auto tracking-wide">Pending Post</p>
          </a>

          <!-- Drafts -->
          <a href="drafts.php" class="bg-blue-400 w-1/2 rounded-bl-xl p-6 flex flex-col text-black h-1/2 hover:shadow-2xl cursor-pointer">
            <div class="flex justify-between items-center">
              <p class="text-3xl font-light"><?= $draft ?></p>
              <img
                src="../assets/icon/archive.svg"
                class="w-10 opacity-60 invert" />
            </div>
            <p class="mt-auto tracking-wide">Drafts</p>
          </a>

          <!-- Scheduled Posts -->
          <a href="schedule.php" class="bg-green-400 w-1/2 rounded-br-xl p-6 flex flex-col h-1/2 hover:shadow-2xl cursor-pointer">
            <div class="flex justify-between items-center">
              <p class="text-3xl font-normal"><?= $scheduled ?></p>
              <img
                src="../assets/icon/calendar.svg"
                class="w-10 opacity-60" />
            </div>
            <p class="mt-auto text-gray-700 tracking-wide">Schedule posts</p>
          </a>
        </div>
      </div>
    </div>
  </div>
  <script src="index.js"></script>
  <script>
    const postDropdownBtn = document.getElementById("postDropdownBtn");
    const postDropdown = document.getElementById("postDropdown");
    const arrowIcon = document.getElementById("arrowIcon");

    postDropdownBtn.addEventListener("click", () => {
      if (sidebar.classList.contains("sidebar-collapsed")) {
        sidebar.classList.remove("sidebar-collapsed");
      }

      postDropdown.classList.toggle("hidden");
      arrowIcon.classList.toggle("rotate-180");
    });
  </script>
</body>

</html>