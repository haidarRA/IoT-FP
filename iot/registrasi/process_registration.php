<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rfid = $_POST['rfid'];
$name = $_POST['name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$allergy = $_POST['allergy'];
$contact = $_POST['contact'];

$sql = "INSERT INTO patients (rfid, name, dob, gender, address, allergy, contact) 
        VALUES ('$rfid', '$name', '$dob', '$gender', '$address', '$allergy', '$contact')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully. <a href='../registrasi'>Go back to the form</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
