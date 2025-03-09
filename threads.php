<?php
// generate csrf token
$_SESSION['_token'] = bin2hex(random_bytes(16));

try{
  getHeader("PetForum");

  // sanatize html characters
  $boardID = htmlspecialchars($_GET["board"]);

  // check if board is an integer
  try{
    $boardID = (int)$boardID;
  }
  catch(Exception){
    header("Location: ../error.php");
    exit();
  }

  // check to see if the ID exists
  if ($stmt = $GLOBALS['database'] ->prepare("SELECT * FROM `boards` WHERE `board_id`=?")){
    $stmt ->bind_param("s", $boardID);
    $stmt ->execute();
    $stmt ->bind_result($boardID, $title);
    $stmt ->store_result();
    while ($stmt -> fetch()){
      echo '<div class="jumbotron text-center">
      <h1>Threads</h1>
      <p>Logged in as: ';

      if (isset($_SESSION['id']))
      {
        echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
      }
      else
      {
        echo "Guest";
      }

      echo '</p> <h1>Board : '. htmlspecialchars($title) . '</div>';
    }
  }

  // if not a recognised ID redirect user and quit script
  if($stmt-> num_rows == 0){
      header("Location: ../error.php");
      exit();
  }

  $stmt -> free_result();
  $stmt -> close();


  // we do not have a user login set up yet
  $user = 1;
  ?>

  <div class="container">
        <?php
          // get all threads in ascending of date created
          if ($stmt = $GLOBALS['database'] -> prepare("SELECT `thread_id`, `threads`.`title`, `users`.`username`, `boards`.`title`, `created` FROM `threads` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `boards` ON `board`=`boards`.`board_id` WHERE `board` = ? ORDER BY `created`" ))
            {
              $stmt -> bind_param("s", $boardID);
              $stmt -> execute();
              $stmt -> bind_result($threadID, $title, $username, $board, $created);
              $stmt -> store_result();

              // xss filtering
              $title = htmlspecialchars($title);
              $username = htmlspecialchars($username);
              
              while ($stmt -> fetch())
              {
                echo "<h5><a href='posts.php?thread=$threadID'>title: $title author: $username created: $created</a></h5><br>";
              }

              $stmt -> free_result();
              $stmt -> close();
            }
        ?>
        <!-- form for creating a thread -->
        <div class="col-sm-4">
        <h3>create Thread</h3>
          <form method="POST" onkeydown="return event.key != 'Enter';"> <!-- Prevents submitting form on [ENTER] -->

            <div class="form-group">
              <label for="title">title:</label>
              <input type="text" class="form-control" id="title" name="title">
              <input type="hidden" name="board" value="<?php echo $boardID; ?>"/>
              <input type="hidden" name="user_id" value="<?php echo $user; ?>"/>
              <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>"/>
            </div>

            <button class="btn btn-primary" formaction="/forms/createThread.php">Create</button>
          </form>
      </div>
  </div>

  <?php

  getFooter();
          }
catch(Exception){
  header("Location: ../error.php");
  exit();
}

?>
