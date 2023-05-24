<?php
/* ALLOWS CROSS ORIGIN (COMMUNICATION BETWEEN TWO SYSTEMS) */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if (isset($_POST['book'])) { //check the post data
    if (isset($_POST['page'])) {
        $page = $_POST['page'];
    } else {
        $page = 1;
    }

    $curl = curl_init();

    $book = urlencode(htmlspecialchars(trim(stripslashes($_POST['book']))));

    $itemsPerPage = 25;

    $APIKEY = 'AIzaSyAtShlOPeIM000Kz6GBARjyiD4rhwR1DnA';

    curl_setopt_array($curl, [
        CURLOPT_URL => sprintf("https://www.googleapis.com/books/v1/volumes?q=%s&key=%s&startIndex=%s&maxResults=25", $book, $APIKEY, (($page - 1) * $itemsPerPage)),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: book-finder1.p.rapidapi.com",
            "X-RapidAPI-Key: b140c55e12mshfba47be9f525419p1553ebjsn3a11ca2ca0ec"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}
