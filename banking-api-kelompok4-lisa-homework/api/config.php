<?php
// This file used for connection ke database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_banking_api_kelompok4";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Gagal Koneksi ke database: " . $conn->connect_error);
} 
?>
