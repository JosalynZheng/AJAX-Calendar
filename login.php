<?php
    ini_set("session.cookie_httponly", 1);
    require 'database.php';
    header("Content-Type: application/json");
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    //store username&password
    $username = htmlentities($json_obj['username']);
    $password = htmlentities($json_obj['password']);
    $stmt = $mysqli->prepare("select count(*), user_id, password from users where name = ?");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($cnt, $id, $password_fromSQL);
    $stmt->fetch();
    
    // If this is the user, let user login
    $bool = password_verify($password, $password_fromSQL);
    if($cnt == 1 && password_verify($password, $password_fromSQL)){
        session_start();
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $username;
        echo json_encode(array(
            "success" => true,
             "user_name" => $username,
             "user_id" => $id,//id may cause problem
             "token"=> $_SESSION['token']
        ));
        exit;
    }
    else{
        echo json_encode(array(
        "success" => false,
        "message" => "Password correct"
        ));
        exit;
    }
?>   