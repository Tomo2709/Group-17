<?php
getHeader("PetForum");
?>

<div class="jumbotron text-center">
  <h1>Boards</h1>
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
      <h2>Boards</h2>
      <?php
        // get all boards in ascending order
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `board_id`, `title` FROM `boards` ORDER BY `board_id` ASC "))
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
</div>
<!-- we could allow users to create their own boards -->
<?php

getFooter();

?>

