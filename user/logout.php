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
  
    session_destroy();

    // Redirect to homepage
    header("Location: " . $GLOBALS['home']);

?>
