<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinShieldController extends Controller
{
    /** Nilai awal (initial default state) tiap halaman. Satu sumber kebenaran untuk server & frontend. */
    private const JUDOL = [
        'saldoAwal'        => 1000000,
        'nominalPutaran'   => 10000,
        'jumlahPutaran'    => 500,
        'peluangMenang'    => 45,
        'multiplierMenang' => 2,
    ];

    private const PINJOL = [
        'pokokPinjaman' => 1000000,
        'bungaHarian'   => 1,
        'tenorHari'     => 30,
        'biayaTetap'    => 100000,
        'jenisBunga'    => 'flat',
    ];

    private const UANG = [
        'pemasukanBulanan' => 3000000,
        'persenKebutuhan'  => 50,
        'persenKeinginan'  => 30,
        'persenMasaDepan'  => 20,
    ];

    // ------------------------------------------------------------------
    // Halaman
    // ------------------------------------------------------------------

    public function beranda(): View
    {
        $judol  = $this->hitungEvJudol(self::JUDOL);
        $pinjol = $this->hitungTagihan(self::PINJOL, self::PINJOL['tenorHari']);

        $state = [
            'judol' => [
                'ev'        => $judol['evPerPutaran'],
                'houseEdge' => $judol['houseEdge'],
                'teks'      => $this->rupiah($judol['evPerPutaran']),
                'dariUser'  => false,
            ],
            'pinjol' => [
                'total'    => $pinjol['totalTagihan'],
                'pokok'    => self::PINJOL['pokokPinjaman'],
                'tenor'    => self::PINJOL['tenorHari'],
                'teks'     => $this->rupiah($pinjol['totalTagihan']),
                'dariUser' => false,
            ],
            'uang' => [
                'kebutuhan' => self::UANG['persenKebutuhan'],
                'keinginan' => self::UANG['persenKeinginan'],
                'masaDepan' => self::UANG['persenMasaDepan'],
                'teks'      => self::UANG['persenKebutuhan'] . ' / ' . self::UANG['persenKeinginan'] . ' / ' . self::UANG['persenMasaDepan'],
                'dariUser'  => false,
            ],
            'langkah' => [
                ['id' => 'catat',   'judul' => 'Catat uang yang benar-benar masuk', 'deskripsi' => 'Pisahkan pemasukan tetap dan yang belum pasti.'],
                ['id' => 'risiko',  'judul' => 'Pilih satu risiko untuk dipahami',  'deskripsi' => 'Baca angka dan asumsi, bukan hanya hasil akhirnya.'],
                ['id' => 'cadangan', 'judul' => 'Sisihkan cadangan sekecil apa pun', 'deskripsi' => 'Mulai dari nominal yang realistis untukmu.'],
            ],
        ];

        return view('beranda', compact('state'));
    }

    public function edukasiJudol(): View
    {
        return view('edukasi-judol', ['defaults' => self::JUDOL]);
    }

    public function kalkulatorPinjol(): View
    {
        return view('kalkulator-pinjol', ['defaults' => self::PINJOL]);
    }

    public function kelolaUang(): View
    {
        $defaults = [
            'pemasukanBulanan' => self::UANG['pemasukanBulanan'],
            'persen' => [
                'kebutuhan' => self::UANG['persenKebutuhan'],
                'keinginan' => self::UANG['persenKeinginan'],
                'masaDepan' => self::UANG['persenMasaDepan'],
            ],
            'qty' => ['desain' => 5, 'les' => 8, 'admin' => 2],
        ];

        $ide = [
            [
                'id'        => 'desain',
                'judul'     => 'Desain konten UMKM',
                'kategori'  => 'Kreatif',
                'icon'      => 'pen',
                'min'       => 50000,
                'max'       => 150000,
                'satuan'    => 'desain',
                'deskripsi' => 'Untuk yang nyaman memakai Canva atau alat desain. Butuh laptop/ponsel dan portofolio sederhana.',
                'langkah'   => [
                    'Buat 3 contoh konten untuk satu jenis usaha.',
                    'Tawarkan paket kecil ke UMKM sekitar.',
                    'Sepakati revisi, tenggat, dan pembayaran.',
                ],
            ],
            [
                'id'        => 'les',
                'judul'     => 'Les privat pelajaran',
                'kategori'  => 'Berbagi ilmu',
                'icon'      => 'book',
                'min'       => 40000,
                'max'       => 80000,
                'satuan'    => 'jam',
                'deskripsi' => 'Pilih materi yang benar-benar kamu kuasai. Mulai dari siswa sekitar rumah atau jaringan keluarga.',
                'langkah'   => [
                    'Pilih satu mata pelajaran dan jenjang.',
                    'Siapkan latihan dan sesi perkenalan.',
                    'Atur jadwal aman di luar sekolah/kuliah.',
                ],
            ],
            [
                'id'        => 'admin',
                'judul'     => 'Admin katalog UMKM',
                'kategori'  => 'Terorganisir',
                'icon'      => 'clipboard',
                'min'       => 150000,
                'max'       => 300000,
                'satuan'    => 'proyek',
                'deskripsi' => 'Contoh proyek: rapikan 20 produk di katalog. Butuh ketelitian, spreadsheet, dan internet.',
                'langkah'   => [
                    'Buat contoh tabel produk yang rapi.',
                    'Tawarkan lingkup kerja yang jelas.',
                    'Gunakan akses terbatas; jaga data usaha.',
                ],
            ],
        ];

        return view('kelola-uang', [
            'defaults' => $defaults,
            'ide'      => $ide,
            'kategori' => ['Semua', 'Kreatif', 'Berbagi ilmu', 'Terorganisir'],
        ]);
    }

    // ------------------------------------------------------------------
    // Endpoint kalkulasi (opsional, formula identik dengan frontend)
    // ------------------------------------------------------------------

    public function hitungJudol(Request $request): JsonResponse
    {
        $data = $request->validate([
            'saldoAwal'        => 'required|numeric|min:0|max:9000000000000',
            'nominalPutaran'   => 'required|numeric|min:0|max:9000000000000',
            'jumlahPutaran'    => 'required|integer|min:1|max:5000',
            'peluangMenang'    => 'required|numeric|between:0,100',
            'multiplierMenang' => 'required|numeric|min:1|max:1000',
            'simulasi'         => 'sometimes|boolean',
        ]);

        $hasil = $this->hitungEvJudol($data);

        if (! empty($data['simulasi'])) {
            $hasil['lintasan'] = $this->simulasiLintasan($data, $hasil);
        }

        return response()->json($hasil);
    }

    public function hitungPinjol(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pokokPinjaman' => 'required|numeric|min:0|max:9000000000000',
            'bungaHarian'   => 'required|numeric|between:0,100',
            'tenorHari'     => 'required|integer|min:1|max:365',
            'biayaTetap'    => 'required|numeric|min:0|max:9000000000000',
            'jenisBunga'    => 'required|in:flat,majemuk',
        ]);

        $tenor = (int) $data['tenorHari'];
        $akhir = $this->hitungTagihan($data, $tenor);

        $baris = array_map(function (int $hari) use ($data) {
            $t = $this->hitungTagihan($data, $hari);

            return ['hari' => $hari, 'bunga' => $t['bunga'], 'totalTagihan' => $t['totalTagihan']];
        }, $this->titikHari($tenor));

        return response()->json($akhir + ['baris' => $baris]);
    }

    // ------------------------------------------------------------------
    // Logika perhitungan
    // ------------------------------------------------------------------

    private function hitungEvJudol(array $p): array
    {
        $nominal       = (float) $p['nominalPutaran'];
        $peluangMenang = (float) $p['peluangMenang'];
        $peluangKalah  = 100 - $peluangMenang;
        $labaMenang    = $nominal * ((float) $p['multiplierMenang'] - 1);
        $rugiKalah     = $nominal;

        $ev        = ($peluangMenang / 100) * $labaMenang - ($peluangKalah / 100) * $rugiKalah;
        $houseEdge = $nominal > 0 ? (-$ev / $nominal) * 100 : 0.0;

        return [
            'peluangKalah'      => $peluangKalah,
            'labaMenang'        => $labaMenang,
            'rugiKalah'         => $rugiKalah,
            'evPerPutaran'      => $ev,
            'houseEdge'         => $houseEdge,
            'saldoHarapanAkhir' => (float) $p['saldoAwal'] + ((int) $p['jumlahPutaran'] * $ev),
        ];
    }

    private function simulasiLintasan(array $p, array $hitung): array
    {
        $n       = (int) $p['jumlahPutaran'];
        $nominal = (float) $p['nominalPutaran'];
        $saldo   = (float) $p['saldoAwal'];
        $menang  = 0;
        $kalah   = 0;
        $step    = max(1, intdiv($n, 50));
        $titik   = [['putaran' => 0, 'saldo' => $saldo]];

        for ($r = 1; $r <= $n; $r++) {
            if ($nominal > 0 && $saldo >= $nominal) {
                if ((mt_rand() / mt_getrandmax()) * 100 < (float) $p['peluangMenang']) {
                    $saldo += $hitung['labaMenang'];
                    $menang++;
                } else {
                    $saldo -= $hitung['rugiKalah'];
                    $kalah++;
                }
            }

            if ($r % $step === 0 || $r === $n) {
                $titik[] = ['putaran' => $r, 'saldo' => $saldo];
            }
        }

        return ['saldoAkhir' => $saldo, 'menang' => $menang, 'kalah' => $kalah, 'titik' => $titik];
    }

    private function bunga(array $p, int $t): float
    {
        $pokok = (float) $p['pokokPinjaman'];
        $b     = (float) $p['bungaHarian'] / 100;

        if (($p['jenisBunga'] ?? 'flat') === 'majemuk') {
            return $pokok * (pow(1 + $b, $t) - 1);
        }

        return $pokok * $b * $t;
    }

    private function hitungTagihan(array $p, int $t): array
    {
        $bunga       = $this->bunga($p, $t);
        $biaya       = (float) $p['biayaTetap'];
        $pokok       = (float) $p['pokokPinjaman'];
        $totalBeban  = $biaya + $bunga;

        return [
            'bunga'           => $bunga,
            'totalBeban'      => $totalBeban,
            'persentaseBeban' => $pokok > 0 ? ($totalBeban / $pokok) * 100 : 0.0,
            'totalTagihan'    => $pokok + $biaya + $bunga,
        ];
    }

    /** 5 titik hari proporsional terhadap tenor (duplikat dibuang untuk tenor sangat pendek). */
    private function titikHari(int $tenor): array
    {
        $titik = [
            0,
            (int) round($tenor * 0.23),
            (int) round($tenor * 0.46),
            (int) round($tenor * 0.7),
            $tenor,
        ];

        $titik = array_values(array_unique($titik));
        sort($titik);

        return $titik;
    }

    private function rupiah(float $angka): string
    {
        $bulat = (int) round($angka);

        return ($bulat < 0 ? '-' : '') . 'Rp' . number_format(abs($bulat), 0, ',', '.');
    }
}
