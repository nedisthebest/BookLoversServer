<?php
/* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];
    if (isset($_POST['meetingid'])) {
        $meetingid = $_POST['meetingid'];
    } else {
        $response = array("result" => "Error", "message" => "Field not found");
        echo json_encode($response);
        exit;
    }
    require('../connect.php');
    try {
        $clubid = DB::queryFirstField("SELECT clubid FROM meetings WHERE meetingid=%s", $meetingid);

        $ismember = DB::query("SELECT * FROM clubmembership c JOIN users u ON u.userid=c.userid WHERE u.userid=%s AND token=%s AND c.clubid=%s", $uid, $token, $clubid);

        if (!$ismember) {
            $response = array("result" => "Error", "message" => "You are not a member of this club");
            echo json_encode($response);
            exit;
        }
        $reviews = DB::query("SELECT userid, rating, body, title FROM reviews WHERE meetingid=%s", $meetingid);

        $averageratings = DB::queryFirstField("SELECT AVG(rating) FROM reviews WHERE meetingid=%s", $meetingid);

        $response = array("result" => "Success", "reviews" => $reviews, "average" => $averageratings);
        echo json_encode($response);
    } catch (Exception $e) {
        if ($e->getCode() == 1062) {
            $response = array("result" => "Error", "message" => "Book already added");
            echo json_encode($response);
            exit;
        }
        $response = array("result" => "Error", "message" => "SERVER ERROR" . $e->getCode() . ": " . $e->getMessage());
        echo json_encode($response);
        exit;
    }
} else {
    $response = array("result" => "Error", "message" => "Invalid Credentials");
    echo json_encode($response);
    exit;
}
