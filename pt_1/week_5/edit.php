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

$stmt = $dbh->prepare('SELECT * FROM autos WHERE autos_id = :aid');
$stmt->execute(array(':aid' => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) == 0 || strlen($_POST['model']) == 0 || strlen($_POST['year']) == 0 || strlen($_POST['mileage']) == 0) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: edit.php?autos_id=' . $_POST['autos_id']);
        return;
    } elseif (!is_numeric($_POST['year'])) {
        $_SESSION['error'] = 'Year must be numeric';
        header('Location: edit.php?autos_id=' . $_POST['autos_id']);
        return;
    } elseif (!is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = 'Mileage must be numeric';
        header('Location: edit.php?autos_id=' . $_POST['autos_id']);
        return;
    } else {
        $stmt = $dbh->prepare('UPDATE autos SET make = :mk, model = :md, year = :yr, mileage = :mi WHERE autos_id = :aid');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':md' => $_POST['model'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage'],
            ':aid' => $_POST['autos_id']
        ));
        $_SESSION['success'] = 'Record updated';
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
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <title>Милюкова Анастасия Максимовна</title>
</head>

<body>
    <div class="container">
        <h1>Editing Automobile</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        ?>
        <form method="post">
            <p>Make: <input type="text" name="make" size="40" value="<?= htmlentities($row['make']) ?>"></p>
            <p>Make: <input type="text" name="model" size="40" value="<?= htmlentities($row['model']) ?>"></p>
            <p>Year: <input type="text" name="year" size="10" value="<?= htmlentities($row['year']) ?>"></p>
            <p>Mileage: <input type="text" name="mileage" size="10" value="<?= htmlentities($row['mileage']) ?>"></p>
            <p>
                <input type="hidden" name="autos_id" value="<?= htmlentities($row['autos_id']) ?>">
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

</html>