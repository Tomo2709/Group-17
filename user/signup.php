<?php


    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);



    try{
    // email verification----------------------------------------------------------------------------------------
        if (strlen($email) < 1 || strlen($email) > 100){
            $_SESSION['SignUpStatus'] = "Your email is either too long or too short!";
            header("Location: " . $GLOBALS['home']);
            die();

        } else if (strpos($email, '@') == False){
            $_SESSION['SignUpStatus'] = "Your email is invalid!";
            header("Location: " . $GLOBALS['home']);
            die();       

        }

        if (preg_match("/[^A-Za-z0-9\_\.-!$\?\^%#~@]/", $password)){ // Flags any html related character in this space
            $_SESSION['SignUpStatus'] = "Invalid characters found, please remove.";
            header("Location: " . $GLOBALS['home']);
            die();    

        }

        // username check [Nothing here for unprotected ver]---------------------------------------------------------

        if (strlen($username) < 1 || strlen($username) > 100){
            $_SESSION['SignUpStatus'] = "Your username is either too long or too short!";
            header("Location: " . $GLOBALS['home']);
            die();

        }

        if (preg_match("/[^A-Za-z0-9\-_!$\?\^%#~]/", $password)){ // Flags any html related character in this space
            $_SESSION['SignUpStatus'] = "Invalid characters found, please remove.";
            header("Location: " . $GLOBALS['home']);
            die();    

        }
        
        // password check--------------------------------------------------------------------------------------------
        
        if (strlen($password) < 6 || strlen($password) > 20){ // Passwods should be at least 6 char long.
            $_SESSION['SignUpStatus'] = "Your password is either too long or too short!";
            header("Location: " . $GLOBALS['home']);
            die();  

        } else if (preg_match("/[^A-Za-z0-9\-!$\?\^%#~]/", $password)){
            $_SESSION['SignUpStatus'] = "Invalid Password,use these symbols only: -!$?^%#~";
            header("Location: " . $GLOBALS['home']);
            die();    

        }

        // prepare passwrd ------------------------------------------------------------------------------------------
        $hasPasswrd = password_hash($password, PASSWORD_DEFAULT); // Hashing Password.

        //-----------------------------------------------------------------------------------------------------------
        // duplicate check-------------------------------------------------------------------------------------------

        $stmt = $GLOBALS['database'] -> prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt -> bind_param("ss", $username, $email);
        $stmt -> execute();

        // obtain result
        $result = $stmt -> get_result();

        // check for duplicates
        if ($result -> num_rows > 0) {
            $_SESSION['SignUpStatus'] = "Email or username being used already.";
            header("Location: " . $GLOBALS['home']);
            die();
        }

        $stmt -> close();

        // insert values---------------------------------------------------------------------------------------------

        if ($stmt = $GLOBALS['database'] -> prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)")) {
            
            $stmt -> bind_param("sss", $username, $email, $hasPasswrd);
            $stmt -> execute();
            $lastID = $stmt -> insert_id;

            $_SESSION['id'] = $lastID;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            $_SESSION['SignUpStatus'] = "All Good!";
            header("Location: " . $GLOBALS['home']);

            $stmt ->close();
        }

    } catch (Exception){
        header("Location: " . $GLOBALS['home'] . "/error.php");
    }
?>