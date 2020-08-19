<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

$stmt = $dbh->prepare('SELECT * FROM autos WHERE autos_id = :aid');
$stmt->execute(array(':aid' => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['autos_id'])) {
    if ($row['autos_id'] != $_POST['autos_id']) {
        $_SESSION['error'] = 'Could not load profile';
        header('Location: index.php');
        return;
    }
    $stmt = $dbh->prepare('DELETE FROM autos WHERE autos_id = :aid');
    $stmt->execute(array(':aid' => $_POST['autos_id']));
    $_SESSION['success'] = 'Record deleted';
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
        <form method="post">
            <p>Confirm: Deleting <?= htmlentities($row['make']) ?></p>
            <input type="hidden" name="autos_id" value="<?= htmlentities($row['autos_id']) ?>">
            <input type="submit" name="delete" value="Delete">
            <a href="index.php">Cancel</a>
        </form>
    </div>
</body>

</html>