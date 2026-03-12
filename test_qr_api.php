<?php
require 'vendor/autoload.php';

try {
    // Try SVG output (no GD needed)
    $opts = new \chillerlan\QRCode\QROptions([
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
        'scale' => 6,
        'imageBase64' => false,
        'svgViewBoxSize' => 200,
    ]);
    $qr = new \chillerlan\QRCode\QRCode($opts);
    $data = $qr->render('TEST-ORDER-001');
    file_put_contents('test_qr.svg', $data);
    echo "SVG OK - " . strlen($data) . " bytes\n";
    echo substr($data, 0, 100) . "\n";
} catch (\Throwable $e) {
    echo "SVG ERROR: " . $e->getMessage() . "\n";
    
    // list constants
    $rc = new ReflectionClass('\chillerlan\QRCode\QRCode');
    foreach ($rc->getConstants() as $k => $v) {
        if (str_starts_with($k, 'OUTPUT')) echo "$k = $v\n";
    }
}
