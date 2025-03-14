<?php
getHeader("PetForum");
?>

<div class="jumbotron text-center">
  <h1>recent posts</h1>
  <p>Logged in as: <?php
    // Check if user is logged in by looking for a session ID
    if (isset($_SESSION['id']))
    {
      // Display the username and email for logged-in users
      echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
    }
    else
    {
      // Display "Guest" for users who aren't logged in
      echo "Guest</p>";
    }
    
   echo '<div><a href="../" class="btn btn-primary">Home</a></div>';
  ?>
</div>

<div class="container">
  <?php
    // Query to get all posts in ascending order of creation date
    // This joins posts table with users and threads tables to get complete post information        
    if ($stmt = $GLOBALS['database'] -> prepare("SELECT `post_id`, `threads`.`title`, `users`.`username`, `posts`.`created` , `image`, `message` FROM `posts` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `threads` ON `thread`=`threads`.`thread_id`ORDER BY `created`" ))
    {
      // Execute the query
      $stmt -> execute();
      // Bind the results to these variables
      $stmt -> bind_result($postID, $title, $username, $created, $image, $message);
      // Store all results
      $stmt -> store_result();

      // Loop through each post and display it
      while ($stmt -> fetch())
      {
        // Output HTML for each post with the post details
        // Each post is displayed as a card with the image, username, message, and timestamp
        // sourced from: https://getbootstrap.com/docs/4.0/examples/album/
        echo "
        <div class=container>
        <div class='card mb-4 box-shadow'>
          <img class='card-img-top' data-src='$image' data-holder-rendered='true'>
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

<?php

getFooter();

?>
