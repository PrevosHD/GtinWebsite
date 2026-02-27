<?php
// Parameter auslesen
$text = $_GET['text'] ?? '';
$size = (int)($_GET['size'] ?? 200);
$fg = $_GET['fg'] ?? '000000'; // Foreground color (QR code)
$bg = $_GET['bg'] ?? 'ffffff'; // Background color

// Validierung (check if text parameter exists, not if it's empty - "0" is valid!)
if (!isset($_GET['text'])) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Missing text parameter';
    exit;
}

$size = max(100, min(1000, $size));

// Hex color validation and conversion
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 6) {
        return array(
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        );
    }
    return array('r' => 0, 'g' => 0, 'b' => 0);
}

// Library einbinden (using the existing phpqrcode from /var/www/qr)
require_once('/var/www/qr/phpqrcode/qrlib.php');

// Temporäre Datei
$tempFile = tempnam(sys_get_temp_dir(), 'qr_') . '.png';

// QR-Code generieren (always black on white first)
QRcode::png($text, $tempFile, QR_ECLEVEL_M, 10, 2);

// Bild laden
$im = imagecreatefrompng($tempFile);
$width = imagesx($im);
$height = imagesy($im);

// Auf gewünschte Größe skalieren
$final = imagecreatetruecolor($size, $size);

// Parse colors
$fgColor = hexToRgb($fg);
$bgColor = hexToRgb($bg);

// Allocate colors
$fgColorId = imagecolorallocate($final, $fgColor['r'], $fgColor['g'], $fgColor['b']);
$bgColorId = imagecolorallocate($final, $bgColor['r'], $bgColor['g'], $bgColor['b']);

// Fill background
imagefilledrectangle($final, 0, 0, $size, $size, $bgColorId);

// Copy and recolor the QR code
imagecopyresampled($final, $im, 0, 0, 0, 0, $size, $size, $width, $height);

// Replace black pixels with foreground color and white with background color
for ($y = 0; $y < $size; $y++) {
    for ($x = 0; $x < $size; $x++) {
        $pixel = imagecolorat($final, $x, $y);
        $rgb = imagecolorsforindex($final, $pixel);

        // If pixel is black (or very dark), replace with foreground color
        if ($rgb['red'] < 128 && $rgb['green'] < 128 && $rgb['blue'] < 128) {
            imagesetpixel($final, $x, $y, $fgColorId);
        } else {
            // Otherwise replace with background color
            imagesetpixel($final, $x, $y, $bgColorId);
        }
    }
}

// Ausgabe
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
imagepng($final, null, 9);

// Cleanup
imagedestroy($im);
imagedestroy($final);
unlink($tempFile);
?>

