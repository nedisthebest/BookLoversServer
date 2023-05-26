<?php
/* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['email']) && isset($_POST['fname']) && isset($_POST['sname']) && isset($_POST['password'])) {
    //registration attempt
    //go get posted shiz and clean it up or clobber it
    $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
    $firstname = trim(stripslashes(htmlspecialchars($_POST['fname'])));
    $lastname = trim(stripslashes(htmlspecialchars($_POST['sname'])));
    $password = $_POST['password'];
    if (isset($_POST['pcode'])) {
        $postcode = trim(stripslashes(htmlspecialchars($_POST['pcode'])));
    } else {
        $postcode = "";
    }

    //handshake with db
    require('../connect.php');

    //check to see if username is used
    $row = DB::queryFirstRow('select * from users where email = %s', $email);
    $response = null;

    if (!$row) {

        DB::insert(
            'users',
            array(
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'postcode' => $postcode,
                'password' => hash("sha256", $password),
            )
        );

        $userID = DB::queryFirstField('SELECT userid FROM users WHERE email = %s', $email);

        $response = array("result" => "Success"); //password correct, return success

        echo json_encode($response); //return response to mobile
    } else {
        $response = array("result" => "Error", "message" => "User Already Exists!");
        echo json_encode($response); //return response to mobile
    }
} else {
    $response = array("result" => "Error", "message" => "Email field not found!");
    echo json_encode($response);
}
