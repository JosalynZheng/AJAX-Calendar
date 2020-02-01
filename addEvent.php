<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    header("Content-Type: application/json");
    //Connect to database
    require 'database.php';
    //Get userid

    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    //store username&password

    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];

    //Get story_id
    $share_id = htmlentities($_POST["shareWith"]);
    $theTitle = htmlentities($_POST["title"]);
    $theDate = htmlentities($_POST["date"]);
    $theTime = htmlentities($_POST["time"]);
    $theTag = htmlentities($_POST["tag"]);

    if(isset($theTitle) && isset($theDate) && isset($theTime) && !empty($theTitle) && !empty($theDate) && !empty($theTime)){
        if(!empty($share_id) && $share_id != $user_id){
            $stmt = $mysqli->prepare("select user_id from users where user_id = '$shareWith'");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('i',$shareWith);
            $stmt->execute();

            $result = $stmt->get_result();
            //exist
            if($result->fetch_assoc() != null){
                $sql = "INSERT INTO events (user_id, title, date, time, tag) VALUES(?, ?, ?, ?, ?);";
                $stmt1 = $mysqli->prepare($sql);
                if(!$stmt1){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt1->bind_param('issss', $shareWith, $theTitle, $theDate, $theTime, $theTag);
                $stmt1->execute();
                $stmt1->close();
            }
            $stmt->close();
        }
        $sql = "INSERT INTO events (user_id, title, date, time, tag) VALUES(?, ?, ?, ?, ?);";
        $stmt = $mysqli->prepare($sql);
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        
        $stmt->bind_param('issss', $user_id, $theTitle, $theDate, $theTime, $theTag);
        $stmt->execute();
        $stmt->close();
            
        echo json_encode(array(
            "success" => true,
            "user_id"=> $user_id
        ));
        exit();
        
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Cannot create event"
        ));
        exit();
    }
   
      
        
        

    