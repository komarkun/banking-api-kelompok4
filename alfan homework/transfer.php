<?php
require_once 'config.php';

// Function untuk mentransfer uang dari satu akun ke akun lainnya
function transferMoney($from_sender_id, $to_sender_id, $saldo)
{
    global $conn;

    // Check jika akun pengirim ada dalam database
    $from_sql = "SELECT balance FROM accounts WHERE sender_id = ?";
    $from_data = $conn->prepare($from_sql);
    $from_data->bind_param("s", $from_sender_id);
    $from_data->execute();
    $from_result = $from_data->get_result();

    if ($from_result->num_rows == 0) {
        echo json_encode(['error' => 'Account not found']);
        return;
    }

    // Check jika akun penerima ada dalam database
    $to_sql = "SELECT balance FROM accounts WHERE sender_id = ?";
    $to_data = $conn->prepare($to_sql);
    $to_data->bind_param("s", $to_sender_id);
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
    if ($from_balance < $saldo) {
        echo json_encode(['error' => 'Saldo tidak mencukupi']);
        return;
    }

    // Mulai transaksi
    $conn->begin_transaction();

    // Kurangkan saldo akun pengirim
    $update_from_sql = "UPDATE accounts SET balance = balance - ? WHERE sender_id = ?";
    $update_from_data = $conn->prepare($update_from_sql);
    $update_from_data->bind_param("ds", $saldo, $from_sender_id);

    // Tambahkan saldo akun penerima
    $update_to_sql = "UPDATE accounts SET balance = balance + ? WHERE sender_id = ?";
    $update_to_data = $conn->prepare($update_to_sql);
    $update_to_data->bind_param("ds", $saldo, $to_sender_id);

    if ($update_from_data->execute() && $update_to_data->execute()) {
        // Commit transaksi jika berhasil
        $conn->commit();

        // Simpan data transfer ke dalam tabel transactions
        $insert_transaction_sql = "INSERT INTO transactions (from_sender_id, to_sender_id, saldo) VALUES (?, ?, ?)";
        $insert_transaction_data = $conn->prepare($insert_transaction_sql);
        $insert_transaction_data->bind_param("ssd", $from_sender_id, $to_sender_id, $saldo);
        $insert_transaction_data->execute();
        
        echo json_encode(['message' => 'transfer berhasil']);
    } else {
        // Rollback transaksi jika gagal
        $conn->rollback();
        echo json_encode(['error' => 'transfer']);
    }

    $update_from_data->close();
    $update_to_data->close();
}

// Menghandle permintaan transfer uang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from_sender_id'], $_POST['to_sender_id'], $_POST['saldo'])) {
    $from_sender_id = $_POST['from_sender_id'];
    $to_sender_id = $_POST['to_sender_id'];
    $saldo = $_POST['saldo'];

    transferMoney($from_sender_id, $to_sender_id, $saldo);
}
?>