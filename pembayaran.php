<?php
session_start();
require '../PHP/Koneksi.php';

$idLapangan = $_GET['id'] ?? '';
$hargaPerJam = $_GET['harga'] ?? 0;

$queryRekening = "SELECT pg.NomorRekening 
                  FROM lapangan l 
                  JOIN pelanggan pg ON l.PemilikId = pg.UserId 
                  WHERE l.IdLapangan = ?";
$stmtRek = $conn->prepare($queryRekening);
$stmtRek->bind_param("s", $idLapangan);
$stmtRek->execute();
$resultRek = $stmtRek->get_result();
$rekening = $resultRek->fetch_assoc()['NomorRekening'] ?? 'Belum diatur';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jamMulai = $_POST['jamMulai'] ?? '';
  $jamSelesai = $_POST['jamSelesai'] ?? '';
  $userId = $_SESSION['user_id'] ?? '';
  $tanggal = date('Y-m-d');
  $status = 'Menunggu';

  if (empty($jamMulai) || empty($jamSelesai) || empty($_FILES['bukti']['name'])) {
    $_SESSION['modal_title'] = 'Lengkapi Form';
    $_SESSION['modal_message'] = 'Mohon lengkapi semua form sebelum membayar.';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
  } else {
    $start = intval(explode(':', $jamMulai)[0]);
    $end = intval(explode(':', $jamSelesai)[0]);
    $durasi = max(0, $end - $start);
    $total = $durasi * intval($hargaPerJam);

    $namaFile = uniqid() . "_" . basename($_FILES['bukti']['name']);
    $tmp = $_FILES['bukti']['tmp_name'];
    $folder = "../bukti/" . $namaFile;
    move_uploaded_file($tmp, $folder);

    $query = "INSERT INTO pemesanan 
      (UserId, IdLapangan, JamMulai, JamSelesai, Tanggal, TotalBayar, BuktiBayar, Status)
      VALUES 
      ('$userId', '$idLapangan', '$jamMulai', '$jamSelesai', '$tanggal', '$total', '$namaFile', '$status')";

    mysqli_query($conn, $query);
    $_SESSION['modal_title'] = 'Berhasil';
    $_SESSION['modal_message'] = 'Pembayaran berhasil dikirim.';
    header("Location: riwayat_user.php?");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran Lapangan</title>
  <link rel="stylesheet" href="../CSS/pembayaran.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .modal {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .modal-box {
      background:rgba(23, 133, 212, 0.78);
      padding: 25px;
      border-radius: 10px;
      text-align: center;
      color: white;
      max-width: 400px;
      width: 90%;
    }
    .modal-box h3 { margin: 0 0 10px; }
    .modal-box button {
      margin: 10px 10px 0 10px;
      background: white;
      color:rgba(23, 133, 212, 0.78);
      font-weight: bold;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="page-wrapper">
    <div class="form-container">
      <h2>Informasi Pembayaran</h2>
      <p><strong>Transfer ke:</strong> <?= htmlspecialchars($rekening) ?></p>
      <p><strong>Harga per Jam:</strong> Rp <?= number_format($hargaPerJam, 0, ',', '.') ?></p>

      <form method="post" enctype="multipart/form-data" id="paymentForm">
        <label for="jamMulai">Jam Mulai:</label>
        <input type="time" id="jamMulai" name="jamMulai">

        <label for="jamSelesai">Jam Selesai:</label>
        <input type="time" id="jamSelesai" name="jamSelesai">

        <p class="total"><strong>Total yang Harus Dibayar:</strong> Rp <span id="totalHarga">0</span></p>

        <label for="bukti">Upload Bukti Pembayaran:</label>
        <input type="file" id="bukti" name="bukti" accept="image/*">

        <div class="button-group">
          <button type="button" class="btn-cancel" onclick="window.history.back()">Batal</button>
          <button type="button" class="btn-confirm" onclick="handleKonfirmasi()">Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>

  <div id="modal-container"></div>

  <script>
    const hargaPerJam = <?= $hargaPerJam ?>;

    document.getElementById('jamMulai').addEventListener('input', hitungTotal);
    document.getElementById('jamSelesai').addEventListener('input', hitungTotal);

    function hitungTotal() {
      const mulai = document.getElementById("jamMulai").value;
      const selesai = document.getElementById("jamSelesai").value;
      if (mulai && selesai) {
        const [jamMulai] = mulai.split(":").map(Number);
        const [jamSelesai] = selesai.split(":").map(Number);
        let totalJam = jamSelesai - jamMulai;
        if (totalJam < 0) totalJam = 0;
        const totalHarga = totalJam * hargaPerJam;
        document.getElementById("totalHarga").innerText = totalHarga.toLocaleString("id-ID");
      } else {
        document.getElementById("totalHarga").innerText = "0";
      }
    }

    function showModal(title, message, showConfirm = false, redirect = false) {
      const modal = document.createElement('div');
      modal.className = 'modal';
      modal.innerHTML = `
        <div class="modal-box">
          <h3>${title}</h3>
          <p>${message}</p>
          ${showConfirm ? `
            <button onclick="submitForm()">OK</button>
            <button onclick="this.parentElement.parentElement.remove()">Batal</button>
          ` : `
            <button onclick="${redirect ? 'window.location.href=\'riwayat_user.php\'' : 'this.parentElement.parentElement.remove()'}">OK</button>
          `}
        </div>
      `;
      document.body.appendChild(modal);
    }


    function handleKonfirmasi() {
      const jamMulai = document.getElementById('jamMulai').value;
      const jamSelesai = document.getElementById('jamSelesai').value;
      const bukti = document.getElementById('bukti').value;

      if (!jamMulai || !jamSelesai || !bukti) {
        showModal('Lengkapi Form', 'Mohon lengkapi semua form sebelum melakukan pembayaran!.');
      } else {
        showModal('Konfirmasi', 'Yakin ingin melakukan pembayaran?', true);
      }
    }

    function submitForm() {
      document.getElementById('paymentForm').submit();
    }
  </script>
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <script>
    showModal("Berhasil", "Pembayaran berhasil dikirim.", false, true);
  </script>
<?php endif; ?>

</body>
</html>
