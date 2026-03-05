<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeController extends Controller
{
    /**
     * Generate QR Code as PNG image response.
     * Used in invoices/receipts for order tracking.
     */
    public function generate(string $data)
    {
        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'      => QRCode::ECC_M,
            'scale'         => 6,
            'imageBase64'   => false,
            'quietzoneSize' => 2,
        ]);

        $qrcode = new QRCode($options);
        $imageData = $qrcode->render($data);

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
