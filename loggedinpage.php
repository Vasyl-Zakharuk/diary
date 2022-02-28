<?php 

    session_start();

    $diaryContent = "";

    if (array_key_exists("id", $_COOKIE) && $_COOKIE['id']) {
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if (array_key_exists("id", $_SESSION) && $_SESSION['id']) {
        include("connection.php");
        $query = "SELECT diary FROM `users` WHERE id = ".mysqli_real_escape_string($link, $_SESSION['id'])." LIMIT 1";
        $row = mysqli_fetch_array(mysqli_query($link, $query));
        $diaryContent = $row['diary'];
    } else {
        header("Location: index.php");
    }

    include("header.php");

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Diary</a>

    <div>
        <div>
            <a href='index.php?logout=1'><button class="btn btn-outline-success" type="submit">Logout</button></a>
        </div>
    </div>
  </div>
</nav>

<div class="container-fluid" id="containerLoggedInPage">
    <textarea id="diary" rows="20%"><?php echo $diaryContent; ?></textarea>
</div>

<?php

    include("footer.php");

?>