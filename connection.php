<?php

// connection.php
define('API_PRIMARY', 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1');
define('API_FALLBACK', 'https://latest.currency-api.pages.dev/v1');
$host = "localhost";
$user = "root";
$pass = "";
$db   = "finance_app";



$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]));
}
?>