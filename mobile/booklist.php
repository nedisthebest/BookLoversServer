<?php
/* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['userid']) && isset($_POST['token']) && isset($_POST['clubid'])) {
    $uid = $_POST['userid'];
    $token = $_POST['token'];
    $clubid = $_POST['clubid'];

    $APIKEY = 'AIzaSyAtShlOPeIM000Kz6GBARjyiD4rhwR1DnA';

    require('../connect.php');

    if (!DB::query('SELECT * FROM clubmembership c JOIN users u ON c.userid = u.userid WHERE u.userid = %s AND u.token = %s AND c.clubid = %s', $uid, $token, $clubid)) {
        $response = array("result" => "Error", "message" => "You are not a member of this club");
        echo json_encode($response);
        exit;
    }

    try {
        $books = DB::query('SELECT DISTINCT bookid, SUM(vote) as `vote` FROM votes WHERE clubid = %s AND bookid NOT IN (SELECT bid from meetings WHERE clubid = %s) GROUP BY bookid', $clubid, $clubid);
        $bookres = array();
        foreach ($books as $book) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => sprintf("https://www.googleapis.com/books/v1/volumes/%s?key=%s", $book["bookid"], $APIKEY),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $response = json_decode($response, true);
            if ($response['error']) {
                $response = array("result" => "Error", "message" => "Google Books API Error");
                echo json_encode($response);
                exit;
            }
            $response["myvote"] = DB::queryFirstField('SELECT vote FROM votes WHERE clubid = %s AND bookid = %s AND userid = %s ', $clubid, $book["bookid"], $uid);
            if (!$response["myvote"]) {
                $response["myvote"] = 0;
            }
            $response["vote"] = $book["vote"];

            array_push($bookres, $response);
        }
        array_multisort(array_column($bookres, 'vote'), SORT_DESC, SORT_NUMERIC, $bookres);


        $response = array("result" => "Success", "books" => $bookres);
        echo json_encode($response);
    } catch (Exception $e) {
        $response = array("result" => "Error", "message" => "SERVER ERROR" . $e->getCode() . ": " . $e->getMessage());
        echo json_encode($response);
        exit;
    }
} else {
    $response = array("result" => "Error", "message" => "Field not found");
    echo json_encode($response);
}
