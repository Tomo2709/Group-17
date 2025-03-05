<?php
try{
  $title = $_POST["title"];
  $board = $_POST["board"];
  $author = $_POST["user_id"];
  $created = date("Y/m/d");

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

