<?php
require_once 'pdo.php';
require_once 'util.php';
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $stmt = $dbh->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ));

    $profile_id = $dbh->lastInsertId();

    insertEdu($dbh, $profile_id);
    insertPos($dbh, $profile_id);

    $_SESSION['success'] = 'Profile added';
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
        <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
            unset($_SESSION['error']);
        }
        ?>
        <form method="post">
            <p>First Name: <input type="text" name="first_name" size="60"></p>
            <p>Last Name: <input type="text" name="last_name" size="60"></p>
            <p>Email: <input type="email" name="email" size="30"></p>
            <p>Headline:<br><input type="text" name="headline" size="80"></p>
            <p>Summary:<br><textarea name="summary" rows="8" cols="80"></textarea></p>
            <p>Education: <input type="submit" id="addEdu" value="+">
                <div id="edu_fields"></div>
            </p>
            <p>Position: <input type="submit" id="addPos" value="+">
                <div id="position_fields"></div>
            </p>
            <p>
                <input type="submit" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

<script>
    countPos = 0;
    countEdu = 0;

    $(document).ready(function() {
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event) {
            event.preventDefault();
            if (countPos >= 9) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position " + countPos);
            $('#position_fields').append(
                '<div id="position' + countPos + '"> \
                <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
                <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove(); countPos--; return false;"></p> \
                <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
                <br><br> \
                </div>');
        });

        $('#addEdu').click(function(event) {
            event.preventDefault();
            if (countEdu >= 9) {
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log("Adding education " + countEdu);
            $('#edu_fields').append(
                '<div id="edu' + countEdu + '"> \
                <p>Year: <input type="text" name="edu_year' + countEdu + '" value=""> \
                <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove(); countEdu--; return false;"><br>\
                <p>School: <input type="text" name="edu_school' + countEdu + '" class="school" size="80" value="">\
                </p></div>'
            );

            $('.school').autocomplete({
                source: "school.php"
            });
        });
    });
</script>

</html>