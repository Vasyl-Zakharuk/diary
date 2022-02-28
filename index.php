<?php

    session_start();

    $error = "";

    if (array_key_exists("logout", $_GET)) {
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        // $_COOKIE("id") = "";
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
        header("Location: loggedinpage.php");
    }

    if (array_key_exists("submit", $_POST)) {
        include("connection.php");
        
        if (!$_POST['email']) {
            $error .= "An email address is required<br>";
        }
        if (!$_POST['password']) {
            $error .= "A password is required<br>";
        }
        if ($error != "") {
            $error = "<p>There were error(s) in your form:</p>".$error;
        } else {
            if ($_POST['signUp'] == '1') {
            $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result) > 0) {
                $error = "That email address is taken";
            } else {
                $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";
                if (!mysqli_query($link, $query)) {
                    $error = "<p>Could not sign you up - please try again later</p>";
                } else {
                    $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";
                    mysqli_query($link, $query);
                    $_SESSION['id'] = mysqli_insert_id($link);
                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);
                    }
                    header("Location: loggedinpage.php");
                }
            } 
        } else {
            $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                $hashedPassword = md5(md5($row['id']).$_POST['password']);
                if ($hashedPassword == $row['password']) {
                    $_SESSION['id'] = $row['id'];
                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", $row['id'], time() + 60*60*24*365);
                    }
                    header("Location: loggedinpage.php");
                } else {
                    $error = "That email/password could not be found";
                }
            } else {
                $error = "That email/password could not be found";
            }
        }
        }
    }
?>

<?php include("header.php"); ?>


  <body>
    
    <div class="container" id="homePageContainer">
        <h1>Secret Diary</h1>

        <p><strong>Store your troughts permanently and securerly</strong></p>

        <div id="error"><?php if ($error != "") {
            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>'; 
        } ?></div>

        <form method="post" style="margin-bottom: 20px" id="signUpForm">
            <p>Interested? Sign up now</p>
            <div class="mb-3">
                <input class="form-control" type="email" name="email" placeholder="Your Email">
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" name="password" placeholder="Password">
            </div>
            <div class="mb-3">
                <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1>
                Stay logged in
            </div>
            <div class="mb-3">
                <input type="hidden" name="signUp" value="1">
            </div>
            <input class="btn btn-success" type="submit" name="submit" value="Sign Up!">
            <p><a class="btn toggleForms" style="color: blue">Log in</a></p>
        </form>

        <form method="post" id="logInForm">
            <p>Log in using your username and password</p>
            <div class="mb-3">
                <input class="form-control" type="email" name="email" placeholder="Your Email">
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" name="password" placeholder="Password">
            </div>
            <div class="mb-3">
                <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1>
                Stay logged in
            </div>
            <div class="mb-3">
                <input type="hidden" name="signUp" value="0">
            </div>
            <input class="btn btn-success" type="submit" name="submit" value="Log In!">
            <p><a class="btn toggleForms" style="color: blue">Sign up</a></p>
        </form>

    </div>

    <?php include("footer.php"); ?>

    
  </body>

