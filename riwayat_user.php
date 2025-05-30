<?php
session_start();
require '../PHP/Koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: masuk.html");
  exit();
}

$userId = $_SESSION['user_id'];

$query = "SELECT p.*, l.NamaLapangan FROM pemesanan p 
          JOIN lapangan l ON p.IdLapangan = l.IdLapangan
          WHERE p.UserId = ? ORDER BY p.Tanggal DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transaksiMenunggu = [];
$riwayatTransaksi = [];

while ($row = $result->fetch_assoc()) {
  if ($row['Status'] === 'Menunggu') {
    $transaksiMenunggu[] = $row;
  } else {
    $riwayatTransaksi[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Transaksi</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../CSS/riwayat_user.css">
  
</head>
<body>
 <!-- DI DALAM <body> -->

<header class="custom-header">
  <div class="header-top">
    <div class="header-left">
      <img src="../GAMBAR/logo.png" alt="Logo FutsalZone" class="header-logo" />
      <h1>FutsalZone</h1>
    </div>
    <div class="judul-tengah">
    <h2>Daftar Transaksi</h2>
    </div>

    <a href="beranda_user.php" class="btn-beranda">
      <i class="fas fa-home"></i> Beranda
    </a>
  </div>

</header>


<main class="transaksi-container">
  <div class="transaksi-grid">

    <!-- Transaksi Menunggu -->
    <section>
      <h2>Transaksi Menunggu Konfirmasi</h2>
      <?php if (count($transaksiMenunggu) === 0): ?>
        <p class="kosong">Tidak ada transaksi yang menunggu.</p>
      <?php else: ?>
        <?php foreach ($transaksiMenunggu as $row): ?>
          <div class="transaksi-card">
            <h3><?= $row['NamaLapangan'] ?></h3>
            <p><strong>Tanggal:</strong> <?= $row['Tanggal'] ?></p>
            <p><strong>Jam:</strong> <?= $row['JamMulai'] ?> - <?= $row['JamSelesai'] ?></p>
            <p><strong>Total:</strong> Rp <?= number_format($row['TotalBayar'], 0, ',', '.') ?></p>
            <p><strong>Status:</strong> 
              <span class="status-<?= strtolower($row['Status']) ?>">
                <?= $row['Status'] ?>
              </span>
            </p>
            <?php if ($row['BuktiBayar']): ?>
              <p><strong>Bukti:</strong> <a href="../bukti/<?= $row['BuktiBayar'] ?>" target="_blank">Lihat Bukti</a></p>
            <?php else: ?>
              <p><strong>Bukti:</strong> Belum diunggah</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <!-- Riwayat Transaksi -->
    <section>
      <h2>Riwayat Transaksi</h2>
      <?php if (count($riwayatTransaksi) === 0): ?>
        <p class="kosong">Belum ada transaksi yang selesai.</p>
      <?php else: ?>
        <?php foreach ($riwayatTransaksi as $row): ?>
          <div class="transaksi-card">
            <h3><?= $row['NamaLapangan'] ?></h3>
            <p><strong>Tanggal:</strong> <?= $row['Tanggal'] ?></p>
            <p><strong>Jam:</strong> <?= $row['JamMulai'] ?> - <?= $row['JamSelesai'] ?></p>
            <p><strong>Total:</strong> Rp <?= number_format($row['TotalBayar'], 0, ',', '.') ?></p>
            <p><strong>Status:</strong> 
              <span class="status-<?= strtolower($row['Status']) ?>">
                <?= $row['Status'] ?>
              </span>
            </p>
            <?php if ($row['BuktiBayar']): ?>
              <p><strong>Bukti:</strong> <a href="../bukti/<?= $row['BuktiBayar'] ?>" target="_blank">Lihat Bukti</a></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

  </div>
</main>

</body>
</html>
