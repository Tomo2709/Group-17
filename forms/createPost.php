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

try{

    // ensure everything is set
    if((!isset($_POST["message"])) || 
    (!isset($_POST["thread"])) ||
    (!isset($_POST["user_id"]))){
        header("Location: ../error.php");
        exit();
    }

    $message = $_POST["message"];
    $thread = $_POST["thread"];
    $author = $_POST["user_id"];
    $created = date("Y/m/d");

    // check if message is empty
    if($message === NULL || strlen($message) <= 0){
        getHeader("petForum");
        // button to send user back to the page they was previously on
        echo '<div class="jumbotron text-center"><div class="alert alert-primary" role="alert">
        message cannot be null</div> <a href="../posts.php?thread=' . htmlspecialchars($thread) .'"'. 'class="btn btn-primary">Try again</a></div></div>';
        
        exit();
    }

    // users cannot create without logging in
    if(!isset($_SESSION['id'])){
        getHeader("petForum");
        // button to send user back to the page they was previously on
        echo '<div class="jumbotron text-center"><div class="alert alert-primary" role="alert">
        you need to be signed in to do this action</div> <a href="../posts.php?thread=' . htmlspecialchars($thread) .'"'. 'class="btn btn-primary">Try again</a></div></div>';
        
        exit();
    }

    // set image directory
    $targetDir = "../images/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);

    // if file exists increment until unique
    $increment = 0;
    while (file_exists($targetFile)){
        $increment++;
        $info = pathinfo($targetFile);
        $targetFile = $info["dirname"] . "/" . $info["filename"] . $increment . "." .$info["extension"];
    }

    // prevent xss
    $message = htmlspecialchars($message);
    $targetFile = htmlspecialchars($targetFile);

    // uploads the file to directory and database
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    $image = basename($targetFile);   

// SQL Injection Patched - create new post and return to the current thread
if ($stmt = $GLOBALS['database'] -> prepare("INSERT INTO `posts` (`message`, `thread`, `author`,`image`, `created`) VALUES (?, ?, ?, ?, ?)"))
{
    $stmt -> bind_param("sssss", $message, $thread, $author, $image, $created);
    $stmt -> execute();
    $last_id = $stmt -> insert_id; 
    $stmt -> close();

    header("Location: ../posts.php?thread=$thread");

}
}
catch(Exception){
    header("Location: ../error.php");
    exit();
}
?>