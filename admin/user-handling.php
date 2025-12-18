<?php

session_start();
require '../src/php/function.php';

$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? "";

$users = getAllUsers($limit, $offset, $search);
$totalUsers = countUsers($search);
$totalPages = ceil($totalUsers / $limit);

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
    <div class="flex w-full h-auto bg-gray-200 min-h-screen">
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
            <a href="index.php" class="flex items-center gap-3 bg-white text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
                <img src="../assets/icon/home.svg" class="w-5" />
                <span class="text-sm sidebar-text">Home</span>
            </a>

            <!-- User -->
            <a
                href="user-handling.php"
                class="flex items-center gap-3 bg-gray-300 text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
                <img src="../assets/icon/user.svg" class="w-5" />
                <span class="text-sm sidebar-text">User</span>
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
                        href="pending.php"
                        class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Pending Posts</a>
                    <a href="schedule.php" class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Schedule Posts</a>
                    <a
                        href="uploaded-post.php"
                        class="px-4 py-2 text-sm hover:bg-gray-300 border-b border-gray-500">Uploaded Posts</a>
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
                        class="flex items-center justify-center hover:bg-gray-100 px-3 py-1 rounded-md transition">
                        <img src="../assets/icon/user.svg" class="w-7" />
                        <p class="text-md font-medium ml-2">
                            <?= htmlspecialchars($user["name"] ?? "Author") ?>
                        </p>
                    </button>

                    <!-- DROPDOWN -->
                    <div
                        id="dropdownProfile"
                        class="hidden absolute right-2 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
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
            <div class="w-full p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold tracking-wide">User</h1>
                </div>

                <!-- Filters -->
                <form method="GET" class="mb-6">
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
                                <th class="px-3 py-2 border">User</th>
                                <th class="px-3 py-2 border">Username</th>
                                <th class="px-3 py-2 border">Role</th>
                                <th class="px-3 py-2 border">Status</th>
                                <th class="px-3 py-2 border">Created At</th>
                                <th class="px-3 py-2 border">Option</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $number = $offset + 1; ?>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b hover:bg-gray-50">

                                    <!-- Number -->
                                    <td class="px-3 py-2 border text-center">
                                        <?= $number++; ?>
                                    </td>

                                    <!-- User (Avatar + Name) -->
                                    <td class="px-3 py-2 border flex items-center gap-3">
                                        <img src="../assets/avatar/<?= htmlspecialchars($user["avatar"]) ?>"
                                            class="w-12 h-12 object-cover rounded-full" />
                                        <span><?= htmlspecialchars($user["name"]) ?></span>
                                    </td>

                                    <!-- Username -->
                                    <td class="px-3 py-2 border">
                                        <?= htmlspecialchars($user["username"]) ?>
                                    </td>

                                    <!-- Role -->
                                    <td class="px-3 py-2 border">
                                        <?= $user["role"] ?>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-3 py-2 border capitalize">
                                        <?= htmlspecialchars($user["status"]) ?>
                                    </td>

                                    <!-- Created At -->
                                    <td class="px-3 py-2 border text-center">
                                        <?= date("Y-m-d H:i:s", strtotime($user["created_at"])) ?>
                                    </td>

                                    <!-- Option -->
                                    <td class="px-3 py-2 border text-center">
                                        <?php if ($user['status'] === 'active'): ?>
                                            <!-- Non Active -->
                                            <a href="option/nonactive-user.php?id=<?= $user['id'] ?>&status=non_active"
                                                class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
                                                onclick="return confirm('Deactivate this account?')">
                                                Jadikan Non Active
                                            </a>
                                        <?php else: ?>
                                            <!-- Active -->
                                            <a href="option/toggle-user-status.php?id=<?= $user['id'] ?>&status=active"
                                                class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700"
                                                onclick="return confirm('Activate this account?')">
                                                Jadikan Active
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                    <div class="flex justify-center gap-2 mt-4">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= $search ?>"
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