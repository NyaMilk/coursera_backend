<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

$stmt = $dbh->query('SELECT * FROM autos');
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
        <h1>Welcome to the Automobiles Database</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
            unset($_SESSION['success']);
        }

        if (!isset($_SESSION['name'])) {
            echo '<p><a href="login.php">Please log in</a></p>' . "\n";
            echo '<p>Attempt to <a href="add.php">add data</a> without logging in</p>' . "\n";
        } else {
            if ($stmt->rowCount() == 0) {
                echo '<p>No Rows Found<p>' . "\n";
            } else {
                echo '<table border="1">' . "\n";
                echo '<thead><tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th>';
                echo '</tr></thead>' . "\n";
                echo '<tbody>' . "\n";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr><td>' . htmlentities($row['make']) . "</td>\n";
                    echo '<td>' . htmlentities($row['model']) . "</td>\n";
                    echo '<td>' . htmlentities($row['year']) . "</td>\n";
                    echo '<td>' . htmlentities($row['mileage']) . "</td>\n";
                    echo '<td>';
                    echo '<a href="edit.php?autos_id=' . htmlentities($row['autos_id']) . '">Edit</a>';
                    echo ' / ';
                    echo '<a href="delete.php?autos_id=' . htmlentities($row['autos_id']) . '">Delete</a>' . "\n";
                    echo '</td></tr>' . "\n";
                }
                echo ('</tbody>' . "\n");
                echo '</table>';
            }
            echo '<p><a href="add.php">Add New Entry</a></p>' . "\n";
            echo '<p><a href="logout.php">Logout</a></p>' . "\n";
            echo '<p><b>Note:</b> Your implementation should retain data across multiple
            logout/login sessions. This sample implementation clears all its
            data periodically - which you should not do in your implementation.</p>' . "\n";
        }
        ?>
    </div>
</body>

</html>