<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];

    require('../connect.php');

    //query database for all events in that month/year (month: 1-12, year: xxxx)
    //create sql date string for start and end of that month

    $month = sprintf('%02d', $month);
    $startOfMonth = $year . '-' . $month . '-01';
    $endOfMonth = (string)date('Y-m-t', strtotime($startOfMonth));
    $endOfMonth = $endOfMonth . " 23:59:59";

    $events = DB::query('SELECT * FROM meetings WHERE meetingtime >= %s AND meetingtime <= %s', $startOfMonth, $endOfMonth);

    $response = null;

    $response = array("result" => "Success", "events" => $events);
    echo json_encode($response);

    // echo json_encode(array("result" => "Success", "message" => "Month: " . $_POST['month'] . " and year: " . $_POST['year'] . " found!"));
} else {
    $response = array("result" => "Error", "message" => "Month or year field not found!");
    echo json_encode($response);
}