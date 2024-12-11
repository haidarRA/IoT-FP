<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient = null;
$nikError = null;

if (isset($_GET['nik'])) {
    $nik = $_GET['nik'];

    $stmt = $conn->prepare("SELECT * FROM patients WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        $nikError = "No patient found with NIK: " . htmlspecialchars($nik);
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pasien</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Detail Pasien</h2>

        <?php if (!$patient): ?>
            <div class="card">
                <div class="card-body">
                    <form action="" method="GET" class="text-center">
                        <div class="mb-3">
                            <label for="nik" class="form-label">Masukkan NIK</label>
                            <input type="text" id="nik" name="nik" class="form-control" required>
                        </div>
                        <?php if ($nikError): ?>
                            <div class="text-danger mb-3"><?php echo $nikError; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Lihat Data</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h5><?php echo htmlspecialchars($patient['name']); ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>NIK:</strong> <?php echo htmlspecialchars($patient['nik']); ?></p>
                    <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($patient['dob']); ?></p>
                    <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
                    <p><strong>Alergi:</strong> <?php echo htmlspecialchars($patient['allergy']); ?></p>
                    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($patient['contact']); ?></p>
                </div>
                <div class="card-footer text-center">
                    <a href="../registrasi" class="btn btn-primary">Kembali ke Form</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
