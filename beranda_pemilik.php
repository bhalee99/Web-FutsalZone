<?php
session_start();
require '../PHP/Koneksi.php';

// Pastikan hanya pemilik yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
  header("Location: login_pemilik.html");
  exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data lengkap pemilik
$resultPemilik = mysqli_query($conn, "SELECT * FROM pelanggan WHERE UserId = '$user_id'");
$data = mysqli_fetch_assoc($resultPemilik);

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = $_POST['nama'];
  $hp = $_POST['hp'];
  $email = $_POST['email'];
  $rekening = $_POST['rekening'];
  $password = $_POST['password'];

  $update = "UPDATE pelanggan SET FullName='$nama', No_HP='$hp', Email='$email', NomorRekening='$rekening', Password='$password' WHERE UserId = '$user_id'";
  mysqli_query($conn, $update);
  echo "<script>window.location.href='beranda_pemilik.php';</script>";
  exit();
}

$query = "SELECT * FROM lapangan WHERE PemilikId = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beranda Pemilik Lapangan</title>
  <link rel="stylesheet" href="../CSS/beranda_pemilik.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
  <div class="logo">
    <h2>FutsalZone</h2>
  </div>

  <div class="judul-lapangan">Daftar Lapangan</div>

  <div class="profile-menu">
    <i class="fas fa-user-circle profile-icon" onclick="toggleDropdown()"></i>

    <div class="dropdown" id="dropdown">
      <h4 class="dropdown-title">Profil</h4>
      <hr />

      <form method="post" class="profile-info">
        <div class="info-row">
          <label for="nama">Nama:</label>
          <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($data['FullName']) ?>" required>
        </div>

        <div class="info-row">
          <label for="hp">No HP:</label>
          <input type="text" name="hp" id="hp" value="<?= htmlspecialchars($data['No_HP']) ?>" required>
        </div>

        <div class="info-row">
          <label for="email">Email:</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($data['Email']) ?>" required>
        </div>

        <div class="info-row">
          <label for="rekening">Nomor Rekening:</label>
          <input type="text" name="rekening" id="rekening" value="<?= htmlspecialchars($data['NomorRekening'] ?? '') ?>" required>
        </div>

        <div class="info-row">
          <label for="password">Password:</label>
          <div style="display: flex; align-items: center;">
            <input type="password" name="password" id="password" value="<?= htmlspecialchars($data['Password']) ?>" required style="flex:1;">
            <i class="fas fa-eye" id="togglePassword" style="margin-left: 8px; cursor: pointer;"></i>
          </div>
        </div>

        <button type="submit" class="logout-button">Simpan</button>
      </form>

      <button class="logout-button" onclick="window.location.href='beranda.html'">Logout</button>
    </div>
  </div>
</header>

<div class="menu">
  <a href="tambah_lapangan.html"><i class="fas fa-plus"></i> Tambah Lapangan</a>
  <a href="kelola_transaksi.php"><i class="fas fa-list"></i> Daftar Transaksi</a>
</div>

<div class="container">
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
      <a href="detail_lapangan.php?id=<?= $row['IdLapangan'] ?>" style="text-decoration: none; color: inherit;">
        <div class="card">
          <img src="../GAMBAR/<?= $row['Gambar'] ?>" alt="<?= $row['NamaLapangan'] ?>">
          <h3><?= $row['NamaLapangan'] ?></h3>
          <p><?= $row['Alamat'] ?></p>
        </div>
      </a>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align: center; width: 100%;">Belum ada lapangan yang didaftarkan.</p>
  <?php endif; ?>
</div>

<script>
  function toggleDropdown() {
    const dropdown = document.getElementById("dropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  }

  window.onclick = function(event) {
    if (!event.target.closest('.profile-menu')) {
      const dropdown = document.getElementById("dropdown");
      if (dropdown) dropdown.style.display = "none";
    }
  };

  // Toggle password visibility
  document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordInput = document.getElementById("password");
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.classList.toggle("fa-eye-slash");
  });
</script>

</body>
</html>
