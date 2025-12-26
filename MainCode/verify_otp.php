<?php
require_once 'koneksi.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents('php://input'));

// Validasi input
if (empty($data->email) || empty($data->otp) || empty($data->nama_karyawan) || empty($data->username) || empty($data->email_karyawan) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit();
}

if (!preg_match('/^\d{6}$/', $data->otp)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'OTP harus 6 digit angka.']);
    exit();
}

if ($data->password !== $data->confirm_password) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password tidak cocok.']);
    exit();
}

if (strlen($data->password) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password minimal 8 karakter.']);
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    exit();
}

// ✅ Gunakan UTC secara konsisten
date_default_timezone_set('UTC');
$now_utc = gmdate('Y-m-d H:i:s');

// Cek apakah ada record dengan: email + OTP cocok + belum expired
$stmt = $conn->prepare("
    SELECT id_owner 
    FROM owner 
    WHERE email = ? 
      AND otp = ? 
      AND otp_expires > ?
");
$stmt->bind_param("sss", $data->email, $data->otp, $now_utc);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Cek apakah username/email karyawan sudah digunakan
    $check = $conn->prepare("SELECT id_karyawan FROM karyawan WHERE username = ? OR email = ?");
    $check->bind_param("ss", $data->username, $data->email_karyawan);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $response = ['status' => 'error', 'message' => 'Username atau email sudah digunakan.'];
    } else {
        // ✅ Sukses — Simpan karyawan
        $insert = $conn->prepare("INSERT INTO karyawan (nama_karyawan, username, email, password, is_verified, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $insert->bind_param("ssss", $data->nama_karyawan, $data->username, $data->email_karyawan, $data->password);
        $insert->execute();
        $insert->close();

        // ✅ Hapus OTP
        $clear = $conn->prepare("UPDATE owner SET otp = NULL, otp_expires = NULL WHERE email = ?");
        $clear->bind_param("s", $data->email);
        $clear->execute();
        $clear->close();
        
        $response = ['status' => 'success', 'message' => 'Pendaftaran karyawan berhasil.'];
    }
    $check->close();
} else {
    // ❌ Gagal — TIDAK ADA perubahan di database. Hanya kirim error.
    $response = ['status' => 'error', 'message' => 'Kode OTP salah.'];
}

$stmt->close();
$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>