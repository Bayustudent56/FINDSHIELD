@extends('layouts.app')

@section('title', 'Kalkulator pinjol')
@section('page', 'Kalkulator pinjol')

@section('content')
<div x-data="kalkulatorPinjol(@js($defaults))" x-cloak class="space-y-6">

    <header>
        <p class="fs-eyebrow">Hitung dampak • Kalkulator edukasi</p>
        <h1 class="fs-h1 mt-2">Bunga kecil per hari, beban besar di akhir.</h1>
        <p class="mt-3 text-[15px] text-slate-500">Lihat akumulasi biaya dalam rupiah sebelum mengambil keputusan tentang pinjaman.</p>
    </header>

    <div class="flex gap-3 rounded-xl bg-warn-bg px-5 py-4 text-warn-text">
        <x-icon name="info" class="mt-0.5 h-[18px] w-[18px] shrink-0" />
        <div>
            <p class="text-sm font-medium">Asumsi simulasi, bukan tarif resmi atau nasihat hukum</p>
            <p class="mt-1 text-[13px] leading-relaxed">Angka ini contoh untuk memahami risiko pinjol ilegal, bukan penawaran pinjaman. Kontrak nyata bisa memiliki biaya, denda, dan metode bunga berbeda.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">

        {{-- Kolom kiri: form --}}
        <div class="space-y-5">
            <section class="fs-card">
                <h2 class="fs-h2">Detail pinjaman</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="fs-label" for="pokokPinjaman">Pokok pinjaman</label>
                        <input id="pokokPinjaman" type="text" inputmode="numeric" autocomplete="off" class="fs-input"
                               x-bind:value="formatRupiah(pokokPinjaman)"
                               x-on:input="pokokPinjaman = parseRupiah($event.target.value); $event.target.value = formatRupiah(pokokPinjaman)">
                    </div>

                    <div>
                        <label class="fs-label" for="bungaHarian">Bunga per hari</label>
                        <div class="relative">
                            <input id="bungaHarian" type="number" min="0" max="100" step="0.1" class="fs-input pr-20" x-model.number="bungaHarian">
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[13px] text-slate-400">% / hari</span>
                        </div>
                    </div>

                    <div>
                        <label class="fs-label" for="tenorHari">Tenor</label>
                        <div class="relative">
                            <input id="tenorHari" type="number" min="1" max="365" step="1" class="fs-input pr-14" x-model.number="tenorHari">
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[13px] text-slate-400">hari</span>
                        </div>
                        <p class="fs-hint">Maksimum 365 hari.</p>
                    </div>

                    <div>
                        <label class="fs-label" for="biayaTetap">Biaya tetap</label>
                        <input id="biayaTetap" type="text" inputmode="numeric" autocomplete="off" class="fs-input"
                               x-bind:value="formatRupiah(biayaTetap)"
                               x-on:input="biayaTetap = parseRupiah($event.target.value); $event.target.value = formatRupiah(biayaTetap)">
                        <p class="fs-hint">Ditambahkan ke tagihan, bukan dipotong di muka.</p>
                    </div>

                    <div>
                        <label class="fs-label" for="jenisBunga">Jenis bunga</label>
                        <select id="jenisBunga" class="fs-input cursor-pointer border-brand/20 bg-brand-mint font-semibold text-brand" x-model="jenisBunga">
                            <option value="flat">Sederhana / flat</option>
                            <option value="majemuk">Majemuk / berbunga-bunga</option>
                        </select>
                        <p class="fs-hint" x-text="jenisBunga === 'flat' ? 'Bunga hanya dihitung dari pokok awal. Tidak berbunga majemuk.' : 'Bunga dihitung dari pokok ditambah bunga hari-hari sebelumnya, sehingga bunga ikut berbunga.'">Bunga hanya dihitung dari pokok awal. Tidak berbunga majemuk.</p>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="button" class="fs-btn" x-on:click="hitung()">
                        Hitung simulasi
                        <x-icon name="calculator" class="h-4 w-4" />
                    </button>
                    <p class="mt-3 text-[11px] text-slate-400">Hasil otomatis diperbarui saat angka diubah • hasil contoh di sebelah kanan</p>
                </div>
            </section>

            <aside class="rounded-2xl bg-brand-mint p-5 text-brand">
                <div class="flex items-center gap-2.5">
                    <x-icon name="shield" class="h-[18px] w-[18px]" />
                    <h3 class="text-[15px] font-medium">Baca sebelum menyetujui</h3>
                </div>
                <p class="mt-3 text-[13px] leading-relaxed">Periksa total dana diterima, total pembayaran, jadwal, biaya, dan legalitas penyelenggara melalui kanal resmi OJK.</p>
            </aside>
        </div>

        {{-- Kolom kanan: hasil --}}
        <div class="space-y-5">
            {{-- Banner hasil --}}
            <section x-ref="hasil" class="grid gap-5 rounded-2xl bg-brand-dark p-7 text-white transition-shadow duration-500 sm:grid-cols-[1fr_auto] sm:items-center"
                     x-bind:class="sorot ? 'ring-4 ring-brand/40' : ''">
                <div class="min-w-0">
                    <p class="text-[13px] text-white/70" x-text="'Total tagihan pada hari ke-' + tenor">Total tagihan pada hari ke-30</p>
                    <p class="mt-2 break-words text-[40px] font-medium leading-none tracking-tight sm:text-[44px]" x-text="formatRupiah(totalTagihan)">Rp1.400.000</p>
                    <p class="mt-3 text-xs text-white/70" x-text="'Dana diterima utuh: ' + formatRupiah(pokok)">Dana diterima utuh: Rp1.000.000</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs text-white/70">Di luar pokok pinjaman</p>
                    <p class="mt-2 text-[30px] font-medium leading-none text-[#AEE3D3]" x-text="'+' + formatRupiah(totalBeban)">+Rp400.000</p>
                    <p class="mt-3 text-xs text-white/70" x-text="formatAngka(persenBeban, 1) + '% dari pokok dalam ' + tenor + ' hari'">40% dari pokok dalam 30 hari</p>
                </div>
            </section>

            {{-- Perhitungan terbuka --}}
            <section class="fs-card">
                <h2 class="fs-h2">Perhitungan terbuka</h2>

                <div class="mt-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[13px] text-slate-500" x-text="jenisBunga === 'flat' ? 'Bunga = pokok × bunga harian × tenor' : 'Bunga = pokok × ((1 + bunga harian)^tenor − 1)'">Bunga = pokok × bunga harian × tenor</p>
                        <p class="mt-3 break-words text-[13px] text-iris" x-text="rumusAngka">Rp1.000.000 × 1% × 30 = Rp300.000</p>
                    </div>
                    <p class="shrink-0 text-base text-ink" x-text="formatRupiah(bungaAkhir)">Rp300.000</p>
                </div>

                <div class="mt-4 flex items-start justify-between gap-4 border-t border-slate-200 pt-4">
                    <p class="text-[13px] text-slate-500">Biaya tetap satu kali</p>
                    <p class="shrink-0 text-base text-ink" x-text="formatRupiah(biaya)">Rp100.000</p>
                </div>

                <p class="mt-4 break-words rounded-lg bg-slate-50 px-4 py-3 text-[13px] font-semibold text-ink" x-text="formatRupiah(pokok) + ' + ' + formatRupiah(bungaAkhir) + ' + ' + formatRupiah(biaya) + ' = ' + formatRupiah(totalTagihan)">Rp1.000.000 + Rp300.000 + Rp100.000 = Rp1.400.000</p>

                <p class="mt-4 text-xs text-slate-500">Diasumsikan lunas sekaligus pada akhir tenor; tidak ada cicilan, denda keterlambatan, atau biaya tambahan lain.</p>
            </section>

            {{-- Grafik + tabel --}}
            <section class="fs-card">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="fs-h2">Bagaimana tagihan bertambah?</h2>
                    <span class="text-[13px] text-slate-500">Tanpa pembayaran selama tenor</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                    <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-brand"></span>Pokok</span>
                    <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-iris"></span>Biaya tetap</span>
                    <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-iris-mid"></span>Bunga</span>
                </div>

                <div class="relative mt-3 h-[280px]">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik batang bertumpuk pokok, biaya tetap, dan bunga pada lima titik hari"></canvas>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[420px] text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-xs font-semibold text-slate-600">
                                <th class="rounded-l-md px-3 py-2.5">Hari ke-</th>
                                <th class="px-3 py-2.5">Bunga terkumpul</th>
                                <th class="rounded-r-md px-3 py-2.5 text-right">Total tagihan*</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="b in baris" x-bind:key="b.hari">
                                <tr class="border-b border-slate-100 last:border-0" x-bind:class="b.hari === tenor ? 'bg-iris-soft' : ''">
                                    <td class="px-3 py-3" x-bind:class="b.hari === tenor ? 'font-semibold text-ink' : 'text-slate-600'" x-text="b.hari"></td>
                                    <td class="px-3 py-3 text-slate-600" x-text="formatRupiah(b.bunga)"></td>
                                    <td class="px-3 py-3 text-right font-semibold" x-bind:class="b.hari === tenor ? 'text-iris' : 'text-ink'" x-text="formatRupiah(b.total)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-xs text-slate-500" x-text="'*Total = pokok + bunga berjalan + biaya tetap ' + formatRupiah(biaya) + '. Biaya sudah masuk sejak hari ke-0.'">*Total = pokok + bunga berjalan + biaya tetap Rp100.000. Biaya sudah masuk sejak hari ke-0.</p>
            </section>
        </div>
    </div>

    {{-- Kalau sudah terlanjur --}}
    <section class="fs-card">
        <div class="grid gap-6 md:grid-cols-[1.1fr_1fr_1fr_1fr] md:gap-8">
            <div>
                <h2 class="text-[19px] font-medium text-ink">Kalau sudah terlanjur meminjam</h2>
                <p class="mt-3 text-[13px] leading-relaxed text-slate-500">Ambil langkah yang bisa kamu kendalikan, satu per satu.</p>
            </div>
            <div>
                <x-icon name="file" class="h-5 w-5 text-brand" />
                <h3 class="mt-3 text-[15px] font-medium text-ink">Rapikan bukti</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">Simpan kontrak, bukti transfer, dan riwayat penagihan.</p>
            </div>
            <div>
                <x-icon name="list-check" class="h-5 w-5 text-brand" />
                <h3 class="mt-3 text-[15px] font-medium text-ink">Petakan kewajiban</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">Catat jatuh tempo; utamakan makan, tempat tinggal, dan kebutuhan dasar.</p>
            </div>
            <div>
                <x-icon name="messages" class="h-5 w-5 text-brand" />
                <h3 class="mt-3 text-[15px] font-medium text-ink">Cari pendampingan</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">Hubungi orang tepercaya dan kanal resmi OJK 157 untuk informasi pengaduan.</p>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function kalkulatorPinjol(awal) {
        let chart = null;

        // Plugin kecil: tulis total tagihan di atas tiap batang.
        const labelTotal = {
            id: 'labelTotal',
            afterDatasetsDraw(c) {
                const ctx = c.ctx;
                const jumlah = c.data.labels.length;
                const metas = c.data.datasets.map(function (d, i) { return c.getDatasetMeta(i); });

                ctx.save();
                ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                for (let i = 0; i < jumlah; i++) {
                    let atas = Infinity;
                    let x = null;
                    let total = 0;

                    metas.forEach(function (m, di) {
                        if (m.hidden) return;
                        const el = m.data[i];
                        if (el) {
                            atas = Math.min(atas, el.y);
                            x = el.x;
                            total += Number(c.data.datasets[di].data[i]) || 0;
                        }
                    });

                    if (x !== null && isFinite(atas)) {
                        ctx.fillStyle = i === jumlah - 1 ? '#5B5BB5' : '#334155';
                        ctx.fillText(formatRupiah(total), x, atas - 6);
                    }
                }

                ctx.restore();
            },
        };

        return {
            pokokPinjaman: awal.pokokPinjaman,
            bungaHarian: awal.bungaHarian,
            tenorHari: awal.tenorHari,
            biayaTetap: awal.biayaTetap,
            jenisBunga: awal.jenisBunga,
            sorot: false,

            // ----- Nilai input yang sudah disanitasi -----
            get pokok() { return clamp(Math.round(Number(this.pokokPinjaman) || 0), 0, 9e12); },
            get bunga() { return clamp(Number(this.bungaHarian) || 0, 0, 100); },
            get tenor() { return clamp(Math.round(Number(this.tenorHari) || 0), 1, 365); },
            get biaya() { return clamp(Math.round(Number(this.biayaTetap) || 0), 0, 9e12); },

            // ----- Rumus -----
            bungaPada(t) {
                const b = this.bunga / 100;
                if (this.jenisBunga === 'majemuk') {
                    return this.pokok * (Math.pow(1 + b, t) - 1);
                }
                return this.pokok * b * t;
            },

            totalPada(t) {
                return this.pokok + this.biaya + this.bungaPada(t);
            },

            get bungaAkhir() { return this.bungaPada(this.tenor); },
            get totalTagihan() { return this.totalPada(this.tenor); },
            get totalBeban() { return this.biaya + this.bungaAkhir; },
            get persenBeban() { return this.pokok > 0 ? (this.totalBeban / this.pokok) * 100 : 0; },

            /** 5 titik hari proporsional terhadap tenor (tenor 30 => 0, 7, 14, 21, 30). */
            get titik() {
                const t = this.tenor;
                const kandidat = [0, Math.round(t * 0.23), Math.round(t * 0.46), Math.round(t * 0.7), t];
                return kandidat
                    .filter(function (v, i) { return kandidat.indexOf(v) === i; })
                    .sort(function (a, b) { return a - b; });
            },

            get baris() {
                return this.titik.map((h) => ({
                    hari: h,
                    bunga: this.bungaPada(h),
                    total: this.totalPada(h),
                }));
            },

            get rumusAngka() {
                const p = formatRupiah(this.pokok);
                const b = formatAngka(this.bunga) + '%';
                const hasil = formatRupiah(this.bungaAkhir);
                if (this.jenisBunga === 'majemuk') {
                    return p + ' × ((1 + ' + b + ')^' + this.tenor + ' − 1) = ' + hasil;
                }
                return p + ' × ' + b + ' × ' + this.tenor + ' = ' + hasil;
            },

            // ----- Siklus hidup -----
            init() {
                this.buatChart();
                this.perbaruiChart();
                this.simpan();

                ['pokokPinjaman', 'bungaHarian', 'tenorHari', 'biayaTetap', 'jenisBunga'].forEach((k) => {
                    this.$watch(k, () => {
                        this.perbaruiChart();
                        this.simpan();
                    });
                });
            },

            hitung() {
                this.perbaruiChart();
                this.simpan();
                this.sorot = true;
                setTimeout(() => { this.sorot = false; }, 900);
                if (window.innerWidth < 1024 && this.$refs.hasil) {
                    this.$refs.hasil.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },

            simpan() {
                simpanLokal('finshield.pinjol', {
                    total: this.totalTagihan,
                    pokok: this.pokok,
                    tenor: this.tenor,
                    jenis: this.jenisBunga,
                    waktu: Date.now(),
                });
            },

            // ----- Chart.js -----
            buatChart() {
                const ctx = this.$refs.canvas.getContext('2d');

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [
                            { label: 'Pokok', data: [], backgroundColor: '#0D5C46', barPercentage: 0.55, categoryPercentage: 0.9 },
                            { label: 'Biaya tetap', data: [], backgroundColor: '#5B5BB5', barPercentage: 0.55, categoryPercentage: 0.9 },
                            { label: 'Bunga', data: [], backgroundColor: '#AEB0E3', barPercentage: 0.55, categoryPercentage: 0.9 },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 350 },
                        layout: { padding: { top: 26 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (item) { return ' ' + item.dataset.label + ': ' + formatRupiah(item.parsed.y); },
                                    footer: function (items) {
                                        const total = items.reduce(function (s, i) { return s + i.parsed.y; }, 0);
                                        return 'Total: ' + formatRupiah(total);
                                    },
                                },
                            },
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false }, border: { display: false } },
                            y: { stacked: true, beginAtZero: true, display: false, grace: '4%' },
                        },
                    },
                    plugins: [labelTotal],
                });
            },

            perbaruiChart() {
                if (!chart) return;
                const b = this.baris;
                const pokok = this.pokok;
                const biaya = this.biaya;

                chart.data.labels = b.map(function (x) { return 'Hari ' + x.hari; });
                chart.data.datasets[0].data = b.map(function () { return pokok; });
                chart.data.datasets[1].data = b.map(function () { return biaya; });
                chart.data.datasets[2].data = b.map(function (x) { return x.bunga; });
                chart.update();
            },
        };
    }
</script>
@endpush
