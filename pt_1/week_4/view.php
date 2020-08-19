<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

$stmt = $dbh->query('SELECT make, year, mileage FROM autos');
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>Tracking Autos for <?= htmlentities($_SESSION['email']) ?></h1>
        <?php
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
            unset($_SESSION['success']);
        }
        ?>
        <h2>Automobiles</h2>
        <?php
        echo "<ul>\n";
        foreach ($positions as $position) {
            echo '<li>' . htmlentities($position['year']) . ' ' . htmlentities($position['make']) . ' / ' . htmlentities($position['mileage']) . '</li>';
        }
        echo "</ul>\n";
        ?>
        <p>
            <a href="add.php">Add New</a>
            |
            <a href="logout.php">Logout</a>
        </p>
    </div>
</body>

</html>