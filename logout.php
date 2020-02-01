<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    header("Content-Type: application/json");
    session_unset();
    if(session_destroy()){
        echo json_encode(
            array("success"=>"true", "user_name"=>"null")
        );
        exit;
    }else{
        echo json_encode(
            array("success"=>"false")
        );
        exit;
    }
?>