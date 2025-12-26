<?php
// Konfigurasi
require_once 'koneksi.php';

// Include PHPMailer
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Header
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Input
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (empty($data->email)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email wajib diisi.']);
    exit();
}

// Koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
    exit();
}

// Cek email owner
$stmt = $conn->prepare("SELECT id_owner FROM owner WHERE email = ?");
$stmt->bind_param("s", $data->email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $response = ['status' => 'error', 'message' => 'Email tidak terdaftar sebagai owner.'];
} else {
    // ✅ Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // ✅ Gunakan UTC untuk konsistensi
    date_default_timezone_set('UTC');
    $expires = gmdate('Y-m-d H:i:s', strtotime('+10 minutes')); // +10 menit dari sekarang (UTC)

    // Simpan ke DB
    $update = $conn->prepare("UPDATE owner SET otp = ?, otp_expires = ? WHERE email = ?");
    $update->bind_param("sss", $otp, $expires, $data->email);
    
    if ($update->execute()) {
        // Kirim email via PHPMailer (SMTP Gmail)
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'midcell2025@gmail.com';                // Ganti dengan email kamu
            $mail->Password   = 'your-app-password-here';               // Ganti dengan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('midcell2025@gmail.com', 'MIDCELL Admin');
            $mail->addAddress($data->email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = '[MIDCELL] Kode Verifikasi Pendaftaran Karyawan (6 Digit)';
            $mail->Body    = "
                <h3>Halo Owner,</h3>
                <p>Kode OTP untuk pendaftaran karyawan baru:</p>
                <div style='font-size: 24px; font-weight: bold; background: #f0f0f0; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                    $otp
                </div>
                <p>Berlaku 10 menit. Bisa digunakan berulang kali selama belum kadaluarsa.</p>
                <hr>
                <p>Terima kasih,<br>MIDCELL Team</p>
            ";

            $mail->send();
            $response = ['status' => 'success', 'message' => 'OTP 6 digit dikirim ke ' . $data->email];
        } catch (Exception $e) {
            // 🔧 Mode development — tampilkan OTP (hapus di production!)
            $response = [
                'status' => 'success_dev',
                'message' => '[DEV] Email gagal. Gunakan kode berikut (10 menit):',
                'otp' => $otp
            ];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Gagal menyimpan OTP ke database.'];
    }
    $update->close();
}

$stmt->close();
$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>