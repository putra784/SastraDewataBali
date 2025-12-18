<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "db_sastra_dewata";

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);

    if ($result === true || $result === false) {
        return $result;
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Login Verification
function login_verif($email, $password)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $email);
    $query = mysqli_query($conn, "SELECT * FROM user WHERE username = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        if ($user['status'] === 'non active') {
            return [
                "status"  => false,
                "message" => "non active"
            ];
        }

        if (password_verify($password, $user['password'])) {
            return [
                "status" => true,
                "data" => [
                    "id"    => $user["id"],
                    "email" => $user["email"],
                    "name"  => $user["name"],
                    "role"  => $user["role"],
                ]
            ];
        }
    }

    return [
        "status"  => false,
        "message" => "invalid"
    ];
}


// Register (Insert data into database)
function register_user($data)
{
    global $conn;

    $name = $data["username"];
    $email  = strtolower(trim($data["email"]));
    $password  = $data["password"];
    $password2 = $data["password2"];

    if (empty($name) || empty($email) || empty($password) || empty($password2)) {
        return [
            "status" => false,
            "message" => "Semua field wajib diisi."
        ];
    }

    if ($password !== $password2) {
        return [
            "status" => false,
            "message" => "Konfirmasi password tidak sama."
        ];
    }

    $check = mysqli_query($conn, "SELECT id FROM user WHERE username='$email'");
    if (mysqli_num_rows($check) > 0) {
        return [
            "status" => false,
            "message" => "Email sudah terdaftar."
        ];
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = "author";
    $avatar = "user.svg";

    $query = "INSERT INTO user (name, username, password, role, avatar)
              VALUES ('$name', '$email', '$hashed', '$role', '$avatar')";

    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        return [
            "status" => true,
            "message" => "Registrasi berhasil."
        ];
    }

    return [
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ];
}

// Mencari user dari ID
function getUserById($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM user WHERE id = $id");
    return mysqli_fetch_assoc($query);
}

// Membuat Post
function createPost($data, $file, $userId)
{
    global $conn;

    // Form fields aman
    $title       = mysqli_real_escape_string($conn, $data["title"] ?? '');
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $location    = mysqli_real_escape_string($conn, $data["location"] ?? '');
    $date        = !empty($data["date"]) ? $data["date"] : NULL;
    $tags        = mysqli_real_escape_string($conn, $data["tags"] ?? '');
    $summary     = mysqli_real_escape_string($conn, $data["summary"] ?? '');
    $language    = mysqli_real_escape_string($conn, $data["language"] ?? '');
    $category    = mysqli_real_escape_string($conn, $data["category"] ?? '');
    $status      = mysqli_real_escape_string($conn, $data["status"] ?? 'pending');

    // FIX: Scheduled at must be NULL, not ""
    $scheduledAt = !empty($data["scheduled_at"]) ? $data["scheduled_at"] : NULL;

    // Image upload
    $imageName = NULL;
    if (isset($file["image"]) && $file["image"]["error"] === 0) {
        $tempName = $file["image"]["tmp_name"];
        $extension = strtolower(pathinfo($file["image"]["name"], PATHINFO_EXTENSION));

        // Valid extensions
        $allowed = ['png', 'jpg', 'jpeg', 'svg'];

        if (!in_array($extension, $allowed)) {
            return [
                "status" => false,
                "message" => "Format gambar tidak valid! Gunakan PNG, JPG, JPEG, atau SVG."
            ];
        }

        $imageName = "IMG_" . time() . "_" . rand(1000, 9999) . "." . $extension;
        $uploadDir = realpath(__DIR__ . "/../../assets/image");

        if (!$uploadDir) {
            $uploadDir = __DIR__ . "/../../assets/image";
            mkdir($uploadDir, 0777, true);
        }

        $fullPath = $uploadDir . "/" . $imageName;

        if (!move_uploaded_file($tempName, $fullPath)) {
            return [
                "status" => false,
                "message" => "Gagal mengupload file gambar."
            ];
        }
    }


    // Query
    $query = "INSERT INTO post 
        (user_id, title, slug, location, date, tags, summary, image, language, category, status, scheduled_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);

    // FIX DATETIME NULL → gunakan "s" tetap
    $stmt->bind_param(
        "isssssssssss",
        $userId,
        $title,
        $slug,
        $location,
        $date,
        $tags,
        $summary,
        $imageName,
        $language,
        $category,
        $status,
        $scheduledAt
    );

    if ($stmt->execute()) {
        return [
            "status" => true,
            "message" => "Post created successfully!",
            "slug" => $slug
        ];
    } else {
        return [
            "status" => false,
            "message" => "Error: " . $stmt->error
        ];
    }
}

// POST FUNCTIONS (LISTING)

// Ambil semua post
function getAllPosts()
{
    global $conn;
    $query = mysqli_query(
        $conn,
        "SELECT * FROM post ORDER BY created_at DESC"
    );
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

// Ambil semua draft
function getDraftPostsByUser($userId)
{
    global $conn;
    $query = mysqli_query(
        $conn,
        "SELECT * FROM post WHERE status = 'draft' AND user_id = $userId ORDER BY created_at DESC"
    );
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

// MENU UNTUK MENGAMBIL POST

// Ambil semua pending post
function getPendingPosts()
{
    global $conn;
    $query = mysqli_query(
        $conn,
        "SELECT * FROM post WHERE status = 'pending' ORDER BY created_at DESC"
    );
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

// Ambil scheduled post
function getScheduledPosts()
{
    global $conn;
    $query = mysqli_query(
        $conn,
        "SELECT * FROM post WHERE status = 'scheduled' ORDER BY scheduled_at ASC"
    );
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

// Ambil satu post berdasarkan ID
function getPublishedPostsByUser($userId)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM post WHERE user_id = $userId AND status = 'published' ORDER BY created_at DESC");
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

// Delete Post
function deletePostById($post_id, $user_id)
{
    global $conn;
    return mysqli_query($conn, "DELETE FROM post WHERE id='$post_id' AND user_id='$user_id'");
}

// Edit Post
function getPostById($id, $user_id)
{
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM post WHERE id='$id' AND user_id='$user_id'");
    return mysqli_fetch_assoc($result);
}

// Update Post
function updatePost($id, $data, $files)
{
    global $conn;

    $title = mysqli_real_escape_string($conn, $data['title']);
    $language = mysqli_real_escape_string($conn, $data['language']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    $slug = mysqli_real_escape_string($conn, $data['slug']);

    // PROSES FILE
    $imageName = $data['old_image']; // dari hidden input untuk keep file lama

    if ($files['image']['name']) {
        $temp = $files['image']['tmp_name'];
        $fileName = time() . "-" . $files['image']['name'];
        move_uploaded_file($temp, "../uploads/" . $fileName);
        $imageName = $fileName;
    }

    $query = "UPDATE post SET 
                title='$title',
                language='$language',
                category='$category',
                slug='$slug',
                image='$imageName'
              WHERE id='$id'";

    $success = mysqli_query($conn, $query);

    if ($success) {
        return [
            "status" => true,
            "slug" => $slug,
            "message" => "Post berhasil diperbarui!"
        ];
    } else {
        return [
            "status" => false,
            "message" => mysqli_error($conn)
        ];
    }
}

// FILTERISASI 
// FUNCTION BUILDER FILTER
function buildFilterQuery($status, $user_id, $language = '', $category = '', $search = '')
{
    $query = "FROM post WHERE status='$status' AND user_id='$user_id'";

    if (!empty($language)) {
        $query .= " AND language='$language'";
    }

    if (!empty($category)) {
        $query .= " AND category='$category'";
    }

    if (!empty($search)) {
        $query .= " AND title LIKE '%$search%'";
    }

    return $query;
}


// GET POST + PAGINATION
function getFilteredPosts($status, $user_id, $limit, $offset, $language = '', $category = '', $search = '')
{
    $baseQuery = buildFilterQuery($status, $user_id, $language, $category, $search);
    $query = "SELECT * $baseQuery ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    return query($query);
}

// GET TOTAL DATA
function getFilteredTotalPosts($status, $user_id, $language = '', $category = '', $search = '')
{
    $baseQuery = buildFilterQuery($status, $user_id, $language, $category, $search);
    $query = "SELECT COUNT(*) as total $baseQuery";
    return query($query)[0]["total"];
}

// Get Trending Posts
function getTrendingPosts()
{
    global $conn;

    $query = "
    SELECT 
      post.id,
      post.title,
      post.category,
      post.summary,
      post.image,
      post.status,
      post.user_id,
      user.name AS author,
      user.avatar
    FROM post
    JOIN user ON post.user_id = user.id
    WHERE post.status = 'published'
    ORDER BY post.created_at DESC
    LIMIT 10
  ";

    $result = mysqli_query($conn, $query);

    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }

    return $posts;
}

function getDataRightSide()
{

    global $conn;

    $query = "
    SELECT 
        post.category,
        post.title,
        post.user_id,
        post.summary,
        post.image,
        post.id,
        user.name,
        user.avatar
    FROM post
    JOIN user ON post.user_id = user.id
    WHERE post.status = 'published'
    ORDER BY post.created_at DESC
    LIMIT 4
    ";

    $result = mysqli_query($conn, $query);

    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }

    return $posts;
}

function getImgYouMayMissed()
{

    global $conn;

    $query = "
    SELECT id, title, image 
    FROM post
    WHERE status = 'published'
    ORDER BY created_at ASC
    LIMIT 3
    ";

    $result = mysqli_query($conn, $query);

    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }

    return $posts;
}

function getLastInsertedId()
{
    global $conn;
    return mysqli_insert_id($conn);
}

// Get img in category
function getGalleryPosts()
{
    global $conn;
    return query("
        SELECT post.id, post.title, post.summary, post.image, post.category, user.name
        FROM post
        JOIN user ON post.user_id = user.id
        WHERE post.status = 'published'
        ORDER BY post.id DESC
        LIMIT 6
    ");
}

function categoryPage()
{
    global $conn;

    $category = isset($_GET['category']) ? $_GET['category'] : '';

    $stmt = $conn->prepare("
        SELECT post.id, post.title, post.summary, post.image, post.category, user.name
        FROM post
        JOIN user ON post.user_id = user.id
        WHERE post.category = ?
        AND post.status = 'published'
        ORDER BY post.id DESC
    ");

    $stmt->bind_param("s", $category);
    $stmt->execute();

    return $stmt->get_result();
}

function searchPostsByCategory($keyword, $category)
{
    global $conn;

    $keyword = "%$keyword%";

    $stmt = $conn->prepare("
        SELECT post.id, post.title, post.summary, post.image, post.category, user.name
        FROM post
        JOIN user ON post.user_id = user.id
        WHERE post.status = 'published'
        AND post.category = ?
        AND post.title LIKE ?
        ORDER BY post.id DESC
    ");

    $stmt->bind_param("ss", $category, $keyword);
    $stmt->execute();

    return $stmt->get_result();
}

function searchAllPosts($keyword)
{
    global $conn;

    $keyword = "%$keyword%";

    $stmt = $conn->prepare("
        SELECT post.id, post.title, post.summary, post.image, post.category, user.name
        FROM post
        JOIN user ON post.user_id = user.id
        WHERE post.status = 'published'
        AND post.title LIKE ?
        ORDER BY post.id DESC
    ");

    $stmt->bind_param("s", $keyword);
    $stmt->execute();

    return $stmt->get_result();
}

// Get All User in User Handler
function getAllUsers($limit, $offset, $search = "")
{
    global $conn;

    $where = "WHERE role = 'author'";

    if (!empty($search)) {
        $safeSearch = "%" . $conn->real_escape_string($search) . "%";
        $where .= " AND (name LIKE '$safeSearch'
                    OR username LIKE '$safeSearch'
                    OR name LIKE '$safeSearch')";
    }

    $query = "
        SELECT id, name, username, avatar, created_at, status, role
        FROM user
        $where
        ORDER BY id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = $conn->query($query);

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            "id"        => $row["id"],
            "avatar"    => $row["avatar"] ?: "default.png",
            "name"      => $row["name"],
            "username"  => $row["username"],
            "role"      => $row["role"],
            "status"    => $row["status"] ?: "active",
            "created_at" => $row["created_at"]
        ];
    }

    return $users;
}


// For Pagination
function countUsers($search = "")
{
    global $conn;

    $where = "WHERE role = 'author'";

    if (!empty($search)) {
        $safeSearch = "%" . $conn->real_escape_string($search) . "%";
        $where .= " AND (
            name LIKE '$safeSearch'
            OR username LIKE '$safeSearch'
            OR name LIKE '$safeSearch'
        )";
    }

    $query = "SELECT COUNT(*) AS total FROM user $where";
    $result = $conn->query($query)->fetch_assoc();

    return $result["total"];
}

function getFilteredPostsAdmin($status, $show, $offset, $language = "", $category = "", $search = "")
{
    global $conn;

    $sql = "SELECT 
                post.id,
                post.title,
                post.image,
                post.language,
                post.category,
                post.created_at,
                user.name AS author_name
            FROM post
            JOIN user ON post.user_id = user.id
            WHERE post.status = ?";

    $params = [$status];
    $types  = "s";

    if (!empty($language)) {
        $sql .= " AND post.language = ?";
        $params[] = $language;
        $types .= "s";
    }

    if (!empty($category)) {
        $sql .= " AND post.category = ?";
        $params[] = $category;
        $types .= "s";
    }

    if (!empty($search)) {
        $sql .= " AND post.title LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }

    $sql .= " ORDER BY post.id DESC LIMIT ? OFFSET ?";
    $params[] = $show;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getFilteredTotalPostsAdmin($status, $language = "", $category = "", $search = "")
{
    global $conn;

    $sql = "SELECT COUNT(*) AS total 
            FROM post 
            JOIN user ON post.user_id = user.id
            WHERE post.status = ?";

    $params = [$status];
    $types  = "s";

    if (!empty($language)) {
        $sql .= " AND post.language = ?";
        $params[] = $language;
        $types .= "s";
    }

    if (!empty($category)) {
        $sql .= " AND post.category = ?";
        $params[] = $category;
        $types .= "s";
    }

    if (!empty($search)) {
        $sql .= " AND post.title LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();
    return (int)$result['total'];
}

function autoPublishScheduledPosts()
{
    global $conn;

    $query = "
        UPDATE post 
        SET status = 'published',
            scheduled_at = NULL
        WHERE status = 'scheduled'
        AND scheduled_at <= NOW()
    ";

    mysqli_query($conn, $query);
}

function getPostsByArchive($year, $month)
{
    global $conn;

    $sql = "
        SELECT 
            post.id,
            post.title,
            post.summary,
            post.image,
            post.category,
            post.created_at,
            user.name
        FROM post
        JOIN user ON post.user_id = user.id
        WHERE post.status = 'published'
          AND YEAR(post.created_at) = ?
          AND MONTH(post.created_at) = ?
        ORDER BY post.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
