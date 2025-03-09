<?php

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($stmt = $GLOBALS['database'] -> prepare("SELECT user_id, username, email, password FROM users WHERE BINARY username = ? AND email = ?")) {
                
        $stmt -> bind_param("ss", $username, $email); 
        $stmt -> execute();
        $stmt -> bind_result($uId, $uNam, $mail, $hasPasswrd);
        $stmt -> store_result();
        
        while ($stmt -> fetch()){

            if (password_verify($password, $hasPasswrd)) {

                $_SESSION['id'] = $uId;
                $_SESSION['username'] = $uNam;
                $_SESSION['email'] = $mail;

                $_SESSION['SingUpStatus'] = "All Good!";

            } else {
                $_SESSION['SignUpStatus'] = "Wrong Password!";

            }
        }
        
    }

    header("Location: " . $GLOBALS['home']);
    $stmt ->close();

?>
