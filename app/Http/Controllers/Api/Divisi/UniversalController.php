<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Controller;
use App\Services\ServisTahunAkademik;
use Illuminate\Http\Request;

class UniversalController extends Controller
{
    private $servis;

    public function __construct()
    {
        $this->servis = new ServisTahunAkademik;
    }

    public function tahunAkademik()
    {
        return $this->servis->getTahunAkademik();
    }

    public function tahunAkademikAktif()
    {
        return $this->servis->getTahunAkademikAktif();
    }

    public function tahunAkademikByKode(request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string',
        ]);
        $kode = $validated['kode'];
        if (! $kode) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter kode diperlukan',
            ], 400);
        }

        return $this->servis->getTahunAkademikByKode($kode);
    }
}
