<?php
// set user
if(isset($_SESSION['id'])){
  $user = $_SESSION['id'];
}

// generate csrf token
$_SESSION['_token'] = bin2hex(random_bytes(16));

  getHeader("PetForum");
  
  // isset check
  if(!isset($_GET["board"])){
    header("Location: ../error.php");
    exit();
  }

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
    // Prepare the SQL statement to prevent SQL injection
    $stmt ->bind_param("s", $boardID);
    // Execute the prepared statement
    $stmt ->execute();

    // Bind the result columns to variables
    $stmt ->bind_result($boardID, $title);
    // Store all results in memory
    $stmt ->store_result();

    // Fetch and display the board information
    while ($stmt -> fetch()){
      echo '<div class="jumbotron text-center">
      <h1>Threads</h1>
      <p>Logged in as: ';
      // Check if a user is logged in
      if (isset($_SESSION['id']))
      {
        echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
      }
      else
      {// Otherwise show as guest
        echo "Guest";
      }

      echo '</p> <h1>Board : '. $title . '</h1>';
      echo '<a href="../" class="btn btn-primary">Home</a></div>';
    }
  }

  // if not a recognised ID redirect user and quit script
  if($stmt-> num_rows == 0){
      header("Location: ../error.php");
      exit();
  }

  // Clean up database resources
  $stmt -> free_result();
  $stmt -> close();

  ?>

  <div class="container">
        <?php
          // get all threads in ascending of date created
          if ($stmt = $GLOBALS['database'] -> prepare("SELECT `thread_id`, `threads`.`title`, `users`.`username`, `boards`.`title`, `created` FROM `threads` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `boards` ON `board`=`boards`.`board_id` WHERE `board` = ? ORDER BY `created`" ))
            {
              // Query the database for all threads in this board, ordered by creation date
              $stmt -> bind_param("s", $boardID);
              // Execute the query
              $stmt -> execute();
              // Bind the result columns to variables
              $stmt -> bind_result($threadID, $title, $username, $board, $created);
              // Store all results in memory
              $stmt -> store_result();

              // Loop through each thread and display it as a link // Loop through each thread and display it as a link
              while ($stmt -> fetch())
              {
                // Create a link to each thread showing its title, author and creation date
                echo "<h5><a href='posts.php?thread=$threadID'>title: $title author: $username created: $created</a></h5><br>";
              }

              // Clean up database resources
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

?>
