<?php
    ini_set("session.cookie_httponly", 1);
    require 'database.php';
    header("Content-Type: application/json");
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    //store username&password
    $username = htmlentities($json_obj['username']);
    $password = htmlentities($json_obj['password']);
    //insert
    $stmt = $mysqli->prepare("select name from users where name = ?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $result = $stmt->get_result();
    //exist
    if($result->fetch_assoc() != null){
        //echo "This username has already existed! Change one please~";
        $stmt->close();
        echo json_encode(array(
            "success" => false,
            "message" => "Username Exists. Register Failed. Please try again!"
        ));
        exit;
    }// not! then insert to database and get the Session_id
    else{
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        //Insert query store it
        $stmt = $mysqli->prepare("insert into users (name, password) values (?, ?)");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
               
        $stmt->bind_param('ss', $username, $password_hashed);
        $stmt->execute();
        $stmt->close();

        if(!$stmt){
            echo json_encode(array(
                "success" => false,
            ));
            exit;
        }else{
            $stmt = $mysqli->prepare("select user_id from users where name = ?");

        $stmt->bind_param('s', $username);

        $stmt->execute();

        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){
            $_SESSION['user_id'] = $row["user_id"];
            $id = $_SESSION['user_id'];
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            //$stmt->close();
        }
            echo json_encode(
                array(
                    "success" => true,
                    "message" => "success!",
                    "user_name" => $username,
                    "user_id" => $id,
                    "token" => $_SESSION['token'],
                ));
            exit;
        }
    }
    
?>