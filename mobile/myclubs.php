<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];

    require('../connect.php');

    $clubs = DB::query('SELECT * FROM clubs c JOIN clubmembership m ON c.clubid = m.clubid JOIN users u ON m.userid = u.userid WHERE u.userid = %s AND u.token = %s', $uid, $token);

    $response = null;

    $response = array("result" => "Success", "clubs" => $clubs);
    echo json_encode($response);
} else {
    $response = array("result" => "Error", "message" => "Incorrect token");
    echo json_encode($response);
}
