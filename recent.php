<?php
getHeader("PetForum");
?>

<div class="jumbotron text-center">
  <h1>recent posts</h1>
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
      <?php
        // get all posts in ascending of date
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `post_id`, `threads`.`title`, `users`.`username`, `posts`.`created` , `image`, `message` FROM `posts` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `threads` ON `thread`=`threads`.`thread_id`ORDER BY `created`" ))
          {
            $stmt -> execute();
            $stmt -> bind_result($postID, $title, $username, $created, $image, $message);
            $stmt -> store_result();

            while ($stmt -> fetch())
            {
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

            $stmt -> free_result();
            $stmt -> close();
          }
      ?>
</div>

<?php

getFooter();

?>
