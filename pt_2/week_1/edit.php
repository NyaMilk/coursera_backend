<?php
require_once 'pdo.php';
session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

$stmt = $dbh->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All values are required';
        header('Location: edit.php?profile_id=' . $_POST['profile_id']);
        return;
    } else {
        $stmt = $dbh->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid');
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => $_POST['profile_id']
        ));
        $_SESSION['success'] = 'Profile updated';
        header('Location: index.php');
        return;
    }
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
        <h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        ?>
        <form method="post">
            <p>First Name: <input type="text" name="first_name" size="60" value="<?= htmlentities($row['first_name']) ?>"></p>
            <p>Last Name: <input type="text" name="last_name" size="60" value="<?= htmlentities($row['last_name']) ?>"></p>
            <p>Email: <input type="email" name="email" size="30" value="<?= htmlentities($row['email']) ?>"></p>
            <p>Headline:<br><input type="text" name="headline" size="80" value="<?= htmlentities($row['headline']) ?>"></p>
            <p>Summary:<br><textarea name="summary" rows="8" cols="80"><?= htmlentities($row['summary']) ?></textarea></p>
            <p>
                <input type="hidden" name="profile_id" value="<?= htmlentities($row['profile_id']) ?>">
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

</html>