<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeController extends Controller
{
    /**
     * Generate QR Code as SVG response.
     * No GD extension needed — SVG works natively.
     */
    public function generate(string $data)
    {
        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'      => QRCode::ECC_M,
            'scale'         => 6,
            'imageBase64'   => false,
            'quietzoneSize' => 2,
            // Style: black modules on white background
            'svgDefs'       => '<style>.light{fill:#fff}.dark{fill:#000}</style>',
            'cssClass'      => 'qrcode',
        ]);

        $qrcode   = new QRCode($options);
        $svgData  = $qrcode->render($data);

        return response($svgData, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
