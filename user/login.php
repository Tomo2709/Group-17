<?php

    // csrf token check
    if(isset($_POST['_token']) && (isset($_SESSION['_token']))){
        $token =$_POST['_token'];
        $temp = $_SESSION['_token'];
        if ($token !== $temp){ 
        header("Location: ../error.php");
        exit();
        }
    }
    
    if(!isset($_POST['_token']) || (!isset($_SESSION['_token']))){
        header("Location: ../error.php");
        exit();
    }
  
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);



    if ($stmt = $GLOBALS['database'] -> prepare("SELECT user_id, username, email, password FROM users WHERE BINARY username = ? AND email = ?")) {
                
        $stmt -> bind_param("ss", $username, $email); 
        $stmt -> execute();
        $stmt -> bind_result($uId, $uNam, $mail, $hasPasswrd);
        $stmt -> store_result();
        


        while ($stmt -> fetch()){
            $nam = $uNam;

            if ($username == $uNam){
                echo "Yes it is the same";

                if (password_verify($password, $hasPasswrd)) { // Checks if the password is correct

                    $_SESSION['id'] = $uId;
                    $_SESSION['username'] = $uNam;
                    $_SESSION['email'] = $mail;

                    $_SESSION['SignUpStatus'] = "All Good!";

                } else {
                    $_SESSION['SignUpStatus'] = "Wrong Password!";

                }

                header("Location: " . $GLOBALS['home']);
                $stmt ->close();
                die();
            }

        } 
        
        $_SESSION['SignUpStatus'] = "Incorrect email or username!";
        header("Location: " . $GLOBALS['home']);
        $stmt ->close();
    } 

?>
