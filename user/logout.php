<?php

session_destroy();

// Redirect to homepage
header("Location: " . $GLOBALS['home']);

?>
