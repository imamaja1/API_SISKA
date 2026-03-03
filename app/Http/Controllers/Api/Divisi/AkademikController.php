<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Controller;
use App\Services\ServisTahunAkademik;

class AkademikController extends Controller
{
    private $ServisTahunAkademik;

    public function __construct()
    {
        $this->ServisTahunAkademik = new ServisTahunAkademik;
    }

    public function StatusPerkuliahan()
    {
        $data = $this->ServisTahunAkademik->getTahunAkademik();

        return $data;
    }
}
