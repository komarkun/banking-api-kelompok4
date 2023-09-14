<?php
require_once 'config.php';

// Function untuk mentransfer uang dari satu akun ke akun lainnya
function transferMoney($from_akun_id, $to_akun_id, $amount)
{
    global $conn;

    // Check jika akun pengirim ada dalam database
    $from_sql = "SELECT balance FROM accounts WHERE akun_id = ?";
    $from_data = $conn->prepare($from_sql);
    $from_data->bind_param("s", $from_akun_id);
    $from_data->execute();
    $from_result = $from_data->get_result();

    if ($from_result->num_rows == 0) {
        echo json_encode(['error' => 'Account not found']);
        return;
    }

    // Check jika akun penerima ada dalam database
    $to_sql = "SELECT balance FROM accounts WHERE akun_id = ?";
    $to_data = $conn->prepare($to_sql);
    $to_data->bind_param("s", $to_akun_id);
    $to_data->execute();
    $to_result = $to_data->get_result();

    if ($to_result->num_rows == 0) {
        echo json_encode(['error' => 'Recipient account not found']);
        return;
    }

    // Ambil saldo akun pengirim
    $from_row = $from_result->fetch_assoc();
    $from_balance = $from_row['balance'];

    // Cek apakah akun pengirim memiliki saldo cukup untuk mentransfer
    if ($from_balance < $amount) {
        echo json_encode(['error' => 'Insufficient balance']);
        return;
    }

    // Mulai transaksi
    $conn->begin_transaction();

    // Kurangkan saldo akun pengirim
    $update_from_sql = "UPDATE accounts SET balance = balance - ? WHERE akun_id = ?";
    $update_from_data = $conn->prepare($update_from_sql);
    $update_from_data->bind_param("ds", $amount, $from_akun_id);

    // Tambahkan saldo akun penerima
    $update_to_sql = "UPDATE accounts SET balance = balance + ? WHERE akun_id = ?";
    $update_to_data = $conn->prepare($update_to_sql);
    $update_to_data->bind_param("ds", $amount, $to_akun_id);

    if ($update_from_data->execute() && $update_to_data->execute()) {
        // Commit transaksi jika berhasil
        $conn->commit();
        echo json_encode(['message' => 'Money transferred successfully']);
    } else {
        // Rollback transaksi jika gagal
        $conn->rollback();
        echo json_encode(['error' => 'Failed to transfer money']);
    }

    $update_from_data->close();
    $update_to_data->close();
}

// Menghandle permintaan transfer uang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from_akun_id'], $_POST['to_akun_id'], $_POST['amount'])) {
    $from_akun_id = $_POST['from_akun_id'];
    $to_akun_id = $_POST['to_akun_id'];
    $amount = $_POST['amount'];

    transferMoney($from_akun_id, $to_akun_id, $amount);
}
?>
