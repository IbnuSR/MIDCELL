<?php
session_start();
include 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Cek di tabel owner terlebih dahulu
        $stmt = $pdo->prepare("SELECT id_owner, nama_owner as user_name, username, password FROM owner WHERE username = ?");
        $stmt->execute([$username]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika ditemukan di tabel owner dan password cocok
        if ($owner && $password === $owner['password']) {
            $_SESSION['user_id'] = $owner['id_owner'];
            $_SESSION['user_name'] = $owner['user_name'];
            $_SESSION['user_role'] = 'owner';
            $_SESSION['logged_in'] = true;
            header('Location: admin/dashboard.php');
            exit;
        }

        // Jika tidak ditemukan di owner, cek di tabel karyawan
        $stmt = $pdo->prepare("SELECT id_karyawan, nama_karyawan as user_name, username, password FROM karyawan WHERE username = ? AND is_verified = 1");
        $stmt->execute([$username]);
        $karyawan = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika ditemukan di tabel karyawan dan password cocok
        if ($karyawan && $password === $karyawan['password']) {
            $_SESSION['user_id'] = $karyawan['id_karyawan'];
            $_SESSION['user_name'] = $karyawan['user_name'];
            $_SESSION['user_role'] = 'karyawan';
            $_SESSION['logged_in'] = true;
            header('Location: admin/dashboard.php');
            exit;
        }

        // Jika tidak ditemukan di keduanya atau password salah
        $error = "Username atau password salah, atau akun belum diverifikasi.";
    } else {
        $error = "Username dan password wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MIDCELL</title>
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

        .login-container {
            background: var(--light);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            color: var(--dark);
        }

        .login-header {
            background: var(--blue-section);
            padding: 30px;
            text-align: center;
            color: var(--light);
        }

        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-form {
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

        .login-button {
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
            margin-bottom: 20px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #64748b;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .register-button {
            background: #60a5fa;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .register-button:hover {
            background: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(96, 165, 250, 0.3);
        }

        .extra-links {
            text-align: center;
            font-size: 0.9rem;
        }

        .extra-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: block;
            margin: 10px 0;
            transition: color 0.3s ease;
        }

        .extra-links a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .back-to-landing {
            margin-top: 20px;
            text-align: center;
        }

        .back-to-landing a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-to-landing a:hover {
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

        @media (max-width: 480px) {
            .login-form {
                padding: 30px 20px;
            }
            
            .login-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-user-lock"></i> Masuk ke MIDCELL</h2>
            <p>Silakan masukkan username dan password Anda</p>
        </div>
        
        <div class="login-form">
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
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required />
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
            
            <div class="divider">atau</div>
            
            <a href="register.php" class="register-button">
                <i class="fas fa-user-plus"></i> Daftar Karyawan
            </a>
            
            <div class="extra-links">
                <a href="forgot_password.php">
                    <i class="fas fa-key"></i> Lupa Password?
                </a>
                <a href="landingpage.php" class="back-to-landing">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
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

        // Add form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username) {
                alert('Username wajib diisi.');
                e.preventDefault();
                return;
            }
            
            if (!password) {
                alert('Password wajib diisi.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>