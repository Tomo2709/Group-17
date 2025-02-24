<?php
getHeader("PetForum");
$boardID = $_GET["board"];
$user = 1;
?>

<div class="jumbotron text-center">
  <h1>Threads</h1>
  <p>Logged in as: <?php

    if (isset($_SESSION['id']))
    {
      echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
    }
    else
    {
      echo "Guest";
    }
    
   ?></p>
   <h1>Board : <?php echo $boardID; ?>
</div>

<div class="container">
      <?php
        // get all threads in ascending of date created
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `thread_id`, `threads`.`title`, `users`.`username`, `boards`.`title`, `created` FROM `threads` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `boards` ON `board`=`boards`.`board_id` WHERE `board` = $boardID ORDER BY `created`" ))
          {
            $stmt -> execute();
            $stmt -> bind_result($threadID, $title, $username, $board, $created);
            $stmt -> store_result();

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
          </div>

          <button class="btn btn-primary" formaction="/forms/createThread.php">Create</button>
        </form>
    </div>
</div>

<?php

getFooter();

?>
