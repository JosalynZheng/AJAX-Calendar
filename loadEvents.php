<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    header("Content-Type: application/json");
    //Connect to database
    require 'database.php';
    $user_id = $_SESSION['user_id'];

    if ($user_id != null){
        $user_id = $_SESSION['user_id'];      
        $user_name = $_SESSION['user_name'];
        $stmt = $mysqli->prepare("select event_id, title, date, time, tag from events where user_id = ?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
            echo json_encode(array(
              "success" => false,
              "exists" => false,
              "message" => $mysqli->error,
            ));
            exit;
        }
        $stmt->bind_param('i', $user_id);      
        $stmt->execute();       
        $stmt->bind_result($event_id, $theTitle, $theDate, $theTime, $theTag);
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = array();
        // Make an array of all the resulting events that will be included in the jsonData
        // that is passed back
            while($row = $result->fetch_assoc()){
            array_push($data, array(
                "event_id" => htmlentities($row['event_id']),
                "title" => htmlentities($row['title']),
                "date" => htmlentities($row['date']),
                "time" => htmlentities($row['time']),             
                "tag" => htmlentities($row['tag'])
            ));
            }
            //database ok!
            echo json_encode(array(
                "success" => true,
                "exists" => true,
                "events" => $data
            ));
            $stmt->close();
         exit;
        
        } else
        {
            echo json_encode(array(
                "success" => true,
                "exists" => false
            ));
            exit;
        }
    } else
    {
        echo json_encode(array(
            "success" => false,
            "exists" => false
        ));
        exit;
    }

    ?>