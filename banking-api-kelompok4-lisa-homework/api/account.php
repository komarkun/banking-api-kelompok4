<?php
require_once 'config.php';

// Function untuk membuat akun_id
function createAccount($akun_id)
{
    global $conn;

    // check keunikan akun agar tidak sama
    $check_sql = "SELECT * FROM accounts WHERE akun_id = ?";
    $check_data = $conn->prepare($check_sql);
    $check_data->bind_param("s", $akun_id);
    $check_data->execute();
    $check_result = $check_data->get_result();
    // jika akun sudah ada maka akan jadi fitur nabung
    if ($check_result->num_rows > 0) {
        echo json_encode(['Nabung' => 'Terima kasih sudah menabung']);
        return;
    }

    // jika akun sudah unik masukan akun baru
    $insert_sql = "INSERT INTO accounts (akun_id) VALUES (?)";
    $insert_data = $conn->prepare($insert_sql);
    $insert_data->bind_param("s", $akun_id);

    if ($insert_data->execute()) {
        echo json_encode(['message' => 'Account created successfully']);
    } else {
        echo json_encode(['error' => 'Failed to create account']);
    }

    $insert_data->close();
}

// Function untuk mendelete akun_id
function deleteAccount($akun_id)
{
    global $conn;

    // Check jika akun ada / tersedia
    $check_sql = "SELECT * FROM accounts WHERE akun_id = ?";
    $check_data = $conn->prepare($check_sql);
    $check_data->bind_param("s", $akun_id);
    $check_data->execute();
    $check_result = $check_data->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['error' => 'Account not found']);
        return;
    }

    // Delete akun_id
    $delete_sql = "DELETE FROM accounts WHERE akun_id = ?";
    $delete_data = $conn->prepare($delete_sql);
    $delete_data->bind_param("s", $akun_id);

    if ($delete_data->execute()) {
        echo json_encode(['message' => 'Account deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete account']);
    }

    $delete_data->close();
}

// Check untuk Membuat atau Mendelete account dengan method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akun_id'])) {
    $akun_id = $_POST['akun_id'];

    // cek API untuk Mendelete akun akun_id + action
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        deleteAccount($akun_id);
    } else {
        createAccount($akun_id);
    }
}

// Function untuk menambah saldo atau menabung di awal
function addBalance($akun_id, $saldo)
{
    global $conn;

    // Check jika akun sudah ada
    $check_sql = "SELECT * FROM accounts WHERE akun_id = ?";
    $check_data = $conn->prepare($check_sql);
    $check_data->bind_param("s", $akun_id);
    $check_data->execute();
    $check_result = $check_data->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode(['error' => 'Account not found']);
        return;
    }

    // Update saldo untuk menabung dan membuat saldo awal
    if (updateAccountBalance($akun_id, $saldo)) {
        echo json_encode(['message' => 'Money saved successfully']);
    } else {
        echo json_encode(['error' => 'Failed to save money']);
    }
}

// Menyimpan uang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akun_id'], $_POST['saldo'])) {
    $akun_id = $_POST['akun_id'];
    $saldo = $_POST['saldo'];
    addBalance($akun_id, $saldo);
}

// function untuk mengupdate saldo atau balance
function updateAccountBalance($akun_id, $saldo)
{
    global $conn;
    $sql = "UPDATE accounts SET balance = balance + ? WHERE akun_id = ?";
    $data = $conn->prepare($sql);
    $data->bind_param("di", $saldo, $akun_id);
    return $data->execute();
}
