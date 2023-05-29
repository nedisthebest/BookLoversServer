<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token']) && isset($_POST['meetingid']) && isset($_POST['body']) && isset($_POST['rating']) && isset($_POST['title'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];
    $meetingid = $_POST['meetingid'];
    $body = $_POST['body'];
    $rating = $_POST['rating'];
    $title = $_POST['title'];

    require('../connect.php');

    try {
        $clubid = DB::queryFirstField("SELECT clubid FROM meetings WHERE meetingid=%s", $meetingid);

        $ismember = DB::query("SELECT * FROM clubmembership c JOIN users u ON u.userid=c.userid WHERE u.userid=%s AND token=%s AND c.clubid=%s", $uid, $token, $clubid);

        if (!$ismember) {
            $response = array("result" => "Error", "message" => "You are not a member of this club");
            echo json_encode($response);
            exit;
        }

        DB::insert(
            'reviews',
            array(
                'userid' => $uid,
                'meetingid' => $meetingid,
                'rating' => $rating,
                'body' => $body,
                'title' => $title
            )
        );
    } catch (Exception $e) {
        if ($e->getCode() == 1062) {
            $response = array("result" => "Error", "message" => "Review already added");
            echo json_encode($response);
            exit;
        }
        $response = array("result" => "Error", "message" => "SERVER ERROR" . $e->getCode() . ": " . $e->getMessage());
        echo json_encode($response);
        exit;
    }


    $response = null;

    $response = array("result" => "Success");
    echo json_encode($response);
} else {
    $response = array("result" => "Error", "message" => "Field not found");
    echo json_encode($response);
}
