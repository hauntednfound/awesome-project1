<?php
session_start();
include __DIR__ . '/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $errors = [];

    if (empty($first_name)) {
        $errors[] = "Vārds ir obligāts.";
    } elseif (strlen($first_name) < 3) {
        $errors[] = "Vārds jābūt vismaz 3 simbolus garam.";
    }

    if (empty($last_name)) {
        $errors[] = "Uzvārds ir obligāts.";
    } elseif (strlen($last_name) < 3) {
        $errors[] = "Uzvārds jābūt vismaz 3 simbolus garam.";
    }

    if (empty($email)) {
        $errors[] = "E-pasts ir obligāts.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Nepareizs e-pasta formāts.";
    } else {
        $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "E-pasts jau ir reģistrēts.";
        }
        $stmt->close();
    }

    if (empty($password)) {
        $errors[] = "Parole ir obligāta.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Parole jābūt vismaz 8 simbolus garai.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, password, registered_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<p>Reģistrācija veiksmīga!</p>";
        } else {
            echo "<p>Kļūda reģistrējoties: " . $stmt->error . "</p>";
        }
        $stmt->close();
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
    <label for="first_name">Vārds:</label>
    <input type="text" id="first_name" name="first_name" >
    <br>
    <label for="last_name">Uzvārds:</label>
    <input type="text" id="last_name" name="last_name" >
    <br>
    <label for="email">E-pasts:</label>
    <input type="email" id="email" name="email" >
    <br>
    <label for="password">Parole:</label>
    <input type="password" id="password" name="password" >
    <br>
    <button type="submit">Reģistrēties</button>
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
