<?php
$data = json_decode(file_get_contents("php://input"), true);

$query = $data['query'];

$api_url = 'http://localhost:5000/get_protocol';

$request_data = json_encode(array('query' => $query));

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);

echo json_encode($response_data);
?>
