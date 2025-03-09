<?php
getHeader("PetForum");

?>

<div class="jumbotron text-center">
  <h1>PetForum Homepage</h1>
  <p>Welcome to the PetForum Homepage</p>
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
</div>

<div class="container">
    <div class="row">
    <div class="col-sm-4">
      <h2>Boards</h2>
      <?php
        // get all boards in ascending order
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `board_id`, `title` FROM `boards` ORDER BY `board_id` ASC LIMIT 10"))
          {
            $stmt -> execute();
            $stmt -> bind_result($boardID, $title);
            $stmt -> store_result();

            while ($stmt -> fetch())
            {
              echo "<h5><a href='threads.php?board=$boardID'>$title</a></h5><br>";
            }

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
              $stmt -> bind_result($threadTitle, $author, $message, $created);
              $stmt -> store_result();

              while ($stmt -> fetch())
              {
                echo "<p>" . "[".$threadTitle."]" ." ". $author . " : " .  $message . " " . " ". $created . "</p>";
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
      ?>
      <h3>Login/signup</h3>
        <form method="POST" onkeydown="return event.key != 'Enter';"> <!-- Prevents submitting form on [ENTER] -->

          <div class="form-group">
            <label for="username">Username:</label>
            <input type="username" class="form-control" id="username" name="username">
          </div>

          <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="email">
          </div>

          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
          </div>

          <!-- There are several default colours in Bootstrap CSS accessed by calling the class
               See: https://getbootstrap.com/docs/4.0/utilities/colors/#color -->
          <button class="btn btn-primary" formaction="/user/signup.php">Signup</button>
          <button class="btn btn-primary" formaction="/user/login.php">Login</button>
          <button class="btn btn-primary" formaction="/user/logout.php">Logout</button>
          <!-- <button class="btn btn-primary" formaction="">Account</button> nah, not needed -->
        </form>
    </div>
  </div>
</div>
</div>


<?php

getFooter();

?>
