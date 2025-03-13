<?php 
    getHeader("PetForum");

    // ensure everything is set
    if(!isset($_POST["search"])){
        header("Location: ../error.php");
        exit();
    }

    // Retrieve user input from the search query
    // Using null coalescing operator to avoid undefined index notice
    $input = $_POST['search'] ?? '';
    $searchPattern = "%$input%"; // Format input for SQL LIKE search

    // Jumbotron Section
    echo '<div class="jumbotron text-center">  
    <p>Logged in as: ';

    if (isset($_SESSION['id']))
    {
      echo $_SESSION['username'] . " (" . $_SESSION['email'] . ")";
      
      // If user is logged in and search input is not empty, save it to search history
      if (!empty($input)) {
          try {
              // Insert current search into history
              $insertQuery = "INSERT INTO `sHistory`(`user`, `input`) VALUES (?, ?)";
              if ($insertStmt = $database->prepare($insertQuery)) {
                  $insertStmt->bind_param("is", $_SESSION['id'], $input);
                  $insertStmt->execute();
                  $insertStmt->close();
                }
          } catch (Exception) {
              // Silently fail
              // we don't want search history errors to break the main functionality
          }
      }
    }
    else
    {
      echo "Guest";
    }
    # test with <script>alert('Injected!');</script>
    echo'</p><h6>Search: ' . htmlspecialchars($input) . '</h6>';
    echo '<a href="../" class="btn btn-primary">Home</a></div></div>';

    // Display Search History for logged-in users
    if (isset($_SESSION['id'])) {
        echo '<div class="container mb-4">';
        echo '<div class="card">';
        echo '<div class="card-header">Recent Searches</div>';
        echo '<div class="card-body">';
        
        try {
            // Query to get the last 5 searches for the current user, ordered by most recent first
            $historyQuery = "SELECT `input` FROM `sHistory` WHERE `user` = ? ORDER BY `search_id` DESC LIMIT 5";
            
            if ($historyStmt = $database->prepare($historyQuery)) {
                $historyStmt->bind_param("i", $_SESSION['id']);
                
                if (!$historyStmt->execute()) {
                    throw new Exception("Error fetching search history");
                }
                
                $historyStmt->bind_result($searchInput);
                $historyStmt->store_result();
                
                if ($historyStmt->num_rows > 0) {
                    echo '<ul class="list-group list-group-flush">';
                    while ($historyStmt->fetch()) {
                        // Display each search term with a link to search for it again
                        echo '<li class="list-group-item">
                        <form method="post" action="search.php">
                            <input type="hidden" name="search" value="' . htmlspecialchars($searchInput) . '">
                            <button type="submit" class="btn btn-link p-0 text-decoration-none">' . htmlspecialchars($searchInput) . '</button>
                        </form>
                        </li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No recent searches found.</p>';
                }
                
                $historyStmt->free_result();
                $historyStmt->close();
            } else {
                throw new Exception("Failed to prepare history query");
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-warning">Search history cannot be displayed.</div>';
        }
        
        echo '</div></div></div>'; // Close card and container
    }
    
    try {
        // Prepare SQL query to fetch posts matching the search term
        // This query joins three tables: posts, users, and threads
        $query = "SELECT 
                    `post_id`, `threads`.`title`, `users`.`username`, 
                    `posts`.`created`, `image`, `message` 
                  FROM `posts` 
                  INNER JOIN `users` ON `author` = `users`.`user_id` 
                  INNER JOIN `threads` ON `thread` = `threads`.`thread_id` 
                  WHERE `threads`.`title` LIKE ?";

        // Prepare the SQL statement to prevent SQL injection
        if ($stmt = $database->prepare($query)) {
            // Bind the search parameter to the prepared statement
            $stmt->bind_param("s", $searchPattern);
            
            // Execute the prepared statement
            if (!$stmt->execute()) {
                throw new Exception("Error executing search query: " . $stmt->error);
            }
            
            // Bind result variables to columns in the result set
            $stmt->bind_result($postID, $title, $username, $created, $image, $message);
            
            // Store all results in memory for processing
            $stmt->store_result();
            
            // Get number of results
            $resultCount = $stmt->num_rows;

            echo "<div class='container'>";

            if ($resultCount == 0) {
                // Display a message if no results were found
                echo "<div class='alert alert-info'>No posts found matching your search term. Try different keywords.</div>";
            } else {
                // Fetch and display results
                while ($stmt->fetch()) {
                    echo "
                    <div class='card mb-4 box-shadow'>
                        <img class='card-img-top' src='../images/" . htmlspecialchars($image, ENT_QUOTES) . "' 
                             alt='Post Image' style='width: 250px; height: 250px;'>
                        <div class='card-body'>
                            <p class='card-text'>" . htmlspecialchars($username, ENT_QUOTES) . ": " . 
                            htmlspecialchars($message, ENT_QUOTES) . "</p>
                            <small class='text-muted'>" . htmlspecialchars($created, ENT_QUOTES) . "</small>
                        </div>
                    </div>";
                }
            }

            echo "</div>"; // Close container div

            // Free the result and close statement
            $stmt->free_result();
            $stmt->close();
        } else {
            // Handle statement preparation failure
            throw new Exception("Failed to prepare search query: " . $database->error);
        }
    } catch (Exception $e) {
        header("Location: ../error.php");
        exit();
    }

    getFooter();
?>
