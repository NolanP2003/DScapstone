<?php
// this line reads the incoming data from the webpage
$data = json_decode(file_get_contents("php://input"), true);

// the query variable stores the query from the webpage using the data variable
$query = $data['query'];

// this is the url of the python app that handles the query
$api_url = 'http://localhost:5000/get_protocol';

// this is the data that will be sent to the python app
$request_data = json_encode(array('query' => $query));

// this is the curl request that sends the data to the python app and gets the response
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

// this executes the curl request and stores the response
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

error_log("Response from Flask: " . $response);
error_log("HTTP Code: " . $http_code);

// Check if response is empty or HTTP code isn't 200
if (!$response || $http_code != 200) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Failed to fetch response from API'
    ));
    exit;
}

// this decodes the response from the python app and sends it back to the webpage
$response_data = json_decode($response, true);

// Check if JSON decoding failed
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Invalid JSON response from API'
    ));
    exit;
}

echo json_encode($response_data);
?>
