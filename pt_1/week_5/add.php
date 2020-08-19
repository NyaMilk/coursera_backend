<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) == 0 || strlen($_POST['model']) == 0 || strlen($_POST['year']) == 0 || strlen($_POST['mileage']) == 0) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: add.php');
        return;
    } elseif (!is_numeric($_POST['year'])) {
        $_SESSION['error'] = 'Year must be numeric';
        header('Location: add.php');
        return;
    } elseif (!is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = 'Mileage must be numeric';
        header('Location: add.php');
        return;
    } else {
        $stmt = $dbh->prepare('INSERT INTO autos (make, model, year, mileage) VALUES ( :mk, :md, :yr, :mi)');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':md' => $_POST['model'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage']
        ));
        $_SESSION['success'] = 'Record added';
        header("Location: index.php");
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
        <h1>Tracking Automobiles for <?= htmlentities($_SESSION['email']) ?></h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        ?>
        <form method="post">
            <p>Make: <input type="text" name="make" size="40"></p>
            <p>Model: <input type="text" name="model" size="40"></p>
            <p>Year: <input type="text" name="year" size="10"></p>
            <p>Mileage: <input type="text" name="mileage" size="10"></p>
            <p>
                <input type="submit" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

</html>