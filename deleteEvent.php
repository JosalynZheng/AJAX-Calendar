<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    header("Content-Type: application/json");
    //Connect to database
    require 'database.php';
    //Get userid
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    //post the event_id
    $event_id = htmlentities($_POST['id']);
    
    // Delete event where event_id matches that passed to it by JavaScript function call
    $stmt = $mysqli->prepare("delete from events where event_id=?");
    if(!$stmt){
        echo json_encode(array(
        "success" => false,
        "user_id" => $user_id,
        "message" => "Incorrect Username or Password"
        ));
       
        exit;
    }
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array(
        "success" => true,
        "user_id" => $user_id,
        ));

    exit();
?>


