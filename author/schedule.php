<?php

session_start();
require '../src/php/function.php';

// Redirect jika tidak login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_page/login.php");
    exit;
}

// Ambil data user
$user = getUserById($_SESSION["user_id"]);

// Get filter value
$show = isset($_GET["show"]) ? $_GET["show"] : 5;
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$language = isset($_GET["language"]) ? $_GET["language"] : "";
$category = isset($_GET["category"]) ? $_GET["category"] : "";
$search = isset($_GET["search"]) ? $_GET["search"] : "";

// pagination
$offset = ($page - 1) * $show;

// ambil posts status schedule
$posts = getFilteredPosts('scheduled', $_SESSION["user_id"], $show, $offset, $language, $category, $search);

// total records for pagination
$totalPosts = getFilteredTotalPosts('schedule', $_SESSION["user_id"], $language, $category, $search);
$totalPages = ceil($totalPosts / $show);

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
    </style>
</head>

<body>
    <!-- Wrapper -->
    <div class="flex w-full min-h-screen bg-gray-200">
        <!-- SIDEBAR -->
        <div class="w-[260px] bg-[#4e342e] text-white flex flex-col p-6">
            <!-- Logo -->
            <div class="flex flex-col items-center gap-2 mb-10">
                <img src="../assets/icon/logo.png" class="w-28" alt="Logo" />
                <p class="text-lg tracking-widest font-semibold">SASTRA DEWATA</p>
            </div>

            <!-- Navigation Title -->
            <p class="uppercase text-sm tracking-wider mb-4">Main Navigation</p>

            <!-- Home -->
            <a
                href="index.php"
                class="flex items-center gap-3 bg-white text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
                <img src="../assets/icon/home.svg" class="w-5" />
                <span class="text-sm">Home</span>
            </a>

            <!-- Add Post -->
            <a
                href="add-post.php"
                class="flex items-center gap-3 bg-white text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
                <img src="../assets/icon/plus-square.svg" class="w-5" />
                <span class="text-sm">Add Post</span>
            </a>

            <!-- Post List -->
            <div class="mb-3">
                <button
                    id="postDropdownBtn"
                    class="w-full flex items-center justify-between bg-white text-black px-4 py-2 rounded-md hover:bg-gray-300 transition">
                    <div class="flex items-center gap-3">
                        <img src="../assets/icon/file-plus.svg" class="w-5" />
                        <span class="text-sm">Posts</span>
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
                        class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Uploaded Post</a>
                    <a
                        href="pending.php"
                        class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Pending Posts</a>
                    <a href="schedule.php" class="px-4 py-2 text-sm hover:bg-gray-300 bg-gray-300">Schedule Posts</a>
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
                    <button class="p-2 rounded-md hover:bg-gray-100">
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

            <!-- CONTENT WRAPPER -->
            <div class="w-full p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold tracking-wide">Schedule Posts</h1>

                    <a
                        href="add-post.php"
                        class="px-4 py-2 bg-[#4e342e] text-white text-sm rounded hover:bg-[#3c2824] transition">
                        Add Posts
                    </a>
                </div>

                <!-- Filters -->
                <form method="GET" class="flex items-center gap-4 mb-6">
                    <div>
                        <p class="text-sm mb-1">Show</p>
                        <select name="show" onchange="this.form.submit()" class="px-3 py-2 border rounded text-sm bg-white">
                            <option <?= $show == 5 ? "selected" : "" ?>>5</option>
                            <option <?= $show == 10 ? "selected" : "" ?>>10</option>
                        </select>
                    </div>

                    <div>
                        <p class="text-sm mb-1">Language</p>
                        <select name="language" onchange="this.form.submit()" class="px-3 py-2 border rounded text-sm bg-white">
                            <option value="">All</option>
                            <option value="English" <?= $language == 'English' ? "selected" : "" ?>>English</option>
                            <option value="Indonesia" <?= $language == 'Indonesia' ? "selected" : "" ?>>Indonesia</option>
                        </select>
                    </div>

                    <div>
                        <p class="text-sm mb-1">Category</p>
                        <select name="category" onchange="this.form.submit()" class="px-3 py-2 border rounded text-sm bg-white">
                            <option value="">All</option>
                            <option value="Balinese Nature" <?= $category == 'Balinese Nature' ? "selected" : "" ?>>Balinese Nature</option>
                            <option value="Tradition" <?= $category == 'Tradition' ? "selected" : "" ?>>Tradition</option>
                            <option value="Palm Leaf" <?= $category == 'Palm Leaf' ? "selected" : "" ?>>Palm Leaf</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <p class="text-sm mb-1">Search</p>
                        <div class="flex items-center">
                            <input type="text" name="search" value="<?= $search ?>" class="px-3 py-2 border rounded-l text-sm" placeholder="Search title...">
                            <button class="px-3 py-[10px] bg-gray-200 rounded-r hover:bg-gray-300 transition">
                                <img src="../assets/icon/search.svg" class="w-4" />
                            </button>
                        </div>
                    </div>
                </form>

                <!-- TABLE -->
                <div class="overflow-x-auto bg-white border rounded shadow-sm">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-3 py-2 border">Id</th>
                                <th class="px-3 py-2 border">Image</th>
                                <th class="px-3 py-2 border">Title</th>
                                <th class="px-3 py-2 border">Language</th>
                                <th class="px-3 py-2 border">Category</th>
                                <th class="px-3 py-2 border">Status</th>
                                <th class="px-3 py-2 border">Created At</th>
                                <th class="px-3 py-2 border">Option</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $number = $offset + 1; ?>
                            <?php foreach ($posts as $post): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 border text-center">
                                        <?= $number++; ?>
                                    </td>

                                    <td class="px-3 py-2 border">
                                        <img src="../assets/image/<?= htmlspecialchars($post["image"]) ?>"
                                            class="w-14 h-14 object-cover rounded" />
                                    </td>

                                    <td class="px-3 py-2 border">
                                        <?= htmlspecialchars($post["title"]) ?>
                                    </td>

                                    <td class="px-3 py-2 border"><?= $post["language"] ?></td>

                                    <td class="px-3 py-2 border"><?= $post["category"] ?></td>

                                    <td class="px-3 py-2 border capitalize">
                                        <?= $post["status"] ?>
                                    </td>

                                    <td class="px-3 py-2 border text-center">
                                        <?= date("Y-m-d H:i:s", strtotime($post["created_at"])) ?>
                                    </td>

                                    <td class="px-3 py-2 border text-center">
                                        <a
                                            href="edit-post.php?id=<?= $post['id'] ?>"
                                            class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 mr-4">
                                            Edit
                                        </a>

                                        <a
                                            href="delete-post.php?id=<?= $post['id'] ?>"
                                            class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                                            onclick="return confirm('Are you sure?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="flex justify-center gap-2 mt-4">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&show=<?= $show ?>&language=<?= $language ?>&category=<?= $category ?>&search=<?= $search ?>"
                                class="mb-4 px-3 py-1 rounded border <?= ($i == $page) ? 'bg-gray-700 text-white' : 'bg-white hover:bg-gray-300' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="index.js"></script>
</body>

</html>