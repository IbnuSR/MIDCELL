<?php
// koneksi.php

$host = 'fdb1032.awardspace.net'; // Biasanya localhost di AwardSpace
$username = '4708947_techtopia'; // Username database Anda
$password = '30maret2023'; // Ganti dengan password database Anda
$dbname = '4708947_techtopia'; // Nama database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>