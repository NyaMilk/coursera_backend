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

$stmt = $dbh->prepare('SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid');
$stmt->execute(array(':pid' => $_GET['profile_id'], ':uid' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . $_POST['profile_id']);
        return;
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . $_POST['profile_id']);
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . $_POST['profile_id']);
        return;
    }

    $stmt = $dbh->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_POST['profile_id'],
        ':uid' => $_SESSION['user_id']
    ));

    $stmt = $dbh->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    insertPos($dbh, $_GET['profile_id']);

    $stmt = $dbh->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    insertEdu($dbh, $_GET['profile_id']);

    $_SESSION['success'] = 'Profile updated';
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
        <h1>Editing Profile for <?= htmlentities($_SESSION['name']) ?></h1>
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
            <p>Education: <input type="submit" id="addEdu" value="+">
                <div id="edu_fields">
                    <?php
                    $schools = loadEdu($dbh, $_GET['profile_id']);

                    foreach ($schools as $school) {
                        echo '<div id="edu' . htmlentities($school['rank']) . '">' . "\n";
                        echo '<p>Year: <input type="text" name="edu_year' . htmlentities($school['rank']) . '"' . ' value="' . htmlentities($school['year']) . '">' . "\n";
                        echo '<input type="button" value="-" ' . 'onclick="$(\'#edu' . htmlentities($school['rank']) . '\').remove(); return false;"></p>' . "\n";
                        echo '<p>School: <input type="text" name="edu_school' . htmlentities($school['rank']) . '" class="school" size="80" value="' . htmlentities($school['name']) . '">' . "\n";
                        echo '</div>' . "\n";
                    }
                    ?>
                </div>
            </p>
            <p>Position: <input type="submit" id="addPos" value="+">
                <div id="position_fields">
                    <?php
                    $positions = loadPos($dbh, $_GET['profile_id']);

                    foreach ($positions as $position) {
                        echo '<div id="position' . htmlentities($position['rank']) . '">' . "\n";
                        echo '<p>Year: <input type="text" name="year' . htmlentities($position['rank']) . '"' . ' value="' . htmlentities($position['year']) . '">' . "\n";
                        echo '<input type="button" value="-" ' . 'onclick="$(\'#position' . htmlentities($position['rank']) . '\').remove(); return false;"></p>' . "\n";
                        echo '<textarea name="desc' . htmlentities($position['rank']) . '" rows="8" cols="80">' . htmlentities($position['description']) . '</textarea>' . "\n";
                        echo '</div>' . "\n";
                    }
                    ?>
                </div>
            </p>
            <p>
                <input type="hidden" name="profile_id" value="<?= htmlentities($row['profile_id']) ?>">
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>

<script>
    countEdu = <?= count($schools) ?>;
    countPos = <?= count($positions) ?>;

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
                <p>Year: <input type="text" name="year' + countPos + '" value=""> \
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

        $('.school').autocomplete({
            source: "school.php"
        });
    });
</script>

</html>