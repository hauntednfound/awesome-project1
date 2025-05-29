<?php
session_start();
include __DIR__ . '/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $errors = [];

    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $errors[] = "Šāds e-pasts netiek atrasts.";
    } else {
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            $errors[] = "Nepareiza parole.";
        }
    }

    if (empty($errors)) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['profile_pic'] = !empty($user['profile_pic']) ? $user['profile_pic'] : null;
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<form method="POST" action="">
    <label for="email">E-pasts:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="password">Parole:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Ielogoties</button>
</form>

<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}
?>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
