<?php
require_once 'pdo.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

$salt = 'XyZzy12*_';
if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = 'Email and password are required';
        header('Location: login.php');
        return;
    }
    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $dbh->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header('Location: index.php');
        return;
    } else {
        $_SESSION['error'] = 'Incorrect password';
        header('Location: login.php');
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
        <h1>Please Log In</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        ?>
        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input id="email" type="email" name="email">
            <br>
            <label for="pass">Password</label>
            <input id="passwd" type="password" name="pass">
            <br>
            <input type="submit" onclick="return doValidate();" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </form>
        <p>For a password hint, view source and find an account and password hint in the HTML comments.</p>
        <!-- Hint: 
        The account is umsi@umich.edu
        The password is php123.
        -->
    </div>
</body>

<script>
    function doValidate() {
        console.log('Validating...');
        try {
            addr = document.getElementById('email').value;
            pw = document.getElementById('passwd').value;
            console.log("Validating addr=" + addr + " pw=" + pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (addr.indexOf('@') == -1) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
</script>


</html>