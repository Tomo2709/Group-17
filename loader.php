<?php
/**
 * Core loader file for PetForum application
 * Initialises database connection, session, and provides layout functions
**/

// csp policy for better xss protection
header("Content-Secruity-Policy: default-src 'self'; script-src 'self' 'nonce-unique'; style-src 'self' 'unsafe-inline'; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");

// Try to start the session to maintain user data across pages
try {
  session_start();
} catch (Exception $e) {
  header("Location: ../error.php");
  exit();
}

/* Database connection with error handling */
try {
  /* Credentials for database */
  $GLOBALS['ip'] = "localhost";
  $GLOBALS['user'] = 'root';
  $GLOBALS['password'] = ''; // TODO: Use a secure password in production
  
  // Attempt database connection
  $GLOBALS['database'] = new mysqli($GLOBALS['ip'], $GLOBALS['user'], $GLOBALS['password'], 'petdb');
  
  // Check if connection was successful
  if ($GLOBALS['database']->connect_error) {
      throw new Exception("Database connection failed");
  }
} catch (Exception $e) {
  header("Location: ../error.php");
  exit();
}

/* Server variables storing important information about where the website is located */
$GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'];
$GLOBALS['home'] = 'http://' . $_SERVER['SERVER_NAME'];

// This function creates the top part of every webpage, including the title and necessary styling tools
function getHeader($title)
{
  ?>
  <!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <title> <?php echo $title;?> </title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
  <body>
  <?php
}

// This function creates the bottom part of every webpage, simply closing the page properly
function getFooter()
{
  echo "</body>";
  echo "</html>";
}

?>
