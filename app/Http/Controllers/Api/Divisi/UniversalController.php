<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Controller;
use App\Services\ServisProgramStudi;
use App\Services\ServisTahunAkademik;
use Illuminate\Http\Request;

class UniversalController extends Controller
{
    private $servisTahunAkademik;

    private $servisProgramStudi;

    public function __construct()
    {
        $this->servisTahunAkademik = new ServisTahunAkademik;
        $this->servisProgramStudi = new ServisProgramStudi;
    }

    public function tahunAkademik()
    {
        return $this->servisTahunAkademik->getTahunAkademik();
    }

    public function tahunAkademikAktif()
    {
        return $this->servisTahunAkademik->getTahunAkademikAktif();
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

    public function program_studi()
    {
        return $this->servisProgramStudi->getProgramStudi();
    }
}
