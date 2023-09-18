<?php
require_once 'config.php';

// Function untuk mengambil daftar transaksi terakhir untuk akun tertentu
function getRecentTransactions($akun_id)
{
    global $conn;

    // Ambil daftar transaksi terakhir untuk akun tertentu
    $sql = "SELECT * FROM transactions WHERE from_akun_id = ? OR to_akun_id = ? ORDER BY timestamp DESC LIMIT 10";
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

// Menghandle permintaan untuk mengambil daftar transaksi terakhir
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['akun_id'])) {
    $akun_id = $_GET['akun_id'];

    // Panggil function untuk mengambil daftar transaksi terakhir
    $recentTransactions = getRecentTransactions($akun_id);

    if (count($recentTransactions) > 0) {
        echo json_encode($recentTransactions);
    } else {
        echo json_encode(['message' => 'No recent transactions found']);
    }
}
?>
