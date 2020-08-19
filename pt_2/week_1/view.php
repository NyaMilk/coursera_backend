<?php
require_once 'pdo.php';
session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

$stmt = $dbh->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Милюкова Анастасия Максимовна</title>
</head>

<body>
    <div class="container">
        <h1>Profile information</h1>
        <p>First Name: <?= htmlentities($row['first_name']) ?></p>
        <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
        <p>Email: <?= htmlentities($row['email']) ?></p>
        <p>Headline:<br><?= htmlentities($row['headline']) ?></p>
        <p>Summary:<br><?= htmlentities($row['summary']) ?></p>
        <a href="index.php">Done</a>
    </div>
</body>

</html>