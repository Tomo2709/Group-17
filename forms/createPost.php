<?php
try{
    $message = $_POST["message"];
    $thread = $_POST["thread"];
    $author = $_POST["user_id"];
    $created = date("Y/m/d");

    // vunerable code - create new post and return to the current thread
    $targetDir = "../images/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);

    // if file exists increment until unique
    $increment = 0;
    while (file_exists($targetFile)){
        $increment++;
        $info = pathinfo($targetFile);
        $targetFile = $info["dirname"] . "/" . $info["filename"] . $increment . "." .$info["extension"];
    }

    // uploads the file to directory and database
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    $image = basename($targetFile);   
    $GLOBALS["database"]->query("INSERT INTO `posts` (`message`, `thread`, `author`,`image`, `created`) 
    VALUES ('$message', '$thread', '$author', '$image', '$created')");
    header("Location: ../posts.php?thread=$thread");
}
catch(Exception){
    header("Location: ../error.php");
}

// SQL Injection Patched - create new post and return to the current thread
/*
if ($stmt = $GLOBALS['database'] -> prepare("INSERT INTO `posts` (`message`, `thread`, `author`,`image`, `created`) VALUES (?, ?, ?, ?, ?)"))
{
    $stmt -> bind_param("sssss", $message, $thread, $author, $image, $created);
    $stmt -> execute();
    $last_id = $stmt -> insert_id; 
    $stmt -> close();

    header("Location: ../posts.php?thread=$thread");

}
*/


?>