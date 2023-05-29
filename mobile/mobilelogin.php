<?php
/* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['email'])) {
    //attempted a login

    //USE ERROR LOG TO GET ERRORS
    //error_log("Error message", 3, "../error.log");

    //retrieve and cleanup user input
    $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
    $password = hash("sha256", $_POST['password']); //hash the password

    //handshake with db
    require('../connect.php');

    //go get nominated users data
    $row = DB::queryFirstRow('SELECT * FROM users WHERE email = %s', $email);



    $response = null;

    if (!$row) {
        //nothing came back, therefore username not registered
        $response = array("result" => "Error", "message" => "No user found!");
        echo json_encode($response);
    } else {
        //got a live user, let's check password
        if ($password == $row['password']) {


            //generate authentication token
            $token = hash("sha256", $email . $password . time());
            DB::query("UPDATE `users` SET `token`=%s, `lastaccess`=NOW() WHERE `email`=%s", $token, $email);

            $response = array("result" => "Success", "firstname" => $row['firstname'], "lastname" => $row['lastname'], "userid" => $row['userid'], "token" => $token); //password correct, return success

            echo json_encode($response); //return response to mobile
        } else {
            $response = array("result" => "Error", "message" => "Password incorrect!");
            echo json_encode($response); //return response to mobile
        }
    }
} else {
    $response = array("result" => "Error", "message" => "Email field not found!");
    echo json_encode($response);
}
