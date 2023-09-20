<?php
$servername = "localhost";  
$username = "root"; 
$password = ""; 
$database = "db_banking_api_kelompok4"; 

// Membuat koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $database);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi sukses";
}

// Sekarang Anda dapat menjalankan kueri SQL dan bekerja dengan database Anda menggunakan $conn
