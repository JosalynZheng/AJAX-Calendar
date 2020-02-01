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
    $event_id = htmlentities($_POST["id"]);
    $theTitle = htmlentities($_POST["title"]);
    $theDate = htmlentities($_POST["date"]);
    $theTime = htmlentities($_POST["time"]);
    $theTag = htmlentities($_POST["tag"]);
    $shareWith = htmlentities($_POST["shareWith"]);
    //check if event exits
    if(!empty($event_id)){
        if(!empty($shareWith) && $user_id != $shareWith){

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
            }else{
                echo json_encode(
                    array(
                        "success" => false,
                        "message" => "User doesn't exist, check again!"
                    ));
                exit();
            }
            $stmt->close();
        }

        $stmt = $mysqli->prepare("update events set title = ?, date = ?, time = ?, tag = ? where event_id = ?");
        if(!$stmt){
            echo json_encode(array(
                "success" => false,
                "message" => "Cannot sql"
            ));
            exit;
        }
        $stmt->bind_param('ssssi', $theTitle, $theDate, $theTime, $theTag, $event_id);
        $stmt->execute();
	    $stmt->close();
        echo json_encode(array(
            "success" => true,
            "user_id" => $user_id 
        ));
        exit();
        
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Cannot edit event"
        ));
        exit();
    }

	
	
	
?>
