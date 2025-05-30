  <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../PHP/Koneksi.php';

if (!isset($_GET['id'])) {
  echo "ID lapangan tidak ditemukan.";
  exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM lapangan WHERE IdLapangan = ?");
$stmt->bind_param("i", $id);  // "i" berarti integer
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();


if (!$data) {
  echo "Lapangan tidak ditemukan. Coba cek ID: $id";
  exit();
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Lapangan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #5fb1ff, #014c92);
      color: #333;
    }
    .detail-container {
      max-width: 800px;
      margin: 40px auto;
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .field-image {
      width: 100%;
      height: 300px;
      object-fit: cover;
    }
    .field-content {
      padding: 25px;
    }
    .field-content h2 {
      margin-top: 0;
      color: #1e88e5;
      font-size: 28px;
    }
    .field-info {
      margin-bottom: 15px;
    }
    .field-info strong {
      display: inline-block;
      width: 120px;
      color: #555;
    }
    .btn-book {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      background-color: #1e88e5;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    .btn-book:hover {
      background-color: #1565c0;
    }
    .detail-actions {
  display: flex;
  gap: 20px;
  margin-top: 30px;
}

.btn-detail {
  text-decoration: none;
  padding: 12px 24px;
  font-size: 1rem;
  font-weight: bold;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  transition: background-color 0.3s, transform 0.2s;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

  .btn-detail.pesan {
    background-color: #1e88e5;
    color: white;
  }

  .btn-detail.pesan:hover {
    background-color: #1565c0;
    transform: translateY(-2px);
  }

  .btn-detail.batal {
    background-color: #e53935;
    color: white;
  }

  .btn-detail.batal:hover {
    background-color: #c62828;
    transform: translateY(-2px);
  }

  </style>
</head>
<body>
  <div class="detail-container">
    <img src="../GAMBAR/<?= $data['Gambar'] ?>" alt="Foto Lapangan" class="field-image">
    <div class="field-content">
      <h2><?= $data['NamaLapangan'] ?></h2>

      <div class="field-info">
        <strong>Alamat:</strong> <?= $data['Alamat'] ?>
      </div>

      <div class="field-info">
        <strong>Fasilitas:</strong> <?= $data['Fasilitas'] ?>
      </div>

      <div class="field-info">
        <strong>Pemilik:</strong> <?= $data['PemilikId'] ?>
      </div>

      <div class="field-info">
        <strong>Harga:</strong> Rp <?= number_format($data['Harga'], 0, ',', '.') ?> / jam
      </div>

      <div class="field-info">
        <strong>Jam Buka:</strong> <?= $data['Jam_Buka'] ?>
      </div>

      <div class="field-info">
        <strong>Jam Tutup:</strong> <?= $data['Jam_Tutup'] ?>
      </div>

      <div class="detail-actions">
        <a href="pembayaran.php?id=<?= $data['IdLapangan'] ?>&harga=<?= $data['Harga'] ?>" class="btn-detail pesan">
          <i class="fas fa-check"></i> Pesan Sekarang
        </a>
        <a href="beranda_user.php" class="btn-detail batal">
          <i class="fas fa-times"></i> Batal
        </a>
      </div>

    </div>
  </div>
</body>
</html>
