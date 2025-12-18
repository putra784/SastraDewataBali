<?php
session_start();
require '../src/php/function.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_page/login.php");
    exit;
}

$user = getUserById($_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_SESSION["user_id"];

    // TENTUKAN STATUS BERDASARKAN BUTTON
    if (isset($_POST["save_draft"])) {
        $_POST["status"] = "draft";
    } elseif (isset($_POST["submit"])) {
        $_POST["status"] = "pending";
    }

    // BARU BUAT POST
    $result = createPost($_POST, $_FILES, $userId);

    if ($result["status"] === true) {

        if ($_POST["status"] === "draft") {
            echo "<script>
                alert('Post berhasil disimpan sebagai draft!');
                window.location.href = 'drafts.php?post={$result['slug']}';
            </script>";
        } else {
            echo "<script>
                alert('Post berhasil dikirim!');
                window.location.href = 'pending.php?post={$result['slug']}';
            </script>";
        }

    } else {
        echo "<script>alert('{$result['message']}');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Post</title>
    <link rel="stylesheet" href="../src/output.css" />

    <!-- Dropdown Animation -->
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

<body class="bg-gray-100">
    <div class="flex w-full h-screen">

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
                class="flex items-center gap-3 bg-gray-300 text-black px-4 py-2 rounded-md mb-3 hover:bg-gray-300 transition">
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
                    <a href="schedule.php" class="px-4 py-2 text-sm hover:bg-gray-300">Schedule Posts</a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-full overflow-auto">

            <!-- TOP NAVBAR -->
            <div class="w-full flex justify-between items-center px-6 py-4 bg-white shadow relative">

                <!-- Left -->
                <div class="flex items-center gap-4">
                    <button class="p-2 rounded-md hover:bg-gray-100">
                        <img src="../assets/icon/menu.svg" class="w-6" />
                    </button>

                    <a href="../about.php"
                        class="px-4 py-1 text-sm bg-black text-white rounded-md hover:bg-gray-800 transition">
                        View Site
                    </a>
                </div>

                <!-- Right Profile -->
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

                    <div id="dropdownProfile"
                        class="hidden absolute right-2 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <a href="profile_author.php"
                            class="block px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2">
                            <img src="../assets/icon/user.svg" class="w-4 opacity-70" />
                            Profile Settings
                        </a>

                        <form action="../src/php/logout.php" method="POST">
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <img src="../assets/icon/log-out.svg" class="w-4 opacity-70" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ADD POST FORM CONTENT -->
            <div class="p-10 flex flex-col m-8 bg-gray-200 rounded-3xl">

                <h1 class="text-3xl font-semibold mb-6">Add Post</h1>

                <form action="" method="POST" enctype="multipart/form-data"
                    class="grid grid-cols-3 gap-8">

                    <!-- LEFT SIDE -->
                    <div class="col-span-2 space-y-6">

                        <div>
                            <label class="font-semibold">Title</label>
                            <input type="text" name="title" required
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md">
                        </div>

                        <div>
                            <label class="font-semibold">Slug (auto)</label>
                            <input type="text" name="slug" id="slug" readonly
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md">
                        </div>

                        <div>
                            <label class="font-semibold">Lokasi</label>
                            <input type="text" name="location" required
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md">
                        </div>

                        <div>
                            <label class="font-semibold">Waktu Pengambilan Foto</label>
                            <input type="datetime-local" name="date" required
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md">
                        </div>

                        <div>
                            <label class="font-semibold">Tags</label>
                            <input type="text" name="tags" required
                                placeholder="example: culture, tradition"
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md">
                        </div>

                        <div>
                            <label class="font-semibold">Summary</label>
                            <textarea name="summary" rows="5" required
                                class="w-full mt-1 px-4 py-2 bg-gray-50 border rounded-md"></textarea>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="space-y-6">

                        <!-- Image Upload -->
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="font-semibold mb-3">Select Image</p>

                            <div class="w-full h-48 bg-gray-100 border flex items-center justify-center rounded-md">
                                <p class="text-gray-400">No Image Selected</p>
                            </div>

                            <input type="file" name="image" accept="image/*" required
                                class="mt-3 w-full text-sm">
                        </div>

                        <!-- Category + Language -->
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="font-semibold mb-3">Category</p>

                            <label class="text-sm">Language</label>
                            <select name="language" required
                                class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-md">
                                <option value="English">English</option>
                                <option value="Indonesian">Indonesian</option>
                            </select>

                            <label class="text-sm mt-3">Category</label>
                            <select name="category" required
                                class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-md">
                                <option value="">Select a category</option>
                                <option value="Balinese Nature">Balinese Nature</option>
                                <option value="Tradition">Tradition</option>
                                <option value="Palm Leaf">Palm Leaf</option>
                            </select>
                        </div>

                        <!-- Publish -->
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="font-semibold mb-3">Publish</p>

                            <input type="datetime-local" name="scheduled_at" id="scheduleTime"
                                class="hidden mt-3 w-full px-3 py-2 bg-gray-50 border rounded-md">

                            <div class="flex justify-between mt-4">
                                <button type="submit" name="save_draft"
                                    class="px-4 py-2 bg-gray-700 text-white rounded-md">
                                    Save as Draft
                                </button>

                                <button type="submit" name="submit"
                                    class="px-4 py-2 bg-green-500 text-white rounded-md">
                                    Submit
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>

        </div>
    </div>

    <script src="index.js"></script>

    <script>
        // Auto slug generator
        document.querySelector("input[name='title']").addEventListener("input", function() {
            document.getElementById("slug").value =
                this.value.toLowerCase().replace(/ /g, "-").replace(/[^a-z0-9-]/g, "");
        });

        // Scheduled visibility
        document.getElementById("scheduledCheck").addEventListener("change", function() {
            document.getElementById("scheduleTime").classList.toggle("hidden", !this.checked);
        });
    </script>

</body>

</html>