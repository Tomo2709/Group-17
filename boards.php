<?php
getHeader("PetForum");
?>

<div class="jumbotron text-center">
  <h1>Boards</h1>
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
</div>

<div class="container">
      <h2>Boards</h2>
      <?php
        // get all boards in ascending order
        if ($stmt = $GLOBALS['database'] -> prepare("SELECT `board_id`, `title` FROM `boards` ORDER BY `board_id` ASC "))
          {
            // Run the query
            $stmt -> execute();
            // bind results
            $stmt -> bind_result($boardID, $title);
            // store results in memory 
            $stmt -> store_result();

            // Loop through each board found in the database
            while ($stmt -> fetch())
            {
              // render each board as hyperlinks to respective threads
              echo "<h5><a href='threads.php?board=$boardID'>$title</a></h5><br>";
            }
            // free results stored in memoery
            $stmt -> free_result();
            // close statement
            $stmt -> close();
          }
      ?>
</div>
<!-- we could allow users to create their own boards -->
<?php

getFooter();

?>

