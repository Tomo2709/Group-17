<?php 
    getHeader("PetForum");

    // Retrieve user input from the search query
    // Using null coalescing operator to avoid undefined index notice
    $input = $_GET['search'] ?? '';
    $searchPattern = "%$input%"; // Format input for SQL LIKE search

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