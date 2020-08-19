<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

$stmt = $dbh->query('SELECT * FROM Profile');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Милюкова Анастасия Максимовна</title>
</head>

<body>
    <div class="container">
        <h1>Anastasia Milyukova's Resume Registry</h1>

        <?php
        // status error & success
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
            unset($_SESSION['success']);
        }

        // login or logout
        if (!isset($_SESSION['name'])) {
            echo '<p><a href="login.php">Please log in</a></p>' . "\n";
        } else {
            echo '<p><a href="logout.php">Logout</a></p>' . "\n";
        }

        // profile table
        if ($stmt->rowCount() == 0) {
            echo 'No Rows Found' . "\n";
        } else {
            echo '<table border="1">' . "\n";
            echo '<thead><tr><th>Name</th><th>Headline</th>';
            if (isset($_SESSION['name'])) {
                echo '<th>Action</th>';
            }
            echo '</tr></thead>' . "\n";
            echo '<tbody>' . "\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr><td>';
                echo '<a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">' . htmlentities($row['first_name']) . " " . htmlentities($row['last_name']) . '</a>' . "\n";
                echo '</td><td>';
                echo $row['headline'] . '</td>';
                if (isset($_SESSION['name'])) {
                    echo '<td>';
                    echo '<a href="edit.php?profile_id=' . htmlentities($row['profile_id']) . '">Edit</a>' . "\n";
                    echo '<a href="delete.php?profile_id=' . htmlentities($row['profile_id']) . '">Delete</a>' . "\n";
                    // echo '<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> ' . '<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
                    echo '</td>' . "\n";
                }
                echo '</tr>';
            }
            echo ('</tbody>' . "\n");
            echo '</table>';
        }

        // add profile
        if (isset($_SESSION['name'])) {
            echo '<p><a href="add.php">Add New Entry</a></p>' . "\n";
        }
        ?>
    </div>
</body>

</html>