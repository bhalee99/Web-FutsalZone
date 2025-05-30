<?php
require 'Koneksi.php';

$UserId   = $_POST["UserId"];
$FullName = $_POST["FullName"];
$No_HP    = $_POST["No_HP"];
$Email    = $_POST["Email"];
$Password = $_POST["Password"];
$Role     = $_POST["role"];

// Cek duplikat UserId
$cek = mysqli_query($conn, "SELECT * FROM pelanggan WHERE UserId='$UserId'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('User ID sudah digunakan!'); window.history.back();</script>";
    exit();
}

// Simpan data ke database
$query_sql = "INSERT INTO pelanggan (UserId, FullName, No_HP, Email, Password, role) 
              VALUES ('$UserId', '$FullName', '$No_HP', '$Email', '$Password', '$Role')";

if (mysqli_query($conn, $query_sql)) {
    // Redirect otomatis sesuai role
    if ($Role === 'pemilik') {
        header("Location: ../HTML/masuk_pemilik.html");
    } else {
        header("Location: ../HTML/masuk.html");
    }
    exit();
} else {
    echo "Pendaftaran Gagal: " . mysqli_error($conn);
}

$conn->close();
?>
