<?php
session_start();
require '../PHP/Koneksi.php';

// Proses update status jika ada form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan'], $_POST['aksi'])) {
  $id = $_POST['id_pemesanan'];
  $aksi = $_POST['aksi']; // 'Diterima' atau 'Ditolak'

  if (in_array($aksi, ['Diterima', 'Ditolak'])) {
    $stmt = $conn->prepare("UPDATE pemesanan SET Status = ? WHERE IdPemesanan = ?");
    $stmt->bind_param("si", $aksi, $id);
    $stmt->execute();
    $stmt->close();
  }
}

// Cek login pemilik
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login_pemilik.html");
    exit();
}

$pemilikId = $_SESSION['user_id'];

// Ambil data pemesanan masuk (status = 'Menunggu')
$queryMasuk = "
SELECT p.IdPemesanan, pg.FullName, pg.No_HP, l.NamaLapangan, 
       p.Tanggal, p.JamMulai, p.JamSelesai, p.TotalBayar, p.BuktiBayar
FROM pemesanan p
JOIN pelanggan pg ON p.UserId = pg.UserId
JOIN lapangan l ON p.IdLapangan = l.IdLapangan
WHERE l.PemilikId = '$pemilikId' AND p.Status = 'Menunggu'
";
$masuk = mysqli_query($conn, $queryMasuk);

// Ambil data riwayat transaksi (selain Menunggu)
$queryRiwayat = "
SELECT pg.FullName, l.NamaLapangan, p.Tanggal, p.JamMulai, p.JamSelesai, p.TotalBayar, p.Status
FROM pemesanan p
JOIN pelanggan pg ON p.UserId = pg.UserId
JOIN lapangan l ON p.IdLapangan = l.IdLapangan
WHERE l.PemilikId = '$pemilikId' AND p.Status != 'Menunggu'
";
$riwayat = mysqli_query($conn, $queryRiwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Transaksi Lapangan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/kelola_transaksi.css">
</head>
<body>

<header class="header-flex">
  <div class="header-left">
    <img src="../GAMBAR/logo.png" alt="Logo" class="logo-icon">
    <span class="brand-name">FutsalZone</span>
  </div>

  <div class="header-center">
    <h1 class="judul-header">Daftar Transaksi</h1>
  </div>

  <div class="header-right">
    <a href="beranda_pemilik.php"><i class="fa fa-home"></i> Beranda</a>
  </div>
</header>


<main>

  <section class="section">
    <h2><i class="fa fa-inbox"></i> Pemesanan Masuk</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>No HP</th>
            <th>Lapangan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Total</th>
            <th>Bukti Bayar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($masuk)): ?>
          <tr>
            <td><?= htmlspecialchars($row['FullName']) ?></td>
            <td><?= htmlspecialchars($row['No_HP']) ?></td>
            <td><?= htmlspecialchars($row['NamaLapangan']) ?></td>
            <td><?= htmlspecialchars($row['Tanggal']) ?></td>
            <td><?= $row['JamMulai'] ?> - <?= $row['JamSelesai'] ?></td>
            <td>Rp <?= number_format($row['TotalBayar'], 0, ',', '.') ?></td>
            <td>
              <a href="../bukti/<?= htmlspecialchars($row['BuktiBayar']) ?>" target="_blank">
                <img src="../bukti/<?= htmlspecialchars($row['BuktiBayar']) ?>" class="bukti-img" alt="Bukti">
              </a>
            </td>
            <td>
              <form method="post" class="aksi-form">
                <input type="hidden" name="id_pemesanan" value="<?= $row['IdPemesanan'] ?>">
                <button name="aksi" value="Diterima" class="btn btn-accept">Terima</button>
                <button name="aksi" value="Ditolak" class="btn btn-reject">Tolak</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="section">
    <h2><i class="fa fa-history"></i> Riwayat Transaksi</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Lapangan</th>
            <th>Waktu</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($riwayat)): ?>
          <tr>
            <td><?= htmlspecialchars($row['FullName']) ?></td>
            <td><?= htmlspecialchars($row['NamaLapangan']) ?></td>
            <td><?= $row['Tanggal'] ?> / <?= $row['JamMulai'] ?> - <?= $row['JamSelesai'] ?></td>
            <td>Rp <?= number_format($row['TotalBayar'], 0, ',', '.') ?></td>
            <td><span class="status <?= $row['Status'] ?>"><?= $row['Status'] ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

</body>
</html>
