<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db/post.php';
$posts = getPosts();

$following = rand(1, 100);
$followers = rand(1000, 100000);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Mājas lapa</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: white;
            font-family: sans-serif;
        }
        .sidebar {
            width: 20%;
            padding: 20px;
            background-color: #1e1e1e;
        }
        .main-content {
            width: 60%;
            padding: 20px;
        }
        .post-feed {
            width: 20%;
            padding: 20px;
            background-color: #1e1e1e;
            max-height: 80vh;
            overflow-y: auto;
        }
        .post-form textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            resize: vertical;
        }
        .post-form button {
            margin-top: 10px;
            padding: 10px;
            background-color: #2979ff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 8px;
        }
        .post {
            background-color: #2a2a2a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            position: relative;
        }
        .delete-post-form {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .delete-post-form button {
            background-color: #ff4b4b;
            border: none;
            color: white;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .delete-post-form button:hover {
            background-color: #ff0000;
        }
        .profile-pic {
            width: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
            display: block;
        }
        .logout-button {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 12px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .logout-button:hover {
            background-color: #c9302c;
        }
        .delete-post {
            background-color: transparent;
            border: none;
            color: #ff5555;
            font-size: 14px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 5px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .delete-post:hover {
            background-color: #ff5555;
            color: #1e1e1e;
        }
        .post .author-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 10px;
            object-fit: cover;
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .upload-profile-pic-form {
            margin-top: 15px;
        }
        .upload-profile-pic-form input[type="file"] {
            display: block;
            margin-top: 5px;
        }
        .upload-profile-pic-form button {
            margin-top: 8px;
            padding: 6px 12px;
            background-color: #2979ff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 6px;
        }
        .upload-profile-pic-form button:hover {
            background-color: #1c54b2;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Profils</h2>
    <img class="profile-pic" src="<?php
        echo isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic'])
            ? htmlspecialchars($_SESSION['profile_pic'])
            : 'https://i1.sndcdn.com/avatars-rrOYOzvqTcBsWhhy-uQzF8Q-t240x240.jpg';
    ?>" alt="Profile Picture">

    <p>Lietotājvārds: <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></p>
    <p>Sekotāji: <?php echo $followers; ?></p>
    <p>Seko: <?php echo $following; ?></p>

    <form class="upload-profile-pic-form" action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
        <label for="profile_pic_upload">Augšupielādēt profila attēlu:</label>
        <input type="file" name="profile_pic" id="profile_pic_upload" accept="image/*" required>
        <button type="submit">Augšupielādēt</button>
    </form>

    <a href="logout.php" class="logout-button">Iziet</a>
</div>

<div class="main-content">
    <h1>Laipni lūdzam!</h1>
    <p>Šī ir jūsu galvenā siena.</p>
</div>

<div class="post-feed">
    <form class="post-form" method="post" action="db/submit_post.php">
        <textarea name="content" placeholder="Raksti kaut ko..." rows="4"></textarea>
        <button type="submit">Publicēt</button>
    </form>

    <h3>Jaunākie ieraksti</h3>
    <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="post">
            <div class="post-header">
                <img
                    src="<?php echo !empty($row['profile_pic'])
                        ? htmlspecialchars($row['profile_pic'])
                        : 'https://i1.sndcdn.com/avatars-rrOYOzvqTcBsWhhy-uQzF8Q-t240x240.jpg'; ?>"
                    alt="User Profile Pic"
                    class="author-pic"
                >
                <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
            </div>
            <small><?php echo $row['created_at']; ?></small>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

            <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                <form class="delete-post-form" method="post" action="db/delete_post.php" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo ierakstu?');">
                    <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Dzēst</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>