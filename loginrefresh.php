<?php
    require 'database.php';
    ini_set("session.cookie_httponly", 1);
    session_start();
    header("Content-Type: application/json");
    $array = array("success" => false, "message" => "Login Please.");
    if(isset($_SESSION['token']) == false){
        echo json_encode($array);
        exit;
    }
    //If success
    $username = $_SESSION['user_name'];
    $stmt = $mysqli->prepare("select count(*) from users where name = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch();
    //mysqli_num_rows(mysqli_query($mysqli, "select name from users where name = '$username'" )) == 0
    if ($cnt == 0){
        echo json_encode(array("success"=>false));
        exit;
    }else{
        echo json_encode(array("success"=>true,));
        exit;
    }
?>