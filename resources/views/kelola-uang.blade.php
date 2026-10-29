@extends('layouts.app')

@section('title', 'Kelola uang')
@section('page', 'Kelola uang')

@section('content')
<div x-data="kelolaUang(@js($defaults))" x-cloak class="space-y-6">

    <header>
        <p class="fs-eyebrow">Bangun kebiasaan • Panduan praktis</p>
        <h1 class="fs-h1 mt-2">Beri setiap rupiah tujuan.</h1>
        <p class="mt-3 text-[15px] text-slate-500">Atur gaji atau uang saku sesuai kebutuhanmu, lalu tambah ruang untuk rencana masa depan.</p>
    </header>

    {{-- Input pemasukan --}}
    <section class="fs-card flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-[18px] font-medium text-ink">Mulai dari uang yang tersedia</h2>
            <p class="mt-1 text-[13px] text-slate-500">Gunakan pemasukan bersih yang sudah pasti, bukan yang masih dijanjikan.</p>
        </div>
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-[250px]">
                <label class="fs-label" for="pemasukan">Gaji / uang saku per bulan</label>
                <input id="pemasukan" type="text" inputmode="numeric" autocomplete="off" class="fs-input"
                       x-bind:value="formatRupiah(pemasukanBulanan)"
                       x-on:input="pemasukanBulanan = parseRupiah($event.target.value); $event.target.value = formatRupiah(pemasukanBulanan)">
            </div>
            <div>
                <p class="mb-1.5 text-[11px] text-slate-400">Contoh alokasi bulanan</p>
                <button type="button" class="fs-btn" x-on:click="lihatPembagian()">
                    Lihat pembagian
                    <x-icon name="pie" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">

        {{-- Pembagian --}}
        <section x-ref="pembagian" class="fs-card scroll-mt-24 transition-shadow duration-500" x-bind:class="sorot ? 'ring-4 ring-brand/25' : ''">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="fs-h2" x-text="'Pembagian ' + p.kebutuhan + ' / ' + p.keinginan + ' / ' + p.masaDepan">Pembagian 50 / 30 / 20</h2>
                <div class="flex items-center gap-3">
                    <span class="text-[13px] text-slate-500">Titik awal yang fleksibel</span>
                    <button type="button" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 transition hover:border-brand hover:text-brand" x-on:click="resetRasio()">Reset ke 50/30/20</button>
                </div>
            </div>

            <div class="mt-5 flex flex-col items-center gap-6 sm:flex-row">
                <div class="relative h-[170px] w-[170px] shrink-0">
                    <canvas x-ref="donut" role="img" aria-label="Diagram donat pembagian pemasukan"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-[11px] text-slate-500">Total dialokasikan</span>
                        <span class="mt-0.5 text-[30px] font-semibold leading-none"
                              x-bind:class="totalPersen > 100 ? 'text-[#B9444A]' : 'text-ink'"
                              x-text="totalPersen + '%'">100%</span>
                    </div>
                </div>

                <div class="min-w-0 text-center sm:text-left">
                    <p class="break-words text-[34px] font-medium leading-none text-ink" x-text="formatRupiah(gaji)">Rp3.000.000</p>
                    <p class="mt-3 text-[13px] leading-relaxed text-slate-500"
                       x-text="sisa === 0 ? 'Terbagi penuh tanpa melebihi pemasukan. Pisahkan pos saat uang masuk agar kebutuhan utama lebih terlindungi.' : (sisa > 0 ? 'Masih ada uang yang belum punya tujuan. Tambahkan ke pos yang paling kamu butuhkan.' : 'Alokasimu melebihi pemasukan. Kurangi salah satu pos sampai total kembali 100%.')">Terbagi penuh tanpa melebihi pemasukan. Pisahkan pos saat uang masuk agar kebutuhan utama lebih terlindungi.</p>
                    <span class="fs-chip mt-3"
                          x-bind:class="sisa === 0 ? 'bg-brand-mint text-brand' : (sisa > 0 ? 'bg-warn-bg text-warn-text' : 'bg-[#FBEDED] text-[#9B3A40]')"
                          x-text="sisa < 0 ? 'Melebihi pemasukan: ' + formatRupiah(Math.abs(sisa)) : 'Sisa belum dialokasikan: ' + formatRupiah(sisa)">Sisa belum dialokasikan: Rp0</span>
                </div>
            </div>

            {{-- Tiga pos + slider --}}
            @php
                $pos = [
                    ['key' => 'kebutuhan', 'nama' => 'Kebutuhan', 'warna' => '#0D5C46'],
                    ['key' => 'keinginan', 'nama' => 'Keinginan', 'warna' => '#5B5BB5'],
                    ['key' => 'masaDepan', 'nama' => 'Masa depan', 'warna' => '#8ECFC0'],
                ];
            @endphp

            <div class="mt-6">
                @foreach ($pos as $item)
                    <div class="border-t border-slate-200 py-4">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-sm" style="background-color: {{ $item['warna'] }}"></span>
                            <label class="w-24 shrink-0 text-[15px] text-ink" for="persen-{{ $item['key'] }}">{{ $item['nama'] }}</label>

                            <input type="range" min="0" max="100" step="1" class="order-last min-w-[120px] flex-1 sm:order-none"
                                   aria-label="Persentase {{ $item['nama'] }}" x-model.number="persen.{{ $item['key'] }}">

                            <div class="ml-auto flex items-center gap-1 sm:ml-0">
                                <input id="persen-{{ $item['key'] }}" type="number" min="0" max="100" step="1"
                                       class="w-14 rounded-md border border-slate-200 px-2 py-1 text-right text-sm text-slate-700 outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                                       x-model.number="persen.{{ $item['key'] }}">
                                <span class="text-sm text-slate-500">%</span>
                            </div>

                            <span class="w-[110px] text-right text-[17px] font-medium text-ink" x-text="formatRupiah(nominal.{{ $item['key'] }})"></span>
                        </div>
                        <p class="mt-2 pl-[26px] text-xs text-slate-500" x-text="detail.{{ $item['key'] }}"></p>
                    </div>
                @endforeach
            </div>

            <div class="mt-2 rounded-xl bg-brand-mint p-4 text-brand">
                <div class="flex items-center gap-2.5">
                    <x-icon name="shield" class="h-[18px] w-[18px]" />
                    <p class="text-sm font-medium">Kebutuhanmu boleh berbeda</p>
                </div>
                <p class="mt-2 text-[13px] leading-relaxed">Jika biaya hidup melebihi 50%, kecilkan pos keinginan lebih dulu. Jangan mengorbankan makan, kesehatan, atau tagihan penting demi mengikuti rasio.</p>
            </div>
        </section>

        {{-- Kolom kanan --}}
        <div class="space-y-5">
            <section class="fs-card">
                <h2 class="fs-h2">Urutan yang lebih aman</h2>
                <ol class="mt-5 space-y-4">
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-brand-mint text-xs font-semibold text-brand">1</span>
                        <div>
                            <p class="text-sm font-medium text-ink">Penuhi kebutuhan dasar</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Makan, tempat tinggal, transport, dan kesehatan didahulukan.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-brand-mint text-xs font-semibold text-brand">2</span>
                        <div>
                            <p class="text-sm font-medium text-ink">Petakan cicilan &amp; jatuh tempo</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Jika ada utang, sesuaikan pos sebelum membagi uang untuk keinginan.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-brand-mint text-xs font-semibold text-brand">3</span>
                        <div>
                            <p class="text-sm font-medium text-ink">Bangun dana darurat</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Mulai kecil, simpan terpisah, dan pilih tempat yang mudah dicairkan.</p>
                        </div>
                    </li>
                </ol>
            </section>

            <section class="rounded-2xl bg-iris-soft p-6">
                <div class="flex items-center gap-2.5 text-iris">
                    <x-icon name="umbrella" class="h-5 w-5" />
                    <h2 class="text-[18px] font-medium">Cadangan, bukan sisa uang</h2>
                </div>
                <p class="mt-4 text-[13px] text-slate-500" x-text="'Target awal ' + formatRupiah(Math.min(500000, targetDarurat)) + ', lalu bertahap ke 3× kebutuhan bulanan.'">Target awal Rp500.000, lalu bertahap ke 3× kebutuhan bulanan.</p>
                <p class="mt-3 break-words text-[30px] font-medium leading-none text-iris" x-text="formatRupiah(targetDarurat)">Rp4.500.000</p>
                <p class="mt-4 text-xs leading-relaxed text-slate-500" x-text="teksDarurat">3 × Rp1.500.000 kebutuhan. Dengan Rp400.000/bulan dari nol, target tercapai sekitar 12 bulan, tanpa menghitung imbal hasil.</p>
            </section>
        </div>
    </div>

    {{-- Ide kerja sampingan --}}
    <section>
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="fs-h2 !text-[20px]">Tambah pemasukan, bukan tambah risiko</h2>
            <p class="text-[13px] text-slate-500">Mulai dari keterampilan yang sudah kamu punya</p>
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-2" role="tablist" aria-label="Filter kategori ide">
            @foreach ($kategori as $nama)
                <button type="button" role="tab"
                        class="rounded-full border px-3.5 py-1.5 text-xs font-semibold transition"
                        x-bind:aria-selected="kategoriAktif === '{{ $nama }}'"
                        x-bind:class="kategoriAktif === '{{ $nama }}' ? 'border-brand bg-brand text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-brand hover:text-brand'"
                        x-on:click="kategoriAktif = '{{ $nama }}'">{{ $nama }}</button>
            @endforeach
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($ide as $item)
                <article class="fs-card flex flex-col" x-show="kategoriAktif === 'Semua' || kategoriAktif === '{{ $item['kategori'] }}'" x-transition.opacity.duration.200ms>
                    <div class="flex items-start justify-between">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-mint text-brand"><x-icon :name="$item['icon']" class="h-5 w-5" /></span>
                        <span class="fs-chip bg-iris-soft text-iris">{{ $item['kategori'] }}</span>
                    </div>
                    <h3 class="mt-5 text-xl font-medium text-ink">{{ $item['judul'] }}</h3>
                    <p class="mt-2 text-[15px] text-brand">Rp{{ number_format($item['min'], 0, ',', '.') }}–{{ number_format($item['max'], 0, ',', '.') }} / {{ $item['satuan'] }}</p>
                    <p class="mt-2 text-[13px] leading-relaxed text-slate-500">{{ $item['deskripsi'] }}</p>

                    {{-- Simulasi mini --}}
                    <div class="mt-4 rounded-xl bg-brand-mint/60 p-4">
                        <label class="text-xs font-semibold text-brand" for="qty-{{ $item['id'] }}">Hitung potensi penghasilan</label>
                        <div class="mt-2 flex items-center gap-2">
                            <input id="qty-{{ $item['id'] }}" type="number" min="0" max="500" step="1"
                                   class="w-20 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-ink outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                                   x-model.number="qty.{{ $item['id'] }}">
                            <span class="text-xs text-slate-600">{{ $item['satuan'] }} / bulan</span>
                        </div>
                        <p class="mt-3 text-[13px] font-semibold text-ink"
                           x-text="'Potensi tambahan: ' + formatRupiah(jumlah('{{ $item['id'] }}') * {{ $item['min'] }}) + ' – ' + formatRupiah(jumlah('{{ $item['id'] }}') * {{ $item['max'] }}) + ' / bulan'"></p>
                        <p class="mt-1 text-[11px] text-slate-500" x-show="gaji > 0"
                           x-text="'≈ ' + formatAngka(jumlah('{{ $item['id'] }}') * {{ $item['min'] }} / gaji * 100, 0) + '–' + formatAngka(jumlah('{{ $item['id'] }}') * {{ $item['max'] }} / gaji * 100, 0) + '% dari pemasukan bulananmu'"></p>
                    </div>

                    <div class="mt-5 border-t border-slate-200 pt-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Langkah mulai</p>
                        <ol class="mt-3 space-y-2.5">
                            @foreach ($item['langkah'] as $i => $langkah)
                                <li class="flex items-start gap-3 text-[13px] text-slate-600">
                                    <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded bg-slate-100 text-[11px] font-semibold text-slate-500">{{ $i + 1 }}</span>
                                    <span>{{ $langkah }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="mt-4 flex items-start gap-2 text-xs text-slate-500">
            <x-icon name="info" class="mt-px h-4 w-4 shrink-0" />
            <span>Kisaran ilustratif, bukan jaminan penghasilan. Sesuaikan wilayah dan pengalaman. Hindari tawaran yang meminta deposit, OTP, atau menjanjikan untung pasti.</span>
        </p>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function kelolaUang(awal) {
        let donut = null;

        const pct = function (v) { return clamp(Math.round(Number(v) || 0), 0, 100); };
        const R = function (v) { return formatRupiah(v); };

        return {
            pemasukanBulanan: awal.pemasukanBulanan,
            persen: { kebutuhan: awal.persen.kebutuhan, keinginan: awal.persen.keinginan, masaDepan: awal.persen.masaDepan },
            qty: { desain: awal.qty.desain, les: awal.qty.les, admin: awal.qty.admin },
            kategoriAktif: 'Semua',
            sorot: false,

            // ----- Input yang sudah disanitasi -----
            get gaji() { return clamp(Math.round(Number(this.pemasukanBulanan) || 0), 0, 9e12); },

            get p() {
                return {
                    kebutuhan: pct(this.persen.kebutuhan),
                    keinginan: pct(this.persen.keinginan),
                    masaDepan: pct(this.persen.masaDepan),
                };
            },

            get totalPersen() { return this.p.kebutuhan + this.p.keinginan + this.p.masaDepan; },

            // ----- Nominal pos utama -----
            get nominal() {
                return {
                    kebutuhan: this.gaji * (this.p.kebutuhan / 100),
                    keinginan: this.gaji * (this.p.keinginan / 100),
                    masaDepan: this.gaji * (this.p.masaDepan / 100),
                };
            },

            get totalAlokasi() {
                return this.nominal.kebutuhan + this.nominal.keinginan + this.nominal.masaDepan;
            },

            get sisa() { return Math.round(this.gaji - this.totalAlokasi); },

            // ----- Sub-rincian proporsional -----
            get detail() {
                const k = this.nominal.kebutuhan;
                const i = this.nominal.keinginan;
                const m = this.nominal.masaDepan;
                return {
                    kebutuhan: 'Makan ' + R(k * 0.6) + ' • transport ' + R(k * 0.2) + ' • pulsa & tagihan ' + R(k * 0.2),
                    keinginan: 'Hiburan ' + R(i * 4 / 9) + ' • jajan ' + R(i * 3 / 9) + ' • hobi ' + R(i * 2 / 9),
                    masaDepan: 'Dana darurat ' + R(m * 2 / 3) + ' • belajar & keterampilan ' + R(m * 1 / 3),
                };
            },

            // ----- Dana darurat -----
            get targetDarurat() { return 3 * this.nominal.kebutuhan; },
            get alokasiDarurat() { return Math.round(this.nominal.masaDepan * (2 / 3)); },
            get estimasiBulan() {
                return this.alokasiDarurat > 0 ? Math.ceil(this.targetDarurat / this.alokasiDarurat) : 0;
            },

            get teksDarurat() {
                const dasar = '3 × ' + R(this.nominal.kebutuhan) + ' kebutuhan. ';
                if (this.targetDarurat <= 0) {
                    return dasar + 'Isi pos kebutuhan lebih dulu agar target dana darurat bisa dihitung.';
                }
                if (this.alokasiDarurat <= 0) {
                    return dasar + 'Belum ada porsi masa depan untuk dana darurat. Sisihkan sebagian dulu agar target ini bisa mulai dicicil.';
                }
                let lama = 'sekitar ' + this.estimasiBulan + ' bulan';
                if (this.estimasiBulan >= 24) {
                    lama += ' (± ' + formatAngka(this.estimasiBulan / 12, 1) + ' tahun)';
                }
                return dasar + 'Dengan ' + R(this.alokasiDarurat) + '/bulan dari nol, target tercapai ' + lama + ', tanpa menghitung imbal hasil.';
            },

            // ----- Ide kerja sampingan -----
            jumlah(id) {
                return clamp(Math.round(Number(this.qty[id]) || 0), 0, 500);
            },

            // ----- Aksi -----
            resetRasio() {
                this.persen = { kebutuhan: 50, keinginan: 30, masaDepan: 20 };
            },

            lihatPembagian() {
                if (this.$refs.pembagian) {
                    this.$refs.pembagian.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                this.sorot = true;
                setTimeout(() => { this.sorot = false; }, 900);
            },

            // ----- Siklus hidup -----
            init() {
                this.buatDonut();
                this.perbaruiDonut();
                this.simpan();

                this.$watch('pemasukanBulanan', () => { this.perbaruiDonut(); this.simpan(); });
                this.$watch('persen', () => { this.perbaruiDonut(); this.simpan(); });
            },

            simpan() {
                simpanLokal('finshield.uang', {
                    kebutuhan: this.p.kebutuhan,
                    keinginan: this.p.keinginan,
                    masaDepan: this.p.masaDepan,
                    pemasukan: this.gaji,
                    waktu: Date.now(),
                });
            },

            // ----- Chart.js (donat) -----
            buatDonut() {
                const ctx = this.$refs.donut.getContext('2d');

                donut = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Kebutuhan', 'Keinginan', 'Masa depan', 'Belum dialokasikan'],
                        datasets: [{
                            data: [1, 0, 0, 0],
                            backgroundColor: ['#0D5C46', '#5B5BB5', '#8ECFC0', '#E2E8F0'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        animation: { duration: 350 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (item) { return ' ' + item.label + ': ' + formatRupiah(item.parsed); },
                                },
                            },
                        },
                    },
                });
            },

            perbaruiDonut() {
                if (!donut) return;
                const n = this.nominal;
                const sisaPositif = Math.max(0, this.sisa);
                const data = [n.kebutuhan, n.keinginan, n.masaDepan, sisaPositif];

                // Jika semua nol, tampilkan cincin abu-abu penuh.
                if (data.reduce(function (a, b) { return a + b; }, 0) <= 0) {
                    data[3] = 1;
                }

                donut.data.datasets[0].data = data;
                donut.update();
            },
        };
    }
</script>
@endpush
