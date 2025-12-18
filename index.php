<?php
session_start();
require 'src/php/function.php';
$trendingPosts = getTrendingPosts();
$rightPosts = getDataRightSide();
$missedPosts = getImgYouMayMissed();

$postResult = $conn->query("
    SELECT id, title, created_at 
    FROM post
    WHERE status = 'published'
");

$archiveResult = $conn->query("
    SELECT 
        YEAR(created_at)  AS year,
        MONTH(created_at) AS month
    FROM post
    WHERE status = 'published'
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC
    LIMIT 4
");

$query = $conn->prepare("SELECT name, username, created_at, avatar FROM user WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$userData = $query->get_result()->fetch_assoc();

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
                    href="<?= ($role === 'admin') ? 'admin/profile.php' : 'author/profile_author.php' ?>"
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

  <!--  Section -->
  <section class="w-full">
    <div class="h-auto w-full">
      <div class="w-full flex justify-center">
        <form
          action="search.php"
          method="GET"
          class="w-full max-w-md flex gap-2 items-center justify-center mt-4">
          <input
            type="text"
            name="q"
            placeholder="Search..."
            class="bg-gray-100 w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-yellow-900 focus:shadow-lg transition-all duration-300 hover:bg-white focus:bg-white"
            required />

          <button type="submit">
            <img
              class="bg-gray-100 rounded-lg border border-gray-300 p-2 cursor-pointer hover:bg-white transition-all duration-200 ease-in"
              src="assets/icon/search.svg"
              alt="search-icon" />
          </button>
        </form>
      </div>

      <div class="relative mx-14 mt-6">
        <!-- VIEWPORT: area yang terlihat -->
        <div
          id="carouselViewport"
          class="overflow-hidden rounded-3xl bg-white border border-gray-300">
          <!-- TRENDING TOPIC -->
          <div
            id="carouselTrack"
            class="flex transition-transform duration-500 ease-in-out w-full bg-red-50">
            <?php foreach ($trendingPosts as $post): ?>
              <article class="w-full flex-shrink-0 flex justify-center items-center">
                <div class="relative flex justify-between items-center bg-white h-full rounded-2xl shadow-sm">

                  <div class="flex flex-col justify-between p-6 w-1/2">
                    <div class="pl-14">
                      <div class="flex gap-2 mb-4 justify-between">
                        <button class="bg-yellow-900 text-white px-4 py-2 rounded-3xl h-10">Trending Topic</button>
                        <button class="bg-yellow-900 text-white px-4 py-2 rounded-3xl h-10"><?= htmlspecialchars($post["category"]) ?></button>
                      </div>

                      <a href="content.php?id=<?= $post['id'] ?>" class="flex">
                        <h2 class="text-gray-900 mb-2 mt-4 text-3xl text-right font-bold ml-auto relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gray-700 after:transition-all hover:after:w-full after:rounded-full">
                          <?= htmlspecialchars($post["title"]) ?>
                        </h2>
                      </a>

                      <p class="text-gray-700 mb-4 text-right">
                        <?= substr(strip_tags($post["summary"]), 0, 120) . "..." ?>
                      </p>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                      <div class="text-right">
                        <p class="text-gray-700 font-medium"><?= htmlspecialchars($post["author"]) ?></p>
                      </div>
                      <a href="profil_all.php?user_id=<?= $post['user_id'] ?>" class="w-12 h-12">
                        <img class="hover:shadow-yellow-900 transition-all duration-200 bg-white border rounded-full h-12 w-12"
                          src="assets/avatar/<?= htmlspecialchars($post['avatar'] ?? 'user.svg') ?>"
                          alt="Author Avatar">
                      </a>
                    </div>
                  </div>

                  <a href="content.php?id=<?= $post['id'] ?>" class="w-1/2 overflow-hidden rounded-r-2xl -mr-0">
                    <img
                      class="w-full h-80 object-cover hover:scale-105 transition-all duration-300 ease"
                      src="assets/image/<?= htmlspecialchars($post["image"]) ?>"
                      alt="<?= htmlspecialchars($post["title"]) ?>" />
                  </a>
                </div>
              </article>
            <?php endforeach; ?>

          </div>
        </div>

        <!-- Prev/Next buttons -->
        <button
          id="prevBtn"
          aria-label="Previous slide"
          class="flex items-center justify-center focus-outline absolute left-3 top-1/2 -translate-y-1/2 bg-yellow-900 text-white p-3 w-12 h-12 rounded-full shadow-lg hover:bg-yellow-800">
          <!-- left arrow -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          id="nextBtn"
          aria-label="Next slide"
          class="flex items-center justify-center focus-outline absolute right-3 top-1/2 -translate-y-1/2 bg-yellow-900 text-white p-3 w-12 h-12 rounded-full shadow-lg hover:bg-yellow-800">
          <!-- right arrow -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- pagination dots -->
      <div id="dots" class="flex justify-center gap-2 mt-4"></div>
    </div>

    <!-- Hero Section -->
    <div class="flex h-[200vh] px-14 mt-10">
      <!-- Left Side -->
      <div class="flex flex-col w-1/3 h-full gap-4">
        <div
          class="bg-white shadow-sm rounded-3xl h-1/5 flex justify-center items-start pt-6 border border-gray-300">
          <img
            class="shadow-md w-32 bg-yellow-900 rounded-full p-4"
            src="assets/icon/logo.png"
            alt="" />
        </div>

        <div class="border border-gray-300 rounded-3xl h-1/2">
          <div class="flex justify-between items-center px-6 pt-1 gap-2">
            <h2 class="text-3xl font-bold">Recent Post</h2>
            <div class="h-1.5 rounded-full w-[45%] bg-yellow-900"></div>
          </div>

          <div class="flex h-full justify-between flex-col -mt-10 pt-10">

            <?php
            // Ambil post terbaru yang sudah publish
            $recentPosts = query("SELECT * FROM post WHERE status='published' ORDER BY created_at DESC LIMIT 3");

            foreach ($recentPosts as $post):
            ?>

              <!-- Card -->
              <div class="flex justify-center items-center w-full px-6 pt-4">
                <a
                  href="content.php?id=<?= $post['id'] ?>"
                  class="h-32 w-1/2 shadow-md overflow-hidden rounded-3xl -mr-0">
                  <img
                    class="w-full h-full object-cover rounded-3xl transform transition-transform duration-300 hover:scale-105"
                    src="assets/image/<?= htmlspecialchars($post['image']) ?>"
                    alt="<?= htmlspecialchars($post['title']) ?>" />
                </a>

                <div class="ml-6 flex flex-col w-1/2">
                  <h2 class="text-xl font-medium">
                    <a href="content.php?id=<?= $post['id'] ?>">
                      <?= htmlspecialchars($post['title']) ?>
                    </a>
                  </h2>

                  <p class="text-gray-800 break-words w-full">
                    <?= substr(strip_tags($post['summary']), 0, 30) . "..." ?>
                  </p>

                  <a class="mt-2 text-blue-600 hover:underline" href="content.php?id=<?= $post['id'] ?>">
                    Read More >>
                  </a>
                </div>
              </div>

              <div class="h-0.5 rounded-full bg-yellow-900 mx-5"></div>

            <?php endforeach; ?>

            <div class="mb-2"></div>
          </div>
        </div>

        <div class="flex flex-col justify-between border border-gray-300 rounded-3xl h-auto">
          <div class="flex justify-between items-center px-6 pt-1 gap-2">
            <h2 class="text-3xl font-bold">Arsip</h2>
            <div class="h-1.5 rounded-full w-[70%] bg-yellow-900"></div>
          </div>

          <?php while ($row = $archiveResult->fetch_assoc()) : ?>

            <?php
            $month = $row['month'];
            $year  = $row['year'];
            $label = date('F Y', strtotime("$year-$month-01"));
            ?>

            <div class="mx-5 my-5">
              <a
                href="arsip.php?month=<?= $month ?>&year=<?= $year ?>"
                class="text-2xl font-medium relative
               after:content-[''] after:absolute after:left-0 after:-bottom-1
               after:h-[2px] after:w-0 after:bg-gray-700
               after:transition-all hover:after:w-full after:rounded-full">
                <?= $label ?>
              </a>
            </div>

          <?php endwhile; ?>
        </div>

      </div>

      <!-- Right Side -->
      <div
        class="flex flex-col justify-between w-2/3 ml-6 h-full rounded-3xl">
        <?php foreach ($rightPosts as $post) : ?>
          <div
            class="flex justify-between items-center border border-gray-300 h-1/4 mb-2 bg-white rounded-3xl">
            <div class="flex flex-col mx-4">

              <!-- Category -->
              <a
                class="px-4 py-2 font-normal text-sm bg-yellow-900 rounded-3xl text-right ml-auto text-white hover:bg-yellow-800 transition-all duration-200 ease-in"
                href="#">
                <?= htmlspecialchars($post['category']); ?>
              </a>

              <!-- Title -->
              <a
                class="mt-4 text-2xl font-bold ml-auto relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gray-700 after:transition-all hover:after:w-full after:rounded-full"
                href="#">
                <?= htmlspecialchars($post['title']); ?>
              </a>

              <!-- Summary (dipotong) -->
              <p class="text-right ml-auto mb-1 mt-1">
                <?= htmlspecialchars(strlen($post['summary']) > 100
                  ? substr($post['summary'], 0, 100) . "..."
                  : $post['summary']); ?>
              </p>

              <!-- Read More -->
              <a class="text-blue-600 text-right ml-auto hover:underline" href="content.php?id=<?= $post['id'] ?>">
                Read More>>
              </a>

              <!-- Author -->
              <div class="mt-5 flex gap-2 items-center ml-auto text-right">
                <span class="text-gray-700 font-medium hover:text-gray-900">
                  <?= htmlspecialchars($post['name']); ?>
                </span>
                <a href="profil_all.php?user_id=<?= $post['user_id'] ?>">
                  <img class="hover:shadow-yellow-900 transition-all duration-200 bg-white border rounded-full w-12 h-12"
                    src="assets/avatar/<?= htmlspecialchars($post['avatar'] ?? 'user.svg') ?>"
                    alt="Author Avatar">
                </a>
              </div>

            </div>

            <!-- Image (FLEXIBLE WIDTH & HEIGHT) -->
            <a class="rounded-3xl bg-red-50 relative -mr-6 ml-2" href="#">
              <div
                class="rounded-3xl overflow-hidden"
                style="width: 300px; height: 240px;">
                <img
                  class="w-full h-full object-cover hover:scale-105 transition-all duration-300 ease"
                  src="assets/image/<?= htmlspecialchars($post['image']); ?>"
                  alt="" />
              </div>
            </a>

          </div>
        <?php endforeach; ?>

      </div>
    </div>

    <!-- You May Missed Card -->
    <div
      class="w-auto flex flex-col bg-white border border-gray-300 shadow-md px-8 py-4 mt-6 mx-14 rounded-3xl">

      <!-- You May Missed Title -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">You May Missed</h1>
        <div class="h-1 w-[75%] bg-yellow-900 rounded-full"></div>
      </div>

      <!-- Image That You Miss -->
      <div class="flex justify-between w-full pb-4">

        <?php foreach ($missedPosts as $post) : ?>
          <a class="group w-[31.5%] h-56 overflow-hidden rounded-3xl relative"
            href="content.php?id=<?= $post['id'] ?>">

            <!-- Judul -->
            <h1 class="text-2xl absolute bottom-0 left-0 p-4 text-white font-semibold 
                 transition:all duration-300 group-hover:text-gray-100 z-10">
              <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <!-- Gambar -->
            <img
              class="w-full h-full object-cover rounded-3xl 
               transition duration-300 ease group-hover:scale-105"
              src="assets/image/<?php echo htmlspecialchars($post['image']); ?>"
              alt="Post image">
          </a>
        <?php endforeach; ?>

      </div>

    </div>

  </section>

  <!-- Footer -->
  <footer class="mt-12 w-full flex flex-col bg-yellow-900 h-72 justify-between pb-6">
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

  <!-- JS -->
  <script src="src/js/script.js"></script>
</body>

</html>