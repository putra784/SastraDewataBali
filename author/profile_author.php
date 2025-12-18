<?php
session_start();
require '../src/php/function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $avatar = $_FILES['avatar'] ?? null;

    if (empty($name) || empty($username)) {
        $_SESSION['error'] = "Nama dan Email wajib diisi!";
        header("Location: profile_author.php");
        exit;
    }

    $avatarFileName = null;

    if ($avatar && $avatar['error'] === 0) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = "Format avatar harus JPG, PNG, atau WEBP!";
            header("Location: profile_author.php");
            exit;
        }

        if ($avatar['size'] > 3 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran maksimal 3MB!";
            header("Location: profile_author.php");
            exit;
        }

        $avatarFileName = "avatar_" . $user_id . "_" . time() . "." . $ext;
        $uploadPath = "../assets/avatar/" . $avatarFileName;

        $oldQuery = $conn->prepare("SELECT avatar FROM user WHERE id = ?");
        $oldQuery->bind_param("i", $user_id);
        $oldQuery->execute();
        $old = $oldQuery->get_result()->fetch_assoc();

        if (!empty($old['avatar']) && file_exists("../assets/avatar/" . $old['avatar'])) {
            unlink("../assets/avatar/" . $old['avatar']);
        }

        move_uploaded_file($avatar['tmp_name'], $uploadPath);

        $query = $conn->prepare("
            UPDATE user SET name = ?, username = ?, avatar = ?
            WHERE id = ?
        ");
        $query->bind_param("sssi", $name, $username, $avatarFileName, $user_id);
    } else {
        $query = $conn->prepare("
            UPDATE user SET name = ?, username = ?
            WHERE id = ?
        ");
        $query->bind_param("ssi", $name, $username, $user_id);
    }

    if ($query->execute()) {
        $_SESSION['success'] = "Profil berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal update profil.";
    }

    header("Location: profile_author.php");
    exit;
}

$query = $conn->prepare("SELECT name, username, created_at, avatar FROM user WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$userData = $query->get_result()->fetch_assoc();

$countQuery = $conn->prepare("
    SELECT status, COUNT(*) as total 
    FROM post 
    WHERE user_id = ? 
    GROUP BY status
");
$countQuery->bind_param("i", $user_id);
$countQuery->execute();
$resultCount = $countQuery->get_result();

$counts = ['draft' => 0, 'pending' => 0, 'scheduled' => 0, 'published' => 0];

while ($row = $resultCount->fetch_assoc()) {
    $counts[$row['status']] = $row['total'];
}

$trendingQuery = $conn->prepare("
    SELECT title, image 
    FROM post 
    WHERE user_id = ? AND status = 'published'
    ORDER BY created_at DESC LIMIT 3
");
$trendingQuery->bind_param("i", $user_id);
$trendingQuery->execute();
$trending = $trendingQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Setting</title>
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
    <div class="flex w-full h-full bg-gray-200">
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
            <a href="index.php" class="flex items-center gap-3 bg-white     text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
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
                                src="<?= !empty($userData['avatar'])
                                            ? '../assets/avatar/' . htmlspecialchars($userData['avatar'])
                                            : '../assets/icon/user.svg' ?>"
                                class="w-full h-full object-cover"
                                alt="Avatar">
                        </div>

                        <p class="text-md font-medium ml-2">
                            <?= htmlspecialchars($userData["name"] ?? "Author") ?>
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

            <!-- PROFILE CONTENT -->
            <div class="flex justify-center items-center h-full w-full gap-4 mt-4 mb-4">
                <!-- LEFT PROFILE CARD -->
                <div class="w-1/2 bg-white flex flex-col p-6 rounded-xl shadow-md ml-4">
                    <div class="flex justify-center items-center w-full text-white">
                        <img class="border border-black rounded-full w-48 h-48 object-cover"
                            src="<?= !empty($userData['avatar']) ? '../assets/avatar/' . $userData['avatar'] : '../assets/icon/user.svg' ?>"
                            alt="Avatar">
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <span class="font-medium text-lg">My Profile</span>
                        <span class="text-sm opacity-70">
                            Created at: <?= date("d M Y", strtotime($userData["created_at"])) ?>
                        </span>
                    </div>

                    <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="flex flex-col mt-4">
                        <input type="text"
                            class="border-b pb-2 focus:outline-none"
                            name="name"
                            value="<?= htmlspecialchars($userData["name"]) ?>">

                        <div class="mt-2 border-b border-black text-black pb-2">
                            <?= htmlspecialchars($userData["username"]) ?>
                        </div>

                        <!-- NEW: Upload Foto Profil -->
                        <label class="mt-4 font-medium">Update Profile Picture</label>
                        <input type="file" name="avatar" class="mt-2">

                        <span class="mt-2 text-yellow-900 underline">As an Author</span>

                        <button class="text-white bg-yellow-900 p-2 rounded-md mt-10"
                            type="submit" name="submit">
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- RIGHT CONTENT -->
                <div class="w-1/2 mr-4 flex flex-col gap-4 h-auto">

                    <!-- STATUS CARD -->
                    <div class="flex flex-col rounded-xl w-full bg-white items-center shadow-md p-6">
                        <h2 class="text-lg font-medium mb-4">Status Post</h2>

                        <div class="flex w-full gap-2">
                            <div class="flex-1 flex flex-col items-center bg-yellow-900 p-4 text-white rounded-md">
                                <span>Drafts</span>
                                <span><?= $counts['draft'] ?></span>
                            </div>
                            <div class="flex-1 flex flex-col items-center bg-yellow-900 p-4 text-white rounded-md">
                                <span>Pending</span>
                                <span><?= $counts['pending'] ?></span>
                            </div>
                            <div class="flex-1 flex flex-col items-center bg-yellow-900 p-4 text-white rounded-md">
                                <span>Scheduled</span>
                                <span><?= $counts['scheduled'] ?></span>
                            </div>
                            <div class="flex-1 flex flex-col items-center bg-yellow-900 p-4 text-white rounded-md">
                                <span>Published</span>
                                <span><?= $counts['published'] ?></span>
                            </div>
                        </div>

                        <a href="add-post.php"
                            class="bg-yellow-900 mt-4 text-white p-2 w-3/4 text-center rounded-md hover:bg-yellow-800">
                            + Add Post
                        </a>
                    </div>

                    <!-- TRENDING POSTS -->
                    <div class="flex flex-col rounded-xl w-full bg-white items-center shadow-md py-4">
                        <h2 class="font-medium text-lg">Top Three Trending Post</h2>

                        <div class="flex gap-4 mt-4 mb-4">
                            <?php if ($trending->num_rows > 0): ?>
                                <?php while ($row = $trending->fetch_assoc()): ?>
                                    <div class="border border-black rounded-lg max-w-32 p-2">
                                        <img class="rounded-md h-20 w-full object-cover"
                                            src="../assets/image/<?= htmlspecialchars($row['image']) ?>"
                                            alt="Post Image">
                                        <p class="text-sm mt-1">
                                            <?= htmlspecialchars(substr($row['title'], 0, 30)) ?>...
                                        </p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-600">Belum ada postingan published</p>
                            <?php endif; ?>
                        </div>
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