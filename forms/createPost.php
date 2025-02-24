<?php

$message = $_POST["message"];
$thread = $_POST["thread"];
$author = $_POST["user_id"];
$image = $_POST["image"];
$created = date("Y/m/d");

// need file upload stuff here

// create new post and return to the current thread
if ($stmt = $GLOBALS['database'] -> prepare("INSERT INTO `posts` (`message`, `thread`, `author`,`image`, `created`) VALUES (?, ?, ?, ?, ?)"))
{
    $stmt -> bind_param("sssss", $message, $thread, $author, $image, $created);
    $stmt -> execute();
    $last_id = $stmt -> insert_id; 
    $stmt -> close();

    header("Location: ../posts.php?thread=$thread");

}



?>