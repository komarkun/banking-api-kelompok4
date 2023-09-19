<?php
require_once 'config.php';

// Function tranfer uang dari satu akun ke akun lain
function transferMoney($sender_id, $receiver_id, $saldo)
{
    global $conn;

    // Check jika pengirim akun terdaftar
    $check_sql = "SELECT balance FROM accounts WHERE akun_id = ?";
    $check_data = $conn->prepare($check_sql);
    $check_data->bind_param("s", $sender_id);
    $check_data->execute();
    $check_result = $check_data->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode(['error' => 'Pengirim tidak ditemuka']);
        return;
    }

    // Check jika penerima akun terdaftar
    $to_sql = "SELECT balance FROM accounts WHERE akun_id = ?";
    $to_data = $conn->prepare($to_sql);
    $to_data->bind_param("s", $receiver_id);
    $to_data->execute();
    $to_result = $to_data->get_result();

    if ($to_result->num_rows == 0) {
        echo json_encode(['error' => 'Penerima tidak ditemukan']);
        return;
    }

    // 
    $check_row = $check_result->fetch_assoc();
    $check_balance = $check_row['balance'];

    // Check if the sender has enough balance to transfer
    if ($check_balance < $saldo) {
        echo json_encode(['error' => 'Saldo anda kurang untuk transfer']);
        return;
    }

    // Start the transaction
    $conn->begin_transaction();

    // Deduct sender's balance
    $update_check_sql = "UPDATE accounts SET balance = balance - ? WHERE akun_id = ?";
    $update_check_data = $conn->prepare($update_check_sql);
    $update_check_data->bind_param("ds", $saldo, $sender_id);

    // Add to receiver's balance
    $update_to_sql = "UPDATE accounts SET balance = balance + ? WHERE akun_id = ?";
    $update_to_data = $conn->prepare($update_to_sql);
    $update_to_data->bind_param("ds", $saldo, $receiver_id);

    if ($update_check_data->execute() && $update_to_data->execute()) {
        // Commit the transaction if successful
        $conn->commit();
        echo json_encode(['message' => 'Uang berhasil ditransfer']);
    } else {
        // Rollback the transaction if it fails
        $conn->rollback();
        echo json_encode(['error' => 'Gagal tranfer uang']);
    }
    // Log untuk menyimpan transaksi
    $log_sql = "INSERT INTO transactions (sender_id, receiver_id, saldo) VALUES (?, ?, ?)";
    $log_data = $conn->prepare($log_sql);
    $log_data->bind_param("ssd", $sender_id, $receiver_id, $saldo);

    if ($log_data->execute()) {
        // jika transaksi succes
        $conn->commit();
        echo json_encode(['message' => 'Riwayat tercatat']);
    } else {
        // jika transaksi gagal
        $conn->rollback();
        echo json_encode(['error' => 'Riwayat gagal']);
    }

    $update_check_data->close();
    $update_to_data->close();
    $log_data->close();
}

// if login untuk mengirim data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sender_id'], $_POST['receiver_id'], $_POST['saldo'])) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $saldo = $_POST['saldo'];

    transferMoney($sender_id, $receiver_id, $saldo);
}