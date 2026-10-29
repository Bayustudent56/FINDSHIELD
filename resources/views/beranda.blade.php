@extends('layouts.app')

@section('title', 'Beranda')
@section('page', 'Beranda')

@section('content')
<div x-data="beranda(@js($state))" x-cloak class="space-y-8">

    <header>
        <p class="fs-eyebrow">Mulai dari memahami</p>
        <h1 class="fs-h1 mt-2">Uangmu, masa depanmu.</h1>
        <p class="mt-3 text-[15px] text-slate-500">Kenali risikonya, hitung dampaknya, lalu susun langkah yang lebih sehat.</p>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-2xl bg-brand-dark px-7 py-9 text-white sm:px-10 sm:py-11">
        <div class="relative z-10 max-w-[560px]">
            <p class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-white/80">
                <x-icon name="shield" class="h-4 w-4" />
                Ruang aman untuk belajar
            </p>
            <h2 class="mt-5 text-[28px] font-semibold leading-snug sm:text-[32px]">Bukan soal cepat kaya. Soal lebih siap mengambil keputusan.</h2>
            <p class="mt-5 text-[14px] leading-relaxed text-white/75">Tiga alat belajar untuk melihat angka di balik risiko dan membangun kebiasaan keuangan yang bisa kamu jalani.</p>
            <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3">
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0E7A63] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#12906F] focus:outline-none focus:ring-2 focus:ring-white/50"
                        x-on:click="document.getElementById('alat').scrollIntoView({ behavior: 'smooth', block: 'start' })">
                    Jelajahi alat belajar
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </button>
                <span class="text-xs text-white/70">Tanpa uang asli • Tanpa menghakimi</span>
            </div>
        </div>

        {{-- Visual perisai + floating card --}}
        <div class="pointer-events-none absolute inset-y-0 right-6 hidden w-[380px] lg:block" aria-hidden="true">
            <div class="absolute left-1/2 top-1/2 h-[210px] w-[210px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/10"></div>
            <div class="absolute left-1/2 top-1/2 h-[170px] w-[170px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/10"></div>
            <div class="absolute left-1/2 top-1/2 flex h-[104px] w-[104px] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-3xl bg-[#14634F]">
                <svg class="h-14 w-14 text-white/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
            </div>

            <div class="absolute left-0 top-9 flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-ink shadow-lg">
                <x-icon name="chart" class="h-5 w-5 text-brand" />
                <div>
                    <p class="text-[10px] text-slate-500">Pahami</p>
                    <p class="text-[13px] font-medium">Risiko &amp; angka</p>
                </div>
            </div>
            <div class="absolute bottom-7 right-0 flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-ink shadow-lg">
                <x-icon name="sprout" class="h-5 w-5 text-brand" />
                <div>
                    <p class="text-[10px] text-slate-500">Bangun</p>
                    <p class="text-[13px] font-medium">Kebiasaan sehat</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3 kartu alat --}}
    <section id="alat" class="scroll-mt-24">
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="fs-h2">Mau mulai dari mana?</h2>
            <p class="text-[13px] text-slate-500">3 alat, satu tujuan: lebih berdaya</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            {{-- Judol --}}
            <article class="fs-card flex flex-col">
                <div class="flex items-start justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-mint text-brand"><x-icon name="chart" class="h-6 w-6" /></span>
                    <span class="text-xs text-slate-400">± 5 menit</span>
                </div>
                <h3 class="mt-5 text-xl font-medium text-ink">Pahami algoritma judol</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Lihat mengapa peluang sesaat berbeda dari risiko kerugian jangka panjang.</p>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-4">
                    <p class="text-[26px] font-medium leading-none text-brand" x-text="formatRupiah(judol.ev)">{{ $state['judol']['teks'] }}</p>
                    <p class="mt-2 text-xs text-slate-500" x-text="judol.dariUser ? 'Nilai harapan per putaran dari simulasi terakhirmu' : 'Nilai harapan per putaran pada contoh'">Nilai harapan per putaran pada contoh</p>
                </div>
                <a href="{{ route('edukasi-judol') }}" class="mt-auto flex items-center justify-between pt-5 text-sm font-medium text-brand hover:underline">
                    Buka simulasi edukasi <x-icon name="arrow-up-right" class="h-4 w-4" />
                </a>
            </article>

            {{-- Pinjol --}}
            <article class="fs-card flex flex-col">
                <div class="flex items-start justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-iris-soft text-iris"><x-icon name="calculator" class="h-6 w-6" /></span>
                    <span class="text-xs text-slate-400">± 5 menit</span>
                </div>
                <h3 class="mt-5 text-xl font-medium text-ink">Cek beban bunga pinjol</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Bongkar akumulasi bunga dan biaya agar total tagihan tidak terasa abstrak.</p>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-4">
                    <p class="text-[26px] font-medium leading-none text-iris" x-text="formatRupiah(pinjol.total)">{{ $state['pinjol']['teks'] }}</p>
                    <p class="mt-2 text-xs text-slate-500" x-text="pinjol.dariUser ? 'Total tagihan hari ke-' + pinjol.tenor + ' dari pokok ' + formatRupiah(pinjol.pokok) : 'Contoh total tagihan dari pokok Rp1 juta'">Contoh total tagihan dari pokok Rp1 juta</p>
                </div>
                <a href="{{ route('kalkulator-pinjol') }}" class="mt-auto flex items-center justify-between pt-5 text-sm font-medium text-brand hover:underline">
                    Hitung simulasi bunga <x-icon name="arrow-up-right" class="h-4 w-4" />
                </a>
            </article>

            {{-- Kelola uang --}}
            <article class="fs-card flex flex-col">
                <div class="flex items-start justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-mint text-brand"><x-icon name="wallet" class="h-6 w-6" /></span>
                    <span class="text-xs text-slate-400">± 5 menit</span>
                </div>
                <h3 class="mt-5 text-xl font-medium text-ink">Beri uangmu arah</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Bagi gaji atau uang saku, siapkan cadangan, dan temukan ide tambahan penghasilan.</p>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-4">
                    <p class="text-[26px] font-medium leading-none text-brand" x-text="uang.kebutuhan + ' / ' + uang.keinginan + ' / ' + uang.masaDepan">{{ $state['uang']['teks'] }}</p>
                    <p class="mt-2 text-xs text-slate-500" x-text="uang.dariUser ? 'Rasio alokasi yang terakhir kamu atur' : 'Titik awal alokasi, bukan aturan kaku'">Titik awal alokasi, bukan aturan kaku</p>
                </div>
                <a href="{{ route('kelola-uang') }}" class="mt-auto flex items-center justify-between pt-5 text-sm font-medium text-brand hover:underline">
                    Lihat panduan uang <x-icon name="arrow-up-right" class="h-4 w-4" />
                </a>
            </article>
        </div>
    </section>

    {{-- Checklist + bantuan --}}
    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_370px]">
        <div class="fs-card">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="fs-h2">Langkah kecil minggu ini</h2>
                <span class="text-[13px] text-slate-500">Mulai 10 menit</span>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuemin="0" x-bind:aria-valuemax="langkah.length" x-bind:aria-valuenow="selesai">
                    <div class="h-full rounded-full bg-brand transition-all duration-300" x-bind:style="'width:' + persen + '%'"></div>
                </div>
                <span class="text-xs font-semibold text-brand" x-text="selesai + ' dari ' + langkah.length + ' selesai'">0 dari 3 selesai</span>
            </div>

            <ul class="mt-5 space-y-4">
                <template x-for="item in langkah" x-bind:key="item.id">
                    <li class="flex items-start gap-3">
                        <button type="button" role="checkbox" x-bind:aria-checked="sudah(item.id)" x-bind:aria-label="item.judul"
                                class="mt-0.5 flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded border transition focus:outline-none focus:ring-2 focus:ring-brand/30"
                                x-bind:class="sudah(item.id) ? 'border-brand bg-brand text-white' : 'border-slate-300 bg-white hover:border-brand'"
                                x-on:click="toggle(item.id)">
                            <svg x-show="sudah(item.id)" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </button>
                        <div class="cursor-pointer" x-on:click="toggle(item.id)">
                            <p class="text-sm font-semibold transition" x-bind:class="sudah(item.id) ? 'text-slate-400 line-through' : 'text-ink'" x-text="item.judul"></p>
                            <p class="mt-0.5 text-xs text-slate-500" x-text="item.deskripsi"></p>
                        </div>
                    </li>
                </template>
            </ul>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4 text-xs">
                <p class="text-brand" x-show="selesai === langkah.length" x-transition>Kerja bagus! Semua langkah minggu ini sudah kamu selesaikan.</p>
                <p class="text-slate-400" x-show="selesai !== langkah.length">Centang saat sudah dilakukan. Progresmu tersimpan di perangkat ini.</p>
                <button type="button" class="text-slate-500 underline-offset-2 hover:text-ink hover:underline" x-show="selesai > 0" x-on:click="atur()">Atur ulang</button>
            </div>
        </div>

        <aside class="rounded-2xl border border-brand/10 bg-brand-mint p-6 text-brand">
            <div class="flex items-center gap-3">
                <x-icon name="heart" class="h-5 w-5" />
                <h2 class="text-[18px] font-medium">Sedang merasa tertekan?</h2>
            </div>
            <p class="mt-4 text-sm leading-relaxed">Kamu tidak harus menghadapi masalah uang sendirian. Cerita ke orang tepercaya dan hindari menutup utang dengan utang baru.</p>
            <div class="mt-5 border-t border-brand/15 pt-4">
                <p class="text-[13px] font-semibold">Cek legalitas &amp; kanal pengaduan</p>
                <p class="mt-3 text-[13px]">Kontak OJK 157 • melalui kanal resmi OJK</p>
            </div>
        </aside>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function beranda(awal) {
        const KUNCI_LANGKAH = 'finshield.checklist';

        return {
            judol: awal.judol,
            pinjol: awal.pinjol,
            uang: awal.uang,
            langkah: awal.langkah,
            tercentang: [],

            init() {
                this.muatRingkasan();
                const tersimpan = bacaLokal(KUNCI_LANGKAH);
                if (Array.isArray(tersimpan)) {
                    const idValid = this.langkah.map(function (l) { return l.id; });
                    this.tercentang = tersimpan.filter(function (id) { return idValid.indexOf(id) !== -1; });
                }
            },

            /** Ambil hasil terakhir dari localStorage (diisi oleh halaman simulasi masing-masing). */
            muatRingkasan() {
                const j = bacaLokal('finshield.judol');
                if (j && typeof j.ev === 'number' && isFinite(j.ev)) {
                    this.judol = { ev: j.ev, houseEdge: j.houseEdge, dariUser: true };
                }

                const p = bacaLokal('finshield.pinjol');
                if (p && typeof p.total === 'number' && isFinite(p.total)) {
                    this.pinjol = { total: p.total, pokok: p.pokok, tenor: p.tenor, dariUser: true };
                }

                const u = bacaLokal('finshield.uang');
                if (u && typeof u.kebutuhan === 'number') {
                    this.uang = { kebutuhan: u.kebutuhan, keinginan: u.keinginan, masaDepan: u.masaDepan, dariUser: true };
                }
            },

            sudah(id) {
                return this.tercentang.indexOf(id) !== -1;
            },

            toggle(id) {
                if (this.sudah(id)) {
                    this.tercentang = this.tercentang.filter(function (x) { return x !== id; });
                } else {
                    this.tercentang = this.tercentang.concat([id]);
                }
                simpanLokal(KUNCI_LANGKAH, this.tercentang);
            },

            atur() {
                this.tercentang = [];
                simpanLokal(KUNCI_LANGKAH, this.tercentang);
            },

            get selesai() {
                return this.tercentang.length;
            },

            get persen() {
                return this.langkah.length ? Math.round((this.selesai / this.langkah.length) * 100) : 0;
            },
        };
    }
</script>
@endpush
