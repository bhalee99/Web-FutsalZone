<?php
session_start();
require 'Koneksi.php';

$id = $_POST['id_lapangan'];
$nama = $_POST['nama_lapangan'];
$alamat = $_POST['alamat'];
$harga = $_POST['harga'];
$jam_buka = $_POST['jam_buka'];
$jam_tutup = $_POST['jam_tutup'];
$fasilitas = $_POST['fasilitas'];
$pemilik = $_SESSION['user_id']; // ✅ GUNAKAN SESSION, BUKAN $_POST

// Upload gambar
$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];
$upload_path = "../gambar/" . $gambar;

move_uploaded_file($tmp, $upload_path);

// Simpan ke database
$sql = "INSERT INTO lapangan (NamaLapangan, Alamat, Harga, Jam_Buka, Jam_Tutup, Fasilitas, Gambar, PemilikId)
        VALUES ('$nama', '$alamat', '$harga', '$jam_buka', '$jam_tutup', '$fasilitas', '$gambar', '$pemilik')";

if (mysqli_query($conn, $sql)) {
    header("Location: ../HTML/beranda_pemilik.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
