<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
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

$stmt = $dbh->prepare('SELECT year, description FROM Position WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($positions === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

$stmt = $dbh->prepare('SELECT * FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :pid ORDER BY year');
$stmt->execute(array(':pid' => $_GET['profile_id']));
$schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($schools === false) {
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
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
        <?php
        if (isset($schools[0])) {
            echo "<p>Education</p><ul>" . "\n";
            foreach ($schools as $school) {
                echo '<li>' . htmlentities($school['year']) . ': ' . htmlentities($school['name']) . '</li>';
            }
            echo '</ul>' . "\n";
        }
        if (isset($positions[0])) {
            echo "<p>Position</p><ul>" . "\n";
            foreach ($positions as $position) {
                echo '<li>' . htmlentities($position['year']) . ': ' . htmlentities($position['description']) . '</li>';
            }
            echo '</ul>' . "\n";
        }
        ?>
        <p><a href="index.php">Done</a></p>
    </div>
</body>

</html>