@extends('layouts.app')

@section('title', 'Edukasi judol')
@section('page', 'Edukasi judol')

@section('content')
<div x-data="judolSim(@js($defaults))" x-cloak class="space-y-6">

    <header>
        <p class="fs-eyebrow">Pahami risiko • Simulasi edukasi</p>
        <h1 class="fs-h1 mt-2">Di balik peluang, ada matematika.</h1>
        <p class="mt-3 text-[15px] text-slate-500">Pelajari house edge dan dampak pengulangan. Bukan permainan, tanpa uang asli atau hadiah.</p>
    </header>

    <div class="flex gap-3 rounded-xl bg-warn-bg px-5 py-4 text-warn-text">
        <x-icon name="info" class="mt-0.5 h-[18px] w-[18px] shrink-0" />
        <div>
            <p class="text-sm font-medium">Menang sesaat tidak menghapus risiko jangka panjang</p>
            <p class="mt-1 text-[13px] leading-relaxed">Nilai harapan negatif berarti rata-rata hasil model merugikan peserta. Hasil tiap individu tetap bervariasi; tidak berarti setiap orang pasti kalah.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">

        {{-- Kolom kiri --}}
        <div class="space-y-5">
            <section class="fs-card">
                <h2 class="fs-h2">Pengaturan model</h2>
                <p class="mt-3 text-[13px] leading-relaxed text-slate-500">Contoh sederhana, bukan algoritma platform tertentu.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="fs-label" for="saldoAwal">Saldo awal</label>
                        <input id="saldoAwal" type="text" inputmode="numeric" autocomplete="off" class="fs-input"
                               x-bind:value="formatRupiah(saldoAwal)"
                               x-on:input="saldoAwal = parseRupiah($event.target.value); $event.target.value = formatRupiah(saldoAwal)">
                    </div>

                    <div>
                        <label class="fs-label" for="nominalPutaran">Nominal per putaran</label>
                        <input id="nominalPutaran" type="text" inputmode="numeric" autocomplete="off" class="fs-input"
                               x-bind:value="formatRupiah(nominalPutaran)"
                               x-on:input="nominalPutaran = parseRupiah($event.target.value); $event.target.value = formatRupiah(nominalPutaran)">
                    </div>

                    <div>
                        <label class="fs-label" for="jumlahPutaran">Jumlah putaran</label>
                        <div class="relative">
                            <input id="jumlahPutaran" type="number" min="1" max="5000" step="1" class="fs-input pr-20" x-model.number="jumlahPutaran">
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[13px] text-slate-400">putaran</span>
                        </div>
                        <p class="fs-hint">Maksimum 5.000 putaran. Coba 100, 1.000, atau 2.000.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4 border-t border-slate-200 pt-5">
                    <p class="text-[13px] font-medium text-ink">Asumsi peluang</p>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="text-[13px] text-slate-500" for="peluangMenang">Peluang hasil menang</label>
                            <div class="flex items-center gap-1">
                                <input type="number" min="0" max="100" step="1" aria-label="Peluang menang dalam persen"
                                       class="w-16 rounded-md border border-slate-200 px-2 py-1 text-right text-sm font-semibold text-ink outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                                       x-model.number="peluangMenang">
                                <span class="text-sm text-slate-500">%</span>
                            </div>
                        </div>
                        <input id="peluangMenang" type="range" min="0" max="100" step="1" class="mt-2 w-full" x-model.number="peluangMenang">
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="text-[13px] text-slate-500" for="multiplierMenang">Pengembalian jika menang</label>
                            <div class="flex items-center gap-1">
                                <input id="multiplierMenang" type="number" min="1" max="1000" step="0.1"
                                       class="w-16 rounded-md border border-slate-200 px-2 py-1 text-right text-sm font-semibold text-ink outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                                       x-model.number="multiplierMenang">
                                <span class="text-sm text-slate-500">×</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs leading-relaxed text-slate-500" x-text="formatAngka(mult) + '× sudah termasuk nominal awal. Jika kalah, nominal putaran hilang. Tiap putaran diasumsikan independen.'">2× sudah termasuk nominal awal. Jika kalah, nominal putaran hilang. Tiap putaran diasumsikan independen.</p>
                </div>

                <div class="mt-5">
                    <button type="button" class="fs-btn" x-on:click="acakUlang()">
                        <span x-text="sudahAcak ? 'Acak ulang simulasi' : 'Tampilkan simulasi'">Tampilkan simulasi</span>
                        <x-icon name="chart" class="h-4 w-4" />
                    </button>
                    <p class="mt-3 text-[11px] text-slate-400">Hasil otomatis diperbarui saat pengaturan diubah • klik untuk mengacak ulang</p>
                    <button type="button" class="mt-2 text-[11px] text-slate-500 underline-offset-2 hover:text-ink hover:underline" x-on:click="kembalikan()">Kembalikan ke contoh</button>
                </div>
            </section>

            <aside class="rounded-2xl bg-brand-mint p-5 text-brand">
                <div class="flex items-center gap-2.5">
                    <x-icon name="shield" class="h-[18px] w-[18px]" />
                    <h3 class="text-[15px] font-medium">Tidak ada pola pemulihan</h3>
                </div>
                <p class="mt-3 text-[13px] leading-relaxed">Hasil sebelumnya tidak mengubah peluang berikutnya pada model independen. Menaikkan nominal justru memperbesar eksposur kerugian.</p>
            </aside>
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-5">
            <section class="fs-card">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="fs-h2">Hasil satu lintasan contoh</h2>
                    <span class="fs-chip bg-brand-mint text-brand" x-text="formatAngka(hasil.n, 0) + ' putaran'">500 putaran</span>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-3">
                    <div>
                        <p class="text-[13px] text-slate-500">Saldo akhir contoh</p>
                        <p class="mt-2 text-[30px] font-medium leading-none text-ink" x-text="formatRupiah(hasil.saldoAkhir)">Rp460.000</p>
                        <p class="mt-3 text-xs text-slate-500" x-text="'Dari saldo awal ' + formatRupiah(hasil.saldoAwal)">Dari saldo awal Rp1.000.000</p>
                    </div>
                    <div>
                        <p class="text-[13px] text-slate-500">Perubahan saldo</p>
                        <p class="mt-2 text-[30px] font-medium leading-none"
                           x-bind:class="perubahan < 0 ? 'text-[#B9444A]' : (perubahan > 0 ? 'text-brand' : 'text-ink')"
                           x-text="(perubahan > 0 ? '+' : '') + formatRupiah(perubahan)">-Rp540.000</p>
                        <p class="mt-3 text-xs text-slate-500" x-text="formatAngka(hasil.menang, 0) + ' menang • ' + formatAngka(hasil.kalah, 0) + ' kalah'">223 menang • 277 kalah</p>
                    </div>
                    <div>
                        <p class="text-[13px] text-slate-500">House edge model</p>
                        <p class="mt-2 text-[30px] font-medium leading-none text-iris" x-text="formatAngka(houseEdge, 2) + '%'">10%</p>
                        <p class="mt-3 text-xs text-slate-500">Bukan peluang kalah per putaran</p>
                    </div>
                </div>

                <p class="mt-4 flex items-start gap-2 rounded-lg bg-[#FBEDED] px-3 py-2 text-xs leading-relaxed text-[#9B3A40]" x-show="hasil.bangkrutDi !== null" x-transition>
                    <x-icon name="alert" class="mt-px h-4 w-4 shrink-0" />
                    <span x-text="'Saldo tidak cukup untuk memasang nominal berikutnya setelah putaran ke-' + formatAngka(hasil.bangkrutDi, 0) + '. Sisa putaran tidak dimainkan.'"></span>
                </p>
            </section>

            <section class="fs-card">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="fs-h2" x-text="'Saldo sepanjang ' + formatAngka(hasil.n, 0) + ' putaran'">Saldo sepanjang 500 putaran</h2>
                    <span class="text-[13px] text-slate-500" x-text="'Nominal tetap ' + formatRupiah(hasil.nominal)">Nominal tetap Rp10.000</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                    <span class="flex items-center gap-2"><span class="h-0.5 w-4 rounded bg-brand"></span>Satu lintasan contoh</span>
                    <span class="flex items-center gap-2"><span class="h-0 w-4 border-t-2 border-dashed border-[#14B8A6]"></span>Saldo harapan matematis</span>
                </div>

                <div class="relative mt-3 h-[260px]">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik saldo sepanjang putaran dibandingkan saldo harapan matematis"></canvas>
                </div>

                <p class="mt-4 text-xs text-slate-500" x-text="'Saldo contoh dicatat tiap ' + formatAngka(hasil.interval, 0) + ' putaran. Garis putus-putus adalah rata-rata teoretis, bukan prediksi hasil individu.'">Saldo contoh dicatat tiap 10 putaran. Garis putus-putus adalah rata-rata teoretis, bukan prediksi hasil individu.</p>

                <div class="mt-3 flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-4 py-3 text-[13px]">
                    <span class="text-slate-600" x-text="'Saldo harapan pada putaran ke-' + formatAngka(putaran, 0)">Saldo harapan pada putaran ke-500</span>
                    <span class="font-medium text-iris" x-text="formatRupiah(saldoHarapan)">Rp500.000</span>
                </div>
            </section>

            <section class="rounded-2xl bg-iris-soft p-6">
                <div class="flex items-center gap-2.5 text-iris">
                    <x-icon name="sigma" class="h-5 w-5" />
                    <h2 class="text-[18px] font-medium" x-text="'Nilai harapan: ' + formatRupiah(ev) + ' per putaran'">Nilai harapan: -Rp1.000 per putaran</h2>
                </div>
                <p class="mt-4 text-sm font-semibold text-ink" x-text="rumusEv">EV = (45% × Rp10.000) + (55% × -Rp10.000)</p>
                <p class="mt-3 text-[13px] leading-relaxed text-slate-600" x-text="rincianEv">Menang memberi laba bersih Rp10.000; kalah memberi rugi Rp10.000.</p>
                <p class="mt-3 rounded-lg bg-white/70 px-3 py-2 text-xs leading-relaxed text-slate-600" x-show="ev >= 0" x-transition>
                    Dengan pengaturan ini nilai harapan tidak negatif, artinya model justru menguntungkan peserta. Hampir tidak ada platform nyata yang dirancang seperti ini; coba turunkan peluang menang atau pengembalian untuk melihat kondisi yang lebih realistis.
                </p>
            </section>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="fs-card">
            <div class="flex items-center gap-3">
                <x-icon name="scale" class="h-5 w-5 text-brand" />
                <h2 class="text-[18px] font-medium text-ink">House edge bukan trik yang bisa dikalahkan</h2>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-slate-500" x-show="houseEdge > 0"
               x-text="'Rata-rata pengembalian model adalah ' + formatAngka(100 - houseEdge, 2) + '% dari total nominal yang dipasang. Selisih ' + formatAngka(houseEdge, 2) + '% adalah keuntungan matematis penyelenggara, bukan jaminan hasil pada satu sesi.'">Rata-rata pengembalian model adalah 90% dari total nominal yang dipasang. Selisih 10% adalah keuntungan matematis penyelenggara, bukan jaminan hasil pada satu sesi.</p>
            <p class="mt-4 text-sm leading-relaxed text-slate-500" x-show="houseEdge <= 0" x-cloak
               x-text="'Pada pengaturan ini house edge-nya ' + formatAngka(houseEdge, 2) + '%, sehingga rata-rata pengembalian model mencapai ' + formatAngka(100 - houseEdge, 2) + '%. Kondisi ini jarang ada pada platform nyata, yang hampir selalu memberi selisih keuntungan bagi penyelenggara.'"></p>
        </section>

        <section class="fs-card">
            <div class="flex items-center gap-3">
                <x-icon name="sliders" class="h-5 w-5 text-brand" />
                <h2 class="text-[18px] font-medium text-ink">Alihkan fokus ke hal yang bisa dikendalikan</h2>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">Beri jeda saat terdorong mengejar kerugian. Prioritaskan kebutuhan dan cadangan, lalu ajak orang tepercaya berdiskusi bila sulit berhenti.</p>
            <a href="{{ route('kelola-uang') }}" class="mt-4 inline-flex items-center gap-1.5 text-[13px] font-medium text-brand hover:underline">
                Susun alokasi uang di Kelola uang <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function judolSim(awal) {
        // Instance Chart.js disimpan di closure (bukan state Alpine) agar tidak dibungkus Proxy reaktif.
        let chart = null;
        let timer = null;

        return {
            saldoAwal: awal.saldoAwal,
            nominalPutaran: awal.nominalPutaran,
            jumlahPutaran: awal.jumlahPutaran,
            peluangMenang: awal.peluangMenang,
            multiplierMenang: awal.multiplierMenang,

            sudahAcak: false,
            hasil: {
                saldoAwal: awal.saldoAwal,
                nominal: awal.nominalPutaran,
                n: awal.jumlahPutaran,
                saldoAkhir: awal.saldoAwal,
                menang: 0,
                kalah: 0,
                bangkrutDi: null,
                interval: 1,
            },

            // ----- Nilai input yang sudah disanitasi -----
            get saldo0() { return clamp(Math.round(Number(this.saldoAwal) || 0), 0, 9e12); },
            get nominal() { return clamp(Math.round(Number(this.nominalPutaran) || 0), 0, 9e12); },
            get putaran() { return clamp(Math.round(Number(this.jumlahPutaran) || 0), 1, 5000); },
            get pMenang() { return clamp(Number(this.peluangMenang) || 0, 0, 100); },
            get pKalah() { return 100 - this.pMenang; },
            get mult() { return clamp(Number(this.multiplierMenang) || 1, 1, 1000); },

            // ----- Rumus -----
            get laba() { return this.nominal * (this.mult - 1); },
            get rugi() { return this.nominal; },
            get ev() { return (this.pMenang / 100) * this.laba - (this.pKalah / 100) * this.rugi; },
            get houseEdge() { return this.nominal > 0 ? (-this.ev / this.nominal) * 100 : 0; },
            get saldoHarapan() { return this.saldo0 + this.putaran * this.ev; },
            get perubahan() { return this.hasil.saldoAkhir - this.hasil.saldoAwal; },

            get rumusEv() {
                return 'EV = (' + formatAngka(this.pMenang) + '% × ' + formatRupiah(this.laba) +
                    ') + (' + formatAngka(this.pKalah) + '% × -' + formatRupiah(this.rugi) + ')';
            },

            get rincianEv() {
                const ev = this.ev;
                return 'Menang memberi laba bersih ' + formatRupiah(this.laba) +
                    '; kalah memberi rugi ' + formatRupiah(this.rugi) +
                    '. Maka EV = ' + formatRupiah((this.pMenang / 100) * this.laba) +
                    ' − ' + formatRupiah((this.pKalah / 100) * this.rugi) +
                    ' = ' + formatRupiah(ev) +
                    '. Saldo harapan: ' + formatRupiah(this.saldo0) +
                    (ev < 0 ? ' − ' : ' + ') +
                    '(' + formatAngka(this.putaran, 0) + ' × ' + formatRupiah(Math.abs(ev)) + ') = ' +
                    formatRupiah(this.saldoHarapan) + '.';
            },

            // ----- Siklus hidup -----
            init() {
                this.buatChart();
                this.jalankanSimulasi();

                const kunci = ['saldoAwal', 'nominalPutaran', 'jumlahPutaran', 'peluangMenang', 'multiplierMenang'];
                kunci.forEach((k) => {
                    this.$watch(k, () => this.jadwalkan());
                });
            },

            jadwalkan() {
                clearTimeout(timer);
                timer = setTimeout(() => this.jalankanSimulasi(), 120);
            },

            acakUlang() {
                this.sudahAcak = true;
                this.jalankanSimulasi();
            },

            kembalikan() {
                this.saldoAwal = awal.saldoAwal;
                this.nominalPutaran = awal.nominalPutaran;
                this.jumlahPutaran = awal.jumlahPutaran;
                this.peluangMenang = awal.peluangMenang;
                this.multiplierMenang = awal.multiplierMenang;
                this.sudahAcak = false;
            },

            // ----- Simulasi Monte Carlo (1 lintasan acak) -----
            jalankanSimulasi() {
                const n = this.putaran;
                const nominal = this.nominal;
                const laba = this.laba;
                const rugi = this.rugi;
                const peluang = this.pMenang;
                const ev = this.ev;
                const awalSaldo = this.saldo0;
                const interval = Math.max(1, Math.floor(n / 50));

                let saldo = awalSaldo;
                let menang = 0;
                let kalah = 0;
                let bangkrutDi = null;

                const aktual = [{ x: 0, y: awalSaldo }];
                const harapan = [{ x: 0, y: awalSaldo }];

                for (let r = 1; r <= n; r++) {
                    if (nominal > 0) {
                        if (saldo < nominal) {
                            // Saldo habis: tidak bisa memasang lagi, saldo berhenti.
                            if (bangkrutDi === null) bangkrutDi = r - 1;
                        } else if (Math.random() * 100 < peluang) {
                            saldo += laba;
                            menang++;
                        } else {
                            saldo -= rugi;
                            kalah++;
                        }
                    }

                    if (r % interval === 0 || r === n) {
                        aktual.push({ x: r, y: saldo });
                        harapan.push({ x: r, y: awalSaldo + r * ev });
                    }
                }

                this.hasil = {
                    saldoAwal: awalSaldo,
                    nominal: nominal,
                    n: n,
                    saldoAkhir: saldo,
                    menang: menang,
                    kalah: kalah,
                    bangkrutDi: bangkrutDi,
                    interval: interval,
                };

                this.perbaruiChart(aktual, harapan, n);

                simpanLokal('finshield.judol', { ev: ev, houseEdge: this.houseEdge, waktu: Date.now() });
            },

            // ----- Chart.js -----
            buatChart() {
                const ctx = this.$refs.canvas.getContext('2d');

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [
                            {
                                label: 'Satu lintasan contoh',
                                data: [],
                                borderColor: '#0D5C46',
                                backgroundColor: '#0D5C46',
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                tension: 0.12,
                                order: 1,
                            },
                            {
                                label: 'Saldo harapan matematis',
                                data: [],
                                borderColor: '#14B8A6',
                                backgroundColor: '#14B8A6',
                                borderWidth: 2,
                                borderDash: [6, 5],
                                pointRadius: 0,
                                pointHoverRadius: 3,
                                order: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 350 },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: function (items) { return 'Putaran ke-' + formatAngka(items[0].parsed.x, 0); },
                                    label: function (item) { return ' ' + item.dataset.label + ': ' + formatRupiah(item.parsed.y); },
                                },
                            },
                        },
                        scales: {
                            x: {
                                type: 'linear',
                                min: 0,
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    maxTicksLimit: 6,
                                    callback: function (v, i, ticks) {
                                        return i === ticks.length - 1 ? formatAngka(v, 0) + ' putaran' : formatAngka(v, 0);
                                    },
                                },
                            },
                            y: {
                                suggestedMin: 0,
                                grid: { color: '#EEF2F6' },
                                border: { display: false },
                                ticks: {
                                    maxTicksLimit: 6,
                                    callback: function (v) { return formatRingkas(v); },
                                },
                            },
                        },
                    },
                });
            },

            perbaruiChart(aktual, harapan, n) {
                if (!chart) return;
                chart.data.datasets[0].data = aktual;
                chart.data.datasets[1].data = harapan;
                chart.options.scales.x.max = n;
                chart.update();
            },
        };
    }
</script>
@endpush
