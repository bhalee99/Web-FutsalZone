<?php
require '../PHP/Koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = $_POST['id_pemesanan'];
  $aksi = $_POST['aksi']; // Diterima atau Ditolak

  // Update status transaksi
  $update = "UPDATE pemesanan SET Status = '$aksi' WHERE IdPemesanan = '$id'";
  mysqli_query($conn, $update);

  // Redirect balik ke halaman transaksi pemilik
  echo "<script>alert('Status transaksi berhasil diperbarui!'); window.location.href = 'kelola_transaksi.php';</script>";
  exit();
}
?>
