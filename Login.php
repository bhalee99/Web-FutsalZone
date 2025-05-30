<?php
session_start();
require 'Koneksi.php';

$identifier = $_POST['identifier'];
$password   = $_POST['password'];
$role       = $_POST['role'];

$sql = "SELECT * FROM pelanggan 
        WHERE (Email='$identifier' OR No_HP='$identifier') 
        AND Password='$password' 
        AND role='$role'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['UserId'];
    $_SESSION['nama']    = $user['FullName'];
    $_SESSION['No_HP']   = $user['No_HP'];
    $_SESSION['Email']   = $user['Email'];
    $_SESSION['role']    = $user['role']; // tambahkan agar bisa dicek nanti

    if ($user['role'] === 'pemilik') {
        header("Location: ../HTML/beranda_pemilik.php");
    } else {
        header("Location: ../HTML/beranda_user.php");
    }
    exit();
} else {
    if ($role === 'pemilik') {
        header("Location: ../HTML/masuk_pemilik.html?error=gagal");
    } else {
        header("Location: ../HTML/masuk.html?error=gagal");
    }
    exit();
}

$conn->close();
?>