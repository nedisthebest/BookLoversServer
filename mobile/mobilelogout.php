<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];


    require('../connect.php');

    $user = DB::queryFirstRow(
        "SELECT * FROM users WHERE userid=%i AND token=%s",
        $uid,
        $token
    );

    if (!$user) {
        $response = array("result" => "Error", "message" => "Invalid Credentials");
        echo json_encode($response);
        exit;
    }

    try {
        DB::update(
            'users',
            array(
                'token' => null
            ),
            "userid=%s",
            $uid
        );
    } catch (Exception $e) {
        $response = array("result" => "Error", "message" => "SERVER ERROR" . $e->getCode() . ": " . $e->getMessage());
        echo json_encode($response);
        exit;
    }


    $response = array("result" => "Success");
    echo json_encode($response);
} else {
    $response = array("result" => "Error", "message" => "Field not found");
    echo json_encode($response);
}
