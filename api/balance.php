<?php
require_once 'config.php';

// function untuk menampilkan saldo / balance
function displayBalance($akun_id)
{
    global $conn;

    $sql = "SELECT balance FROM accounts WHERE akun_id = ?";
    $data = $conn->prepare($sql);
    $data->bind_param("s", $akun_id);
    $data->execute();
    $result = $data->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['Saldo Anda' => $row['balance']]);
    } else {
        echo json_encode(['error' => 'Account not found']);
    }

    $data->close();
}

// menampilkan balance atau saldo
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['akun_id'])) {
    $akun_id = $_GET['akun_id'];
    displayBalance($akun_id);
}
