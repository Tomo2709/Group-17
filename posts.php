<?php
try{
getHeader("PetForum");
$threadID = $_GET["thread"];
if ($stmt = $GLOBALS['database'] ->prepare("SELECT * FROM `threads` WHERE `thread_id`= $threadID ")){
  $stmt ->execute();
  $stmt ->bind_result($threadID, $title, $board, $author, $created);
  $stmt ->store_result();
}
if($stmt-> num_rows == 0){
    header("Location: ../error.php");
    exit();
}

$user = 1;

?>

<div class="jumbotron text-center">
  <h1>posts</h1>
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
   <h1>thread : <?php echo $title; ?></h1>
</div>

<div class="container">
      <?php
        // get all posts in ascending of date created
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `post_id`, `threads`.`title`, `users`.`username`, `posts`.`created` , `image`, `message` FROM `posts` INNER JOIN `users` ON `author` = `users`.`user_id` INNER JOIN `threads` ON `thread`=`threads`.`thread_id` WHERE `thread` = $threadID ORDER BY `created`" ))
          {
            $stmt -> execute();
            $stmt -> bind_result($postID, $title, $username, $created, $image, $message);
            $stmt -> store_result();

            while ($stmt -> fetch())
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
}
catch(Exception){
    header("Location: ../error.php");
    exit();
}
?>
