<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('Name parameter missing');
}

if (isset($_POST['logout'])) {
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);
    header('Location: index.php');
    return;
}

if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) == 0) {
        $_SESSION['error'] = 'Make is required';
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = 'Mileage and year must be numeric';
    } else {
        $stmt = $dbh->prepare('INSERT INTO autos (make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage']
        ));
        $_SESSION['success'] = 'Record inserted';
    }
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
        <h1>Tracking Autos for <?= $_GET['name'] ?></h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
            unset($_SESSION['success']);
        }
        ?>
        <form method="post">
            <p>Make: <input type="text" name="make" size="60"></p>
            <p>Year: <input type="text" name="year"></p>
            <p>Mileage: <input type="text" name="mileage"></p>
            <p>
                <input type="submit" value="Add">
                <input type="submit" name="logout" value="Logout">
            </p>
        </form>
        <h2>Automobiles</h2>
        <?php
        echo "<ul>\n";
        foreach ($positions as $position) {
            echo '<li>' . htmlentities($position['year']) . ' ' . htmlentities($position['make']) . ' / ' . htmlentities($position['mileage']) . '</li>';
        }
        echo "</ul>\n";
        ?>
    </div>
</body>

</html>