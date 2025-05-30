<?php
require '../PHP/Koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM lapangan");

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pemesanan Lapangan Futsal</title>
  <link rel="stylesheet" href="../CSS/beranda_user.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
 
</head>
<body>
    <header class="custom-header">
    <div class="header-left">
        <img src="../GAMBAR/logo.png" alt="Logo FutsalZone" class="header-logo" />
        <h1>FutsalZone</h1>
    </div>

    <p class="header-tagline">Main Futsal Gak Perlu Ribet, Booking Sekarang!</p>

      <div class="icon-box">
        <a href="riwayat_user.php" class="icon-link">
          <img src="../GAMBAR/transaksi.png" alt="Transaksi" class="transaksi-icon" />
          <p class="icon-label">Transaksi</p>
        </a>
      </div>


      <!-- Profil -->
      <div class="icon-box" id="profileBox">
        <i class="fas fa-user-circle profile-icon" onclick="toggleProfileMenu(event)"></i>
        <p class="icon-label">Profil</p>
        <div class="profile-dropdown" id="profileDropdown">
          <h4>Profil Pengguna</h4>
            <div class="profile-info">
              <div class="info-row"><span class="label">User ID:</span><span class="value">muhiqbal099</span></div>
              <div class="info-row"><span class="label">Nama:</span><span class="value">MUH IQBAL</span></div>
              <div class="info-row"><span class="label">No HP:</span><span class="value">0822-9010-2056</span></div>
              <div class="info-row"><span class="label">Email:</span><span class="value">muhikbal@gmail.com</span></div>
            </div>


          <button class="logout-button" onclick="logout()">Logout</button>
        </div>
      </div>

    </header>


  <main>
    <form class="search-form" method="GET">
      <input type="text" id="fieldName" placeholder="Cari Nama Lapangan" />
      <input type="text" id="fieldCity" placeholder="Cari Kota Lapangan" />
      <button type="submit">Konfirmasi</button>
    </form>

    <section class="fields-section">
      <div class="fields-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <a href="detail_lapangan.php?id=<?= $row['IdLapangan'] ?>" class="card">
            <img src="../GAMBAR/<?= $row['Gambar'] ?>" alt="<?= $row['NamaLapangan'] ?>">
            <h3><?= $row['NamaLapangan'] ?></h3>
            <p><?= $row['Alamat'] ?></p>
            <p class="harga">Rp <?= number_format($row['Harga'], 0, ',', '.') ?> / jam</p>
          </a>
        <?php endwhile; ?>
      </div>


    </section>
  </main>

  <script>

    let currentAction = '';

    function openModal(action) {
      currentAction = action;
      document.getElementById('modalTitle').innerText =
        `Pilih Peran untuk ${action === 'daftar' ? 'Daftar' : 'Masuk'}`;
      document.getElementById('roleModal').style.display = 'flex';
    }

    function redirectTo(role) {
      const url =
        role === 'user'
          ? (currentAction === 'daftar' ? 'daftar.html' : 'masuk.html')
          : (currentAction === 'daftar' ? 'daftar_pemilik.html' : 'masuk_pemilik.html');
      window.location.href = url;
    }

    window.onclick = function (e) {
      const modal = document.getElementById('roleModal');
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    };
    </script>
      
    <script>
    function toggleProfileMenu(event) {
      event.stopPropagation();
      const dropdown = document.getElementById("profileDropdown");
      dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    // Tutup dropdown kalau klik di luar
    document.addEventListener("click", function (e) {
      const dropdown = document.getElementById("profileDropdown");
      const profileBox = document.getElementById("profileBox");
      if (!profileBox.contains(e.target)) {
        dropdown.style.display = "none";
      }
    });

    function logout() {
      window.location.href = "beranda.html"; // ganti sesuai kebutuhanmu
    }
    </script>

</body>
</html>
