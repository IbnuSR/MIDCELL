<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include koneksi database
require_once 'koneksi.php';

// Include PHPMailer
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';
$otp_sent = false;

// Request OTP
if (isset($_POST['request_otp'])) {
    $email_owner = 'midcell2025@gmail.com'; // Hardcode

    // Cek apakah email owner ada di database
    $stmt = $pdo->prepare("SELECT id_owner FROM owner WHERE email = ?");
    $stmt->execute([$email_owner]);
    $owner = $stmt->fetch();
    
    if (!$owner) {
        $error = "Email owner tidak ditemukan. Silakan hubungi admin.";
    } else {
        // Generate OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + (10 * 60)); // 10 menit
        
        // Update OTP di tabel owner
        $stmt = $pdo->prepare("UPDATE owner SET otp = ?, otp_expires = ? WHERE email = ?");
        $stmt->execute([$otp, $expires, $email_owner]);
        
        // Kirim OTP ke email owner
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
            $mail->addAddress($email_owner);

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
            $success = "Kode OTP telah dikirim ke email owner ($email_owner). Silakan cek inbox/spam Anda.";
            $otp_sent = true;
        } catch (Exception $e) {
            // 🔧 Mode development — tampilkan OTP (hapus di production!)
            $success = "[DEV] Email gagal: " . $mail->ErrorInfo . ". Gunakan kode berikut (10 menit): <strong>$otp</strong>";
            $otp_sent = true;
        }
    }
}

// Register Karyawan
if (isset($_POST['register'])) {
    $nama_karyawan = trim($_POST['nama_karyawan']);
    $username = trim($_POST['username']);
    $email_karyawan = trim($_POST['email_karyawan']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $otp = $_POST['otp'];

    // Validasi input
    if (empty($nama_karyawan) || empty($username) || empty($email_karyawan) || empty($password) || empty($confirm_password) || empty($otp)) {
        $error = "Semua field wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $error = "OTP harus 6 digit angka.";
    } else {
        // Cek apakah username/email karyawan sudah digunakan
        $stmt = $pdo->prepare("SELECT id_karyawan FROM karyawan WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email_karyawan]);
        if ($stmt->fetch()) {
            $error = "Username atau email sudah digunakan.";
        } else {
            // Verifikasi OTP dengan tabel owner
            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("
                SELECT id_owner 
                FROM owner 
                WHERE email = ? 
                  AND otp = ? 
                  AND otp_expires > ?
            ");
            $stmt->execute(['midcell2025@gmail.com', $otp, $now]);
            $result = $stmt->fetch();

            if ($result) {
                // ✅ Sukses — Simpan karyawan dan hapus OTP
                $stmt = $pdo->prepare("INSERT INTO karyawan (nama_karyawan, username, email, password, is_verified, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$nama_karyawan, $username, $email_karyawan, $password]);
                
                $stmt = $pdo->prepare("UPDATE owner SET otp = NULL, otp_expires = NULL WHERE email = ?");
                $stmt->execute(['midcell2025@gmail.com']);
                
                $success = "Pendaftaran berhasil! Silakan login untuk melanjutkan.";
            } else {
                $error = "Kode OTP salah atau sudah kadaluarsa.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - MIDCELL</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #2563eb;
            --accent: #60a5fa;
            --light: #ffffff;
            --dark: #212529;
            --success: #4ade80;
            --white-section: #ffffff;
            --blue-section: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--blue-section);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: var(--light);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            color: var(--dark);
        }

        .register-header {
            background: var(--blue-section);
            padding: 30px;
            text-align: center;
            color: var(--light);
        }

        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .register-form {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: #f8fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--light);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
        }

        .request-otp-button {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .request-otp-button:hover {
            background: var(--secondary);
        }

        .register-button {
            background: var(--blue-section);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-to-login a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            font-size: 0.9rem;
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
            font-size: 0.9rem;
        }

        /* Popup OTP */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .popup-header {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .popup-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 3px;
        }

        .popup-button {
            background: var(--primary);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
        }

        .popup-button:hover {
            background: var(--secondary);
        }

        .close-popup {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
        }

        @media (max-width: 480px) {
            .register-form {
                padding: 30px 20px;
            }
            
            .register-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2><i class="fas fa-user-plus"></i> Daftar Karyawan</h2>
            <p>Silakan isi formulir di bawah ini</p>
        </div>
        
        <div class="register-form">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group">
                    <label for="nama_karyawan">Nama Lengkap</label>
                    <input type="text" id="nama_karyawan" name="nama_karyawan" required 
                           value="<?php echo htmlspecialchars($_POST['nama_karyawan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <label for="email_karyawan">Email Karyawan</label>
                    <input type="email" id="email_karyawan" name="email_karyawan" required 
                           value="<?php echo htmlspecialchars($_POST['email_karyawan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required />
                        <span class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required />
                        <span class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="button" onclick="openOtpPopup()" class="request-otp-button">
                    <i class="fas fa-paper-plane"></i> Minta Kode OTP
                </button>
                
                <input type="hidden" name="otp" id="otpInput" />
                <button type="submit" name="register" class="register-button" id="registerBtn" disabled>
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>
            
            <div class="back-to-login">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Sudah punya akun? Masuk di sini
                </a>
            </div>
        </div>
    </div>

    <!-- Popup OTP -->
    <div class="popup-overlay" id="otpPopup">
        <div class="popup-content">
            <span class="close-popup" onclick="closeOtpPopup()">&times;</span>
            <h3 class="popup-header"><i class="fas fa-key"></i> Masukkan Kode OTP</h3>
            <p style="margin-bottom: 20px; color: #64748b;">Kode OTP telah dikirim ke email owner. Masukkan kode yang telah diberikan.</p>
            <input type="text" class="popup-input" id="popupOtp" placeholder="______" maxlength="6" oninput="formatOtp(this)" />
            <button class="popup-button" onclick="submitOtp()">Konfirmasi OTP</button>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const passwordInput = document.getElementById(id);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function openOtpPopup() {
            document.getElementById('otpPopup').style.display = 'flex';
        }

        function closeOtpPopup() {
            document.getElementById('otpPopup').style.display = 'none';
        }

        function formatOtp(input) {
            let value = input.value.replace(/\D/g, '').substring(0, 6);
            input.value = value;
        }

        function submitOtp() {
            const otp = document.getElementById('popupOtp').value;
            if (otp.length === 6) {
                document.getElementById('otpInput').value = otp;
                document.getElementById('registerBtn').disabled = false;
                closeOtpPopup();
            } else {
                alert('OTP harus 6 digit.');
            }
        }

        // Close popup when clicking outside
        window.onclick = function(event) {
            const popup = document.getElementById('otpPopup');
            if (event.target === popup) {
                closeOtpPopup();
            }
        }

        // Add form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            
            if (password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok.');
                e.preventDefault();
                return;
            }
            
            if (password.length < 8) {
                alert('Password minimal 8 karakter.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>