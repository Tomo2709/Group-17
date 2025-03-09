<?php

    $token =$_POST['_token'];
    $temp = $_SESSION['_token'];
    if ($token !== $temp){ 
        header("Location: ../error.php");
        exit();
    } 

    session_destroy();

    // Redirect to homepage
    header("Location: " . $GLOBALS['home']);

?>
