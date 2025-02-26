<?php

$message = $_POST["message"];
$thread = $_POST["thread"];
$author = $_POST["user_id"];
$created = date("Y/m/d");

// need file upload stuff here
$targetDir = "../images/";
$targetFile = $targetDir . basename($_FILES["image"]["name"]);
move_uploaded_file($_FILES["image"]["tmp_name"],$targetFile);
$image = basename($_FILES["image"]["name"]);   

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