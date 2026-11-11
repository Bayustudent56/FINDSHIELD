"""FinShield: aplikasi literasi keuangan interaktif (Flask).

Backend bertugas untuk routing, initial state, dan endpoint kalkulasi opsional.
Seluruh interaktivitas real-time (kalkulator, simulasi, tabel, grafik) berjalan di
frontend dengan Alpine.js + Chart.js, tanpa reload halaman.
"""

import math
import os
import random
from datetime import datetime

from flask import Flask, jsonify, render_template, request
from markupsafe import Markup

app = Flask(__name__)
app.config["JSON_SORT_KEYS"] = False

# ----------------------------------------------------------------------
# Nilai awal (initial default state) tiap halaman
# ----------------------------------------------------------------------
JUDOL = {
    "saldoAwal": 1_000_000,
    "nominalPutaran": 10_000,
    "jumlahPutaran": 500,
    "peluangMenang": 45,
    "multiplierMenang": 2,
}

PINJOL = {
    "pokokPinjaman": 1_000_000,
    "bungaHarian": 1,
    "tenorHari": 30,
    "biayaTetap": 100_000,
    "jenisBunga": "flat",
}

UANG = {
    "pemasukanBulanan": 3_000_000,
    "persen": {"kebutuhan": 50, "keinginan": 30, "masaDepan": 20},
    "qty": {"desain": 5, "les": 8, "admin": 2},
}

IDE_KERJA = [
    {
        "id": "desain",
        "judul": "Desain konten UMKM",
        "kategori": "Kreatif",
        "icon": "pen",
        "min": 50_000,
        "max": 150_000,
        "satuan": "desain",
        "deskripsi": "Untuk yang nyaman memakai Canva atau alat desain. Butuh laptop/ponsel dan portofolio sederhana.",
        "langkah": [
            "Buat 3 contoh konten untuk satu jenis usaha.",
            "Tawarkan paket kecil ke UMKM sekitar.",
            "Sepakati revisi, tenggat, dan pembayaran.",
        ],
    },
    {
        "id": "les",
        "judul": "Les privat pelajaran",
        "kategori": "Berbagi ilmu",
        "icon": "book",
        "min": 40_000,
        "max": 80_000,
        "satuan": "jam",
        "deskripsi": "Pilih materi yang benar-benar kamu kuasai. Mulai dari siswa sekitar rumah atau jaringan keluarga.",
        "langkah": [
            "Pilih satu mata pelajaran dan jenjang.",
            "Siapkan latihan dan sesi perkenalan.",
            "Atur jadwal aman di luar sekolah/kuliah.",
        ],
    },
    {
        "id": "admin",
        "judul": "Admin katalog UMKM",
        "kategori": "Terorganisir",
        "icon": "clipboard",
        "min": 150_000,
        "max": 300_000,
        "satuan": "proyek",
        "deskripsi": "Contoh proyek: rapikan 20 produk di katalog. Butuh ketelitian, spreadsheet, dan internet.",
        "langkah": [
            "Buat contoh tabel produk yang rapi.",
            "Tawarkan lingkup kerja yang jelas.",
            "Gunakan akses terbatas; jaga data usaha.",
        ],
    },
]

KATEGORI_IDE = ["Semua", "Kreatif", "Berbagi ilmu", "Terorganisir"]

LANGKAH_MINGGUAN = [
    {"id": "catat", "judul": "Catat uang yang benar-benar masuk", "deskripsi": "Pisahkan pemasukan tetap dan yang belum pasti."},
    {"id": "risiko", "judul": "Pilih satu risiko untuk dipahami", "deskripsi": "Baca angka dan asumsi, bukan hanya hasil akhirnya."},
    {"id": "cadangan", "judul": "Sisihkan cadangan sekecil apa pun", "deskripsi": "Mulai dari nominal yang realistis untukmu."},
]

# ----------------------------------------------------------------------
# Ikon SVG (dipanggil di template: {{ icon('shield', 'h-5 w-5') }})
# ----------------------------------------------------------------------
ICON_PATHS = {
    "grid": '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
    "chart": '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
    "calculator": '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8"/><path d="M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 19h.01M12 19h.01"/>',
    "wallet": '<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>',
    "shield": '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
    "sprout": '<path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"/>',
    "help": '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/>',
    "arrow-right": '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    "arrow-up-right": '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
    "info": '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    "sigma": '<path d="M18 7V5a1 1 0 0 0-1-1H6.5a.5.5 0 0 0-.4.8l4.5 6a2 2 0 0 1 0 2.4l-4.5 6a.5.5 0 0 0 .4.8H17a1 1 0 0 0 1-1v-2"/>',
    "scale": '<path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>',
    "sliders": '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
    "heart": '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
    "file": '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>',
    "list-check": '<path d="m3 17 2 2 4-4"/><path d="m3 7 2 2 4-4"/><path d="M13 6h8"/><path d="M13 12h8"/><path d="M13 18h8"/>',
    "messages": '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    "pen": '<path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.586 7.586"/><circle cx="11" cy="11" r="2"/>',
    "book": '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
    "clipboard": '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6"/><path d="M9 16h4"/>',
    "umbrella": '<path d="M22 12a10.06 10.06 0 0 0-20 0Z"/><path d="M12 12v8a2 2 0 0 0 4 0"/><path d="M12 2v1"/>',
    "pie": '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
    "menu": '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
    "x": '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    "alert": '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
}


def icon(name: str, css_class: str = "h-5 w-5") -> Markup:
    paths = ICON_PATHS.get(name, "")
    return Markup(
        f'<svg class="{css_class}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" '
        f'stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" '
        f'aria-hidden="true">{paths}</svg>'
    )


# ----------------------------------------------------------------------
# Format & rumus (identik dengan logika frontend)
# ----------------------------------------------------------------------
def angka(nilai) -> str:
    """1000000 -> '1.000.000'"""
    return f"{int(round(float(nilai))):,}".replace(",", ".")


def rupiah(nilai) -> str:
    """-540000 -> '-Rp540.000'"""
    bulat = int(round(float(nilai)))
    return ("-" if bulat < 0 else "") + "Rp" + angka(abs(bulat))


@app.context_processor
def variabel_global():
    return {"tahun": datetime.now().year}


app.jinja_env.globals["icon"] = icon
app.jinja_env.filters["rupiah"] = rupiah
app.jinja_env.filters["angka"] = angka


def hitung_ev_judol(p: dict) -> dict:
    nominal = float(p["nominalPutaran"])
    peluang_menang = float(p["peluangMenang"])
    peluang_kalah = 100 - peluang_menang
    laba_menang = nominal * (float(p["multiplierMenang"]) - 1)
    rugi_kalah = nominal

    ev = (peluang_menang / 100) * laba_menang - (peluang_kalah / 100) * rugi_kalah
    house_edge = (-ev / nominal) * 100 if nominal > 0 else 0.0

    return {
        "peluangKalah": peluang_kalah,
        "labaMenang": laba_menang,
        "rugiKalah": rugi_kalah,
        "evPerPutaran": ev,
        "houseEdge": house_edge,
        "saldoHarapanAkhir": float(p["saldoAwal"]) + int(p["jumlahPutaran"]) * ev,
    }


def simulasi_lintasan(p: dict, hitung: dict) -> dict:
    n = int(p["jumlahPutaran"])
    nominal = float(p["nominalPutaran"])
    saldo = float(p["saldoAwal"])
    menang = kalah = 0
    step = max(1, n // 50)
    titik = [{"putaran": 0, "saldo": saldo}]

    for r in range(1, n + 1):
        if nominal > 0 and saldo >= nominal:
            if random.random() * 100 < float(p["peluangMenang"]):
                saldo += hitung["labaMenang"]
                menang += 1
            else:
                saldo -= hitung["rugiKalah"]
                kalah += 1
        if r % step == 0 or r == n:
            titik.append({"putaran": r, "saldo": saldo})

    return {"saldoAkhir": saldo, "menang": menang, "kalah": kalah, "titik": titik}


def bunga_pada(p: dict, t: int) -> float:
    pokok = float(p["pokokPinjaman"])
    b = float(p["bungaHarian"]) / 100
    if p.get("jenisBunga") == "majemuk":
        return pokok * (math.pow(1 + b, t) - 1)
    return pokok * b * t


def hitung_tagihan(p: dict, t: int) -> dict:
    bunga = bunga_pada(p, t)
    biaya = float(p["biayaTetap"])
    pokok = float(p["pokokPinjaman"])
    total_beban = biaya + bunga
    return {
        "bunga": bunga,
        "totalBeban": total_beban,
        "persentaseBeban": (total_beban / pokok) * 100 if pokok > 0 else 0.0,
        "totalTagihan": pokok + biaya + bunga,
    }


def titik_hari(tenor: int) -> list:
    kandidat = [0, round(tenor * 0.23), round(tenor * 0.46), round(tenor * 0.7), tenor]
    return sorted(set(int(x) for x in kandidat))


# ----------------------------------------------------------------------
# Halaman
# ----------------------------------------------------------------------
@app.route("/")
def beranda():
    judol = hitung_ev_judol(JUDOL)
    pinjol = hitung_tagihan(PINJOL, PINJOL["tenorHari"])
    persen = UANG["persen"]

    state = {
        "judol": {"ev": judol["evPerPutaran"], "houseEdge": judol["houseEdge"], "teks": rupiah(judol["evPerPutaran"]), "dariUser": False},
        "pinjol": {
            "total": pinjol["totalTagihan"],
            "pokok": PINJOL["pokokPinjaman"],
            "tenor": PINJOL["tenorHari"],
            "teks": rupiah(pinjol["totalTagihan"]),
            "dariUser": False,
        },
        "uang": {
            "kebutuhan": persen["kebutuhan"],
            "keinginan": persen["keinginan"],
            "masaDepan": persen["masaDepan"],
            "teks": f"{persen['kebutuhan']} / {persen['keinginan']} / {persen['masaDepan']}",
            "dariUser": False,
        },
        "langkah": LANGKAH_MINGGUAN,
    }
    return render_template("beranda.html", state=state)


@app.route("/edukasi-judol")
def edukasi_judol():
    return render_template("edukasi_judol.html", defaults=JUDOL)


@app.route("/kalkulator-pinjol")
def kalkulator_pinjol():
    return render_template("kalkulator_pinjol.html", defaults=PINJOL)


@app.route("/kelola-uang")
def kelola_uang():
    return render_template("kelola_uang.html", defaults=UANG, ide=IDE_KERJA, kategori=KATEGORI_IDE)


# ----------------------------------------------------------------------
# Endpoint kalkulasi sisi server (opsional; frontend menghitung sendiri secara real-time)
# ----------------------------------------------------------------------
def _angka(data: dict, kunci: str, minimum: float, maksimum: float, bulat: bool = False):
    if kunci not in data:
        raise ValueError(f"'{kunci}' wajib diisi")
    try:
        nilai = float(data[kunci])
    except (TypeError, ValueError):
        raise ValueError(f"'{kunci}' harus berupa angka")
    if not math.isfinite(nilai) or nilai < minimum or nilai > maksimum:
        raise ValueError(f"'{kunci}' harus antara {minimum:g} dan {maksimum:g}")
    return int(nilai) if bulat else nilai


@app.post("/api/judol/hitung")
def api_judol():
    data = request.get_json(silent=True) or {}
    try:
        p = {
            "saldoAwal": _angka(data, "saldoAwal", 0, 9e12),
            "nominalPutaran": _angka(data, "nominalPutaran", 0, 9e12),
            "jumlahPutaran": _angka(data, "jumlahPutaran", 1, 5000, bulat=True),
            "peluangMenang": _angka(data, "peluangMenang", 0, 100),
            "multiplierMenang": _angka(data, "multiplierMenang", 1, 1000),
        }
    except ValueError as e:
        return jsonify({"error": str(e)}), 422

    hasil = hitung_ev_judol(p)
    if data.get("simulasi"):
        hasil["lintasan"] = simulasi_lintasan(p, hasil)
    return jsonify(hasil)


@app.post("/api/pinjol/hitung")
def api_pinjol():
    data = request.get_json(silent=True) or {}
    try:
        p = {
            "pokokPinjaman": _angka(data, "pokokPinjaman", 0, 9e12),
            "bungaHarian": _angka(data, "bungaHarian", 0, 100),
            "tenorHari": _angka(data, "tenorHari", 1, 365, bulat=True),
            "biayaTetap": _angka(data, "biayaTetap", 0, 9e12),
            "jenisBunga": data.get("jenisBunga", "flat"),
        }
    except ValueError as e:
        return jsonify({"error": str(e)}), 422

    if p["jenisBunga"] not in ("flat", "majemuk"):
        return jsonify({"error": "'jenisBunga' harus 'flat' atau 'majemuk'"}), 422

    hasil = hitung_tagihan(p, p["tenorHari"])
    hasil["baris"] = [
        {"hari": h, "bunga": hitung_tagihan(p, h)["bunga"], "totalTagihan": hitung_tagihan(p, h)["totalTagihan"]}
        for h in titik_hari(p["tenorHari"])
    ]
    return jsonify(hasil)


@app.after_request
def header_keamanan(resp):
    resp.headers.setdefault("X-Content-Type-Options", "nosniff")
    resp.headers.setdefault("Referrer-Policy", "strict-origin-when-cross-origin")
    resp.headers.setdefault("X-Frame-Options", "SAMEORIGIN")
    return resp


@app.errorhandler(404)
def tidak_ditemukan(_e):
    return render_template("beranda.html", state=_state_kosong()), 404


def _state_kosong():
    """State minimal agar halaman 404 tetap tampil memakai layout Beranda."""
    judol = hitung_ev_judol(JUDOL)
    pinjol = hitung_tagihan(PINJOL, PINJOL["tenorHari"])
    p = UANG["persen"]
    return {
        "judol": {"ev": judol["evPerPutaran"], "houseEdge": judol["houseEdge"], "teks": rupiah(judol["evPerPutaran"]), "dariUser": False},
        "pinjol": {"total": pinjol["totalTagihan"], "pokok": PINJOL["pokokPinjaman"], "tenor": PINJOL["tenorHari"], "teks": rupiah(pinjol["totalTagihan"]), "dariUser": False},
        "uang": {"kebutuhan": p["kebutuhan"], "keinginan": p["keinginan"], "masaDepan": p["masaDepan"], "teks": f"{p['kebutuhan']} / {p['keinginan']} / {p['masaDepan']}", "dariUser": False},
        "langkah": LANGKAH_MINGGUAN,
    }


if __name__ == "__main__":
    app.run(
        host=os.environ.get("HOST", "127.0.0.1"),
        port=int(os.environ.get("PORT", "5000")),
        debug=os.environ.get("FLASK_DEBUG", "0") == "1",
    )
