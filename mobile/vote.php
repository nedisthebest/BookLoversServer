<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token']) && isset($_POST['bid']) && isset($_POST['clubid'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];
    $bid = $_POST['bid'];
    $clubid = $_POST['clubid'];
    if (isset($_POST['vote'])) {
        $vote = $_POST['vote'];
    } else {
        $vote = 1;
    }
    if (abs($vote) > 1 || !($vote % 1 == 0)) {
        $response = array("result" => "Error", "message" => "Invalid vote");
        echo json_encode($response);
        exit;
    }

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

    $isMember = DB::query(
        "SELECT * FROM clubmembership WHERE userid=%s AND clubid=%s",
        $uid,
        $clubid
    );

    if (!$isMember) {
        $response = array("result" => "Error", "message" => "User is not a member of the club");
        echo json_encode($response);
        exit;
    }


    try {
        if (DB::query("SELECT * FROM votes WHERE userid = %s AND bookid = %s AND clubid = %s", $uid, $bid, $clubid)) {
            if ($vote == 0) {
                DB::delete(
                    'votes',
                    "userid=%s AND bookid=%s AND clubid=%s",
                    $uid,
                    $bid,
                    $clubid
                );
                $response = array("result" => "Success");
                echo json_encode($response);
                exit;
            }
            DB::update(
                'votes',
                array(
                    'vote' => $vote
                ),
                "userid=%s AND bookid=%s AND clubid=%s",
                $uid,
                $bid,
                $clubid
            );
            $response = array("result" => "Success");
            echo json_encode($response);
            exit;
        }
        DB::insert(
            'votes',
            array(
                'userid' => $uid,
                'bookid' => $bid,
                'clubid' => $clubid,
                'vote' => $vote
            )
        );
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


    $response = null;

    $response = array("result" => "Success");
    echo json_encode($response);
} else {
    $response = array("result" => "Error", "message" => "Field not found");
    echo json_encode($response);
}
