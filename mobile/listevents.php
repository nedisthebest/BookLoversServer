<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['month']) && isset($_POST['year']) && isset($_POST['uid']) && isset($_POST['token'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $uid = $_POST['uid'];
    $token = $_POST['token'];

    $APIKEY = 'YOURAPIKEY';

    require('../connect.php');

    //query database for all events in that month/year (month: 1-12, year: xxxx)
    //create sql date string for start and end of that month

    $month = sprintf('%02d', $month);
    $startOfMonth = $year . '-' . $month . '-01';
    $endOfMonth = (string)date('Y-m-t', strtotime($startOfMonth));
    $endOfMonth = $endOfMonth . " 23:59:59";

    $events = DB::query('SELECT * FROM meetings m JOIN clubmembership c ON m.clubid = c.clubid JOIN users u on c.userid = u.userid WHERE m.meetingtime >= %s AND m.meetingtime <= %s AND u.userid = %s AND u.token = %s', $startOfMonth, $endOfMonth, $uid, $token);

    $response = null;

    foreach ($events as &$event) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf("https://www.googleapis.com/books/v1/volumes/%s?key=%s", $event["bid"], $APIKEY),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        $book = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $book = json_decode($book, true);
        if ($response['error']) {
            $response = array("result" => "Error", "message" => "Google Books API Error");
            echo json_encode($response);
            exit;
        }

        $event["book"] = $book;
    }

    $response = array("result" => "Success", "events" => $events);
    echo json_encode($response);

    // echo json_encode(array("result" => "Success", "message" => "Month: " . $_POST['month'] . " and year: " . $_POST['year'] . " found!"));
} else {
    $response = array("result" => "Error", "message" => "Month or year field not found!");
    echo json_encode($response);
}
