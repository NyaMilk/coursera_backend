<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

$stmt = $dbh->prepare('SELECT profile_id, first_name, last_name FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    if ($row['profile_id'] != $_POST['profile_id']) {
        $_SESSION['error'] = 'Could not load profile';
        header('Location: index.php');
        return;
    }
    $stmt = $dbh->prepare('DELETE FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
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
        <h1>Deleteing Profile</h1>
        <form method="post">
            <p>First Name: <?= $row['first_name'] ?></p>
            <p>Last Name: <?= $row['last_name'] ?></p>
            <input type="hidden" name="profile_id" value="<?= htmlentities($row['profile_id']) ?>">
            <input type="submit" name="delete" value="Delete">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>