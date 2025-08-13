<?php
// Jaza na data yako ya DB hapa:
$servername = "localhost";
$username = "root";
$password = "";  // Badilisha kama unatumia password
$dbname = "harusi";

// Unganisha DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Kagua kama connection iko sawa
if ($conn->connect_error) {
    die("Kosa la kuunganisha DB: " . $conn->connect_error);
}

// Pokea data kutoka form kwa usalama
$jina = $conn->real_escape_string(trim($_POST['jina']));
$kiasi = intval($_POST['kiasi']);

// Hakikisha data inakidhi vigezo vya msingi
if (empty($jina) || $kiasi < 100) {
    die("Tafadhali jaza jina na kiasi sahihi.");
}

// Tengeneza token ya kipekee
$token = uniqid('harusi_');

// Ingiza data DB
$sql = "INSERT INTO malipo (jina, kiasi, token) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $jina, $kiasi, $token);

if ($stmt->execute()) {
    // Tumeng'oa mafanikio, sasa tengeneza QR code

    require_once 'phpqrcode/qrlib.php';  // Hakikisha path ni sahihi

    $folder = "../images/qrcodes/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);  // Unda folder kama haipo
    }

    $file = $folder . $token . ".png";

    // Tengeneza QR code kwa token
    QRcode::png($token, $file, QR_ECLEVEL_L, 6);

    // Onyesha ujumbe na QR code
    echo "<h2>Asante $jina kwa kuchangia TZS $kiasi!</h2>";
    echo "<p>Hii hapa QR code yako ya uthibitisho:</p>";
    echo "<img src='../images/qrcodes/$token.png' alt='QR Code'>";

    echo "<br><br><a href='../payment.html' style='
      background:#880e4f; color:#fff; padding:10px 20px; border-radius:20px; text-decoration:none;'>Rudi Malipo</a>";

} else {
    echo "Kuna tatizo lililotokea: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
