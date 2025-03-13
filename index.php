<?php

// Include the header for the webpage
getHeader("PetForum");
?>

<!-- Jumbotron Section -->
<div class="jumbotron text-center">
  <h1>PetForum Homepage</h1>
  <p>Welcome to the PetForum Homepage</p>
  <!-- relfect currently logged in user, if not logged in show guest -->
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
  <!-- Search bar that allows users to find threads by title -->

  <div class="search-container">
    <form action="search.php" method="POST">
      <input type="text" placeholder="Search For Thread Title..." name="search">
    </form>
  </div>
</div>



<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <h2>Boards</h2>
      <?php

        // Prepare a database query to get top 5 boards, sorted by board_id
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `board_id`, `title` FROM `boards` ORDER BY `board_id` ASC LIMIT 5"))
        {
          // Run the query
          $stmt -> execute();
          // Store the results in variables ($boardID and $title)
          $stmt -> bind_result($boardID, $title);
          // Get all results
          $stmt -> store_result();
          
          // Loop through each board found in the database
          while ($stmt -> fetch())
          {
            // For each board, create a link that goes to threads.php with the board ID
            echo "<h5><a href='threads.php?board=$boardID'>$title</a></h5><br>";
          }
          // Clean up by freeing the result and closing the statement
          $stmt -> free_result();
          $stmt -> close();
        }
      ?>
      <a href="boards.php" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">View all Boards</a>
      </div>

      <div class="col-sm-4">
        <h3>Recent activity</h3>
        <?php
          // get recent posts order by post_id
          if ($stmt = $GLOBALS['database'] -> prepare("SELECT  `threads`.`title`, `users`.`username`, `posts`.`message`, `posts`.`created` FROM `posts` INNER JOIN `threads` ON `posts`.`thread` = `threads`.`thread_id` INNER JOIN `users` ON `posts`.`author` = `users`.`user_id` ORDER BY  `posts`.`post_id` ASC LIMIT 5"))
          {
            $stmt -> execute();
            $stmt -> bind_result($title, $author, $message, $created);
            $stmt -> store_result();

            while ($stmt -> fetch())
            {
              echo "<p>" . "[".$title."]" ." ". $author . " : " .  $message . " " . " ". $created . "</p>";
            }

            $stmt -> free_result();
            $stmt -> close();
          }
        ?>
        <a href="recent.php" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">View all recent posts</a>
      </div>

      <!-- log in stuff right here -->
      <div class="col-sm-4">
        <?php
          if (isset($_SESSION['SignUpStatus'])) {
            echo $_SESSION['SignUpStatus'];
            unset($_SESSION['SignUpStatus']);
          }

          if (isset($_SESSION['id'])){
            echo "<h3>Account Actions</h3>";

          }
          else {
            echo "<h3>Login/signup</h3>";
          }
        ?>

        <form method="POST" onkeydown="return event.key != 'Enter';"> <!-- Prevents submitting form on [ENTER] -->

          <?php
          if (isset($_SESSION['id'])){
            echo'<button class="btn btn-primary" formaction="/user/logout.php">Logout</button>';

          }
          else{

            echo'<div class="form-group">
            <label for="username">Username:</label>
            <input type="username" class="form-control" id="username" name="username">
            </div>';

            echo'<div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="email">
            </div>';

            echo '<div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
            </div>';

            echo'<button class="btn btn-primary" formaction="/user/signup.php">Signup</button>';
            echo'<button class="btn btn-primary" formaction="/user/login.php">Login</button>';
          
          }
          ?>

        </form>
      </div>
    </div>
  </div>
</div>

<?php

  getFooter();

?>
