<?php
  try{

    // ensure everything is set
    if((!isset($_POST["title"])) || 
    (!isset($_POST["board"])) ||
    (!isset($_POST["user_id"]))){
      header("Location: ../error.php");
      exit();
    }

    $title = $_POST["title"];
    $board = $_POST["board"];
    $author = $_POST["user_id"];
    $created = date("Y/m/d");

    // check if title is empty
    if($title === NULL || strlen($title) <= 0){
      getHeader("petForum");
      // button to send user back to the page they was previously on
      // sourced from: https://getbootstrap.com/docs/4.0/components/buttons/
      echo '<div class="jumbotron text-center"><div class="alert alert-primary" role="alert">
      title cannot be null</div> <a href="../threads.php?board='.htmlspecialchars($board) .'"'. 'class="btn btn-primary">Try again</a></div></div>';
      exit();
    }
    
    // users cannot create without logging in
    if(!isset($_SESSION['id'])){
      getHeader("petForum");
      // button to send user back to the page they was previously on
      // sourced from: https://getbootstrap.com/docs/4.0/components/buttons/
      echo '<div class="jumbotron text-center"><div class="alert alert-primary" role="alert">
      you need to be signed in to do this action</div> <a href="../threads.php?board=' . htmlspecialchars($board) .'"'. 'class="btn btn-primary">Try again</a></div></div>';
      exit();
    }

    // xss patch
    $title = htmlspecialchars($title);


    // create new thread
    if ($stmt = $GLOBALS['database'] -> prepare("INSERT INTO `threads` (`title`, `board`, `author`, `created`) VALUES (?, ?, ?, ?)"))
    {
      $stmt -> bind_param("ssss", $title, $board, $author, $created);
      $stmt -> execute();
      $last_id = $stmt -> insert_id; 
      $stmt -> close();

    }

    // find the thread just created and redirect user into it
    if ($stmt = $GLOBALS['database'] -> prepare("SELECT MAX(`thread_id`) FROM `threads`" ))
    {
      $stmt -> execute();
      $stmt -> bind_result($threadID);
      $stmt -> store_result();
      while ($stmt -> fetch())
      {
          header("Location: ../posts.php?thread=$threadID");
      }
      $stmt -> free_result();
      $stmt -> close();

    }

  }
  catch(Exception){
    header("Location: ../error.php");
    exit();

  }
?>

