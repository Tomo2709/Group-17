<?php
// generate csrf token
$_SESSION['_token'] = bin2hex(random_bytes(16));

  getHeader("PetForum");

  // sanatize user input
  $threadID = htmlspecialchars($_GET["thread"]);

  // check if integer
  try{
    $threadID = (int)$threadID;
  }
  catch(Exception){
    header("Location: ../error.php");
    exit();
  }

  // check if threadID exists
  if ($stmt = $GLOBALS['database'] ->prepare("SELECT * FROM `threads` WHERE `thread_id`= ? ")){
    $stmt -> bind_param("s", $threadID);
    // Execute the query to get thread information
    $stmt ->execute();
    // Bind the results to these variables
    $stmt ->bind_result($threadID, $title, $board, $author, $created);
    // Store all results
    $stmt ->store_result();

    // Fetch and display the thread information
    while ($stmt -> fetch()){
      echo '<div class="jumbotron text-center">
      <h1>Posts</h1>
      <p>Logged in as: ';

      // Check if user is logged in and display appropriate user info
      if (isset($_SESSION['id']))
      {
        // Display username and email for logged-in users
        echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
      }
      else
      {
        // Display "Guest" for users who aren't logged in
        echo "Guest";
      }

      // Check if no threads were found with the given ID
      echo '</p> <h1>Thread : '. $title . '</div>';
    }
  }
  // if threadID doesnt exist redirect
  if($stmt-> num_rows == 0){
      header("Location: ../error.php");
      exit();
  }
  // ISSUE: Hardcoded user ID                       ISSUE ISSUE ISSUE ISSUE ISSUE ISSUE ISSUE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  // This sets a fixed user ID regardless of who is actually logged in
  $user = 1;

  ?>

  <div class="container">
        <?php
          // Query to get all posts ascending of date created
          // This query joins multiple tables to get post details, thread title, and author info
          if ($stmt = $GLOBALS['database'] -> prepare("SELECT `post_id`, `threads`.`title`, `users`.`username`, `posts`.`created` , `image`, `message` FROM `posts` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `threads` ON `thread`=`threads`.`thread_id` WHERE `thread` = ? ORDER BY `created`" ))
            {
              // bind the thread ID parameter
              $stmt -> bind_param("s", $threadID);
              // execute the query
              $stmt -> execute();
              // Bind the results to these variables
              $stmt -> bind_result($postID, $title, $username, $created, $image, $message);
              // Store the results
              $stmt -> store_result();

              while ($stmt -> fetch())
              // Output HTML for each post with the post details
              {
                echo "
                <div class=container>
                <div class='card mb-4 box-shadow'>
                  <img class='card-img-top' src='../images/$image' data-holder-rendered='true' style='width: 250px;height : 250px;'>
                  <div class='card-body'>
                    <p class='card-text'>$username: $message</p>
                      <small class='text-muted'>$created</small>
                    </div>
                  </div>
                </div>
              ";
              }
              echo "</div>";
              // Free resources and close the statement
              $stmt -> free_result();
              $stmt -> close();
            }
        ?>
  </div>

  <!-- form for creating a post -->
  <div class="col-sm-4">
        <h3>create post</h3>
          <form method="POST" enctype="multipart/form-data" return event.key != 'Enter';"> <!-- Prevents submitting form on [ENTER] -->

            <div class="form-group">
              <label for="message">message:</label>
              <input type="text" class="form-control" id="message" name="message">
              <input type="hidden" name="thread" value="<?php echo $threadID; ?>"/>
              <input type="hidden" name="user_id" value="<?php echo $user; ?>"/>
              <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>"/>
            </div>
            <div class="form-group">
              <label for="image">image:</label>
              <input type="file" class="form-control" id="image" name="image">
            </div>

            <button class="btn btn-primary" formaction="/forms/createPost.php">Create</button>
          </form>
      </div>

  <?php

  getFooter();
  
?>
