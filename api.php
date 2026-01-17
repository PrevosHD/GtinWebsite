<?php
// Parameter auslesen
$text = $_GET['text'] ?? '';
$size = (int)($_GET['size'] ?? 200);

// Validierung
if (empty($text)) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Missing text parameter';
    exit;
}

$size = max(100, min(1000, $size));

// Library einbinden (using the existing phpqrcode from /var/www/qr)
require_once('/var/www/qr/phpqrcode/qrlib.php');

// Temporäre Datei
$tempFile = tempnam(sys_get_temp_dir(), 'qr_') . '.png';

// QR-Code generieren
QRcode::png($text, $tempFile, QR_ECLEVEL_M, 10, 2);

// Bild laden und auf gewünschte Größe skalieren
$im = imagecreatefrompng($tempFile);
$width = imagesx($im);
$height = imagesy($im);

// Auf gewünschte Größe skalieren
$final = imagecreatetruecolor($size, $size);
imagecopyresampled($final, $im, 0, 0, 0, 0, $size, $size, $width, $height);

// Ausgabe
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
imagepng($final, null, 9);

// Cleanup
imagedestroy($im);
imagedestroy($final);
unlink($tempFile);
?>

