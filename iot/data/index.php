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
$rfidError = null;

if (isset($_GET['rfid'])) {
    $rfid = $_GET['rfid'];

    $stmt = $conn->prepare("SELECT * FROM patients WHERE rfid = ?");
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        $rfidError = "No patient found with RFID UID: " . htmlspecialchars($rfid);
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
                        <?php if ($rfidError): ?>
                            <div class="text-danger mb-3"><?php echo $rfidError; ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h5><?php echo htmlspecialchars($patient['name']); ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>RFID UID:</strong> <?php echo htmlspecialchars($patient['rfid']); ?></p>
                    <p><strong>No. Antrian:</strong> <?php echo htmlspecialchars($patient['id']); ?></p>
                    <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($patient['dob']); ?></p>
                    <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
                    <p><strong>Alergi:</strong> <?php echo htmlspecialchars($patient['allergy']); ?></p>
                    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($patient['contact']); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
