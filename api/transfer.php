<?php
header("Content-Type: application/json");
require_once('config.php');

// Terima permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Terima data JSON dari permintaan
    $requestData = json_decode(file_get_contents("php://input"), true);

    // Periksa apakah data yang diperlukan ada
    if (isset($requestData["sender_id"]) && isset($requestData["receiver_id"]) && isset($requestData["saldo"])) {
        $fromAccount = $requestData["sender_id"];
        $toAccount = $requestData["receiver_id"];
        $amount = floatval($requestData["saldo"]);

        // Query SQL untuk mengambil saldo pengirim berdasarkan nama akun
        $sql = "SELECT balance FROM accounts WHERE fullname = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $fromAccount);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $senderBalance = $row["balance"];

            // Periksa apakah saldo mencukupi dan lanjutkan dengan transfer jika saldo mencukupi
            if ($senderBalance >= $amount) {
                // Mulai transaksi
                $conn->begin_transaction();

                // Update saldo pengirim
                $sqlUpdateSender = "UPDATE accounts SET balance = balance - ? WHERE fullname = ?";
                $stmtUpdateSender = $conn->prepare($sqlUpdateSender);
                $stmtUpdateSender->bind_param("ds", $amount, $fromAccount);
                $stmtUpdateSender->execute();

                // Update saldo penerima
                $sqlUpdateReceiver = "UPDATE accounts SET balance = balance + ? WHERE fullname = ?";
                $stmtUpdateReceiver = $conn->prepare($sqlUpdateReceiver);
                $stmtUpdateReceiver->bind_param("ds", $amount, $toAccount);
                $stmtUpdateReceiver->execute();

                // Catat transaksi
                $sqlInsertTransaction = "INSERT INTO transactions (sender_id, receiver_id, saldo) VALUES (?, ?, ?)";
                $stmtInsertTransaction = $conn->prepare($sqlInsertTransaction);
                $stmtInsertTransaction->bind_param("ssd", $fromAccount, $toAccount, $amount);
                $stmtInsertTransaction->execute();

                // Commit transaksi
                $conn->commit();

                echo json_encode(["message" => "Transfer berhasil"]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Saldo pengirim tidak mencukupi"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Akun pengirim tidak ditemukan"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Permintaan tidak valid. Pastikan Anda mengirimkan 'sender_id', 'receiver_id', dan 'saldo' dalam bentuk JSON."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Metode HTTP tidak diizinkan"]);
}

// Tutup koneksi ke database
$conn->close();
?>