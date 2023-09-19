<?php
require_once 'config.php';

function getRecentTransactions($akun_id)
{
    global $conn;

    $sql = "SELECT id AS transaction_id, sender_id, receiver_id, saldo AS amount, timestamp FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY timestamp DESC";
    $data = $conn->prepare($sql);
    $data->bind_param("ss", $akun_id, $akun_id);
    $data->execute();
    $result = $data->get_result();

    $transactions = [];

    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    return $transactions;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['akun_id'])) {
    $akun_id = $_GET['akun_id'];
    $transactions = getRecentTransactions($akun_id);

    if (!empty($transactions)) {
        echo json_encode(['transactions' => $transactions]);
    } else {
        echo json_encode(['message' => 'Tidak ada riwayat transaksi']);
    }
}