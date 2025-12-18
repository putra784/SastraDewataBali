<?php
session_start();
require 'src/php/function.php';

// --- GET ARTICLE ID --- //
$id = $_GET['id'] ?? 0;

// --- AMBIL DETAIL ARTIKEL --- //
$detail = query("
    SELECT post.*, user.username, user.name
    FROM post
    JOIN user ON user.id = post.user_id
    WHERE post.id = $id
")[0];

// --- AMBIL RECENT POSTS --- //
$recentPosts = query("SELECT * FROM post WHERE status='published' ORDER BY created_at DESC LIMIT 5");

// --- COMMENT CREATE --- //
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment'])) {

    if (!isset($_SESSION["user_id"])) {
        echo "
        <script>
            alert('Anda harus login terlebih dahulu untuk melakukan komentar.');
            window.location.href = 'login_page/login.php';
        </script>";
        exit;
    }

    $comment = htmlspecialchars($_POST['comment']);
    $postId  = $id;

    mysqli_query(
        $conn,
        "INSERT INTO comments (post_id, comment, created_at, user_id)
         VALUES ('$postId', '$comment', NOW(), '{$_SESSION["user_id"]}')"
    );

    header("Location: content.php?id=$id");
    exit;
}

// --- AMBIL KOMENTAR --- //
$comments = query("
    SELECT comments.*, user.name, user.avatar
    FROM comments
    LEFT JOIN user ON user.id = comments.user_id
    WHERE comments.post_id = $id
    ORDER BY comments.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $detail['title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

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

    <!-- MAIN CONTAINER (SIDEBAR + ARTICLE) -->
    <div class="flex px-14 mt-32 gap-6">

        <!-- LEFT SIDEBAR -->
        <div class="w-1/4 flex flex-col gap-6 justify-center">

            <!-- LOGO BOX -->
            <div class="bg-white p-6 rounded-3xl shadow border border-gray-300 flex justify-center">
                <img src="assets/icon/logo.png" class="w-24 rounded-full p-4 bg-yellow-900" />
            </div>

            <!-- RECENT POSTS -->
            <div class="bg-white rounded-3xl border border-gray-300 p-5">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold mb-3">Recent Post</h2>
                    <div class="h-1 w-[40%] bg-yellow-900 mb-4 rounded-full"></div>
                </div>

                <?php foreach ($recentPosts as $post): ?>
                    <a href="content.php?id=<?= $post['id'] ?>">
                        <div class="flex mb-4 cursor-pointer">
                            <img class="w-24 h-24 rounded-xl object-cover border hover:transform hover:scale-105 transition-all duration-200"
                                src="assets/image/<?= $post['image'] ?>">
                            <div class="ml-4">
                                <p class="font-semibold hover:underline"><?= $post['title'] ?></p>
                                <p class="text-gray-600 text-sm">
                                    <?= substr($post['title'], 0, 60) ?>...
                                </p>
                            </div>
                        </div>
                    </a>
                    <div class="h-0.5 bg-yellow-900 rounded-full mb-4"></div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- MAIN ARTICLE -->
        <div class="w-3/4 bg-white border border-gray-300 rounded-3xl shadow p-6">

            <!-- Gambar Besar -->
            <img src="assets/image/<?= $detail['image'] ?>"
                class="w-full h-[400px] object-cover rounded-3xl mb-5 -mt-14">

            <!-- Category + Author -->
            <div class="flex items-center justify-between mb-3">
                <span class="px-4 py-1 bg-yellow-900 text-white rounded-full text-sm">
                    <?= strtoupper($detail['category']) ?>
                </span>

                <span class="text-gray-600 text-sm">
                    By <?= $detail['name'] ?> • <?= date("d F Y", strtotime($detail['created_at'])) ?>
                </span>
            </div>

            <!-- Judul -->
            <h1 class="text-4xl font-bold mt-4 mb-4"><?= $detail['title'] ?></h1>

            <!-- Lokasi -->
            <p class="text-yellow-900 font-semibold mb-3">
                📍 <?= $detail['location'] ?>
            </p>

            <!-- Konten -->
            <p class="text-gray-800 leading-7 whitespace-pre-line">
                <?= $detail['summary'] ?>
            </p>
        </div>
    </div>

    <!-- COMMENT SECTION -->
    <div class="mx-14 mt-10 bg-white border rounded-3xl shadow p-6 mb-20">

        <h2 class="text-2xl font-bold mb-4">Leave a Comment</h2>

        <!-- LIST COMMENT -->
        <?php foreach ($comments as $c): ?>
            <?php
            // Tentukan avatar (default user.svg)
            $avatar = $c['avatar'] ? "assets/avatar/" . $c['avatar'] : "assets/icon/user.svg";
            // Tentukan nama user
            $name   = $c['name'] ?? "Unknown User";
            ?>

            <div class="flex items-start mb-4">
                <!-- Avatar -->
                <img src="<?= $avatar ?>" class="w-10 h-10 rounded-full border object-cover">

                <!-- Isi komentar -->
                <div class="ml-4">
                    <p class="font-semibold"><?= $name ?></p>
                    <p class="text-gray-800"><?= $c['comment'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?= date("d M Y H:i", strtotime($c['created_at'])) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- FORM COMMENT -->
        <form method="POST" class="mt-6">
            <textarea
                name="comment"
                required
                class="w-full border p-4 rounded-xl mb-4"
                placeholder="Write a comment..."></textarea>

            <button
                class="px-6 py-2 bg-yellow-900 text-white rounded-xl hover:bg-yellow-800">
                Post comment
            </button>
        </form>

    </div>

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