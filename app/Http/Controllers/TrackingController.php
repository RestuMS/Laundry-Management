<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\OrderTrackingService;
use App\Http\Requests\TrackOrderRequest;

class TrackingController extends Controller
{
    protected $trackingService;

    public function __construct(OrderTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function index(Request $request)
    {
        return view('tracking.index');
    }

    public function search(TrackOrderRequest $request)
    {
        $data = $this->trackingService->getTrackingData($request->input('q'));

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan. Periksa kembali Nomor HP / Kode Order Anda.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
