<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinShield') · FinShield</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Helper JavaScript global: dipakai semua halaman (format Rupiah, angka, penyimpanan lokal) --}}
    <script>
        window.clamp = function (nilai, min, max) {
            return Math.min(max, Math.max(min, nilai));
        };

        /** formatRupiah(1000000) => "Rp1.000.000", formatRupiah(-540000) => "-Rp540.000" */
        window.formatRupiah = function (angka) {
            let n = Number(angka);
            if (!isFinite(n)) n = 0;
            n = Math.round(n);
            const teks = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.abs(n));
            return (n < 0 ? '-' : '') + 'Rp' + teks;
        };

        /** Angka biasa dengan pemisah Indonesia: formatAngka(45.5) => "45,5" */
        window.formatAngka = function (angka, maksDesimal) {
            const n = Number(angka);
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: maksDesimal === undefined ? 2 : maksDesimal,
            }).format(isFinite(n) ? n : 0);
        };

        /** Ringkas untuk sumbu grafik: Rp900 rb, Rp1,2 jt, Rp3 M */
        window.formatRingkas = function (angka) {
            const n = Number(angka) || 0;
            const a = Math.abs(n);
            const s = n < 0 ? '-' : '';
            const f = function (v, satuan) {
                return s + 'Rp' + window.formatAngka(v, 1) + ' ' + satuan;
            };
            if (a >= 1e12) return f(a / 1e12, 'T');
            if (a >= 1e9) return f(a / 1e9, 'M');
            if (a >= 1e6) return f(a / 1e6, 'jt');
            if (a >= 1e3) return f(a / 1e3, 'rb');
            return s + 'Rp' + Math.round(a);
        };

        /** Ambil angka dari teks input: "Rp1.250.000" => 1250000 */
        window.parseRupiah = function (teks) {
            const digit = String(teks === undefined || teks === null ? '' : teks).replace(/[^0-9]/g, '');
            return Math.min(Number(digit) || 0, 9e12);
        };

        window.simpanLokal = function (kunci, nilai) {
            try { localStorage.setItem(kunci, JSON.stringify(nilai)); } catch (e) { /* abaikan jika storage diblokir */ }
        };

        window.bacaLokal = function (kunci) {
            try {
                const mentah = localStorage.getItem(kunci);
                return mentah ? JSON.parse(mentah) : null;
            } catch (e) {
                return null;
            }
        };
    </script>
</head>

@php
    $navigasi = [
        ['route' => 'beranda',           'label' => 'Beranda',           'icon' => 'grid'],
        ['route' => 'edukasi-judol',     'label' => 'Edukasi judol',     'icon' => 'chart'],
        ['route' => 'kalkulator-pinjol', 'label' => 'Kalkulator pinjol', 'icon' => 'calculator'],
        ['route' => 'kelola-uang',       'label' => 'Kelola uang',       'icon' => 'wallet'],
    ];
@endphp

<body class="bg-canvas font-sans text-ink antialiased" x-data="{ sidebarTerbuka: false, bantuan: false }" x-on:keydown.escape.window="sidebarTerbuka = false; bantuan = false">

    {{-- Overlay mobile --}}
    <div class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" x-show="sidebarTerbuka" x-cloak x-transition.opacity x-on:click="sidebarTerbuka = false"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full transform flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0"
           x-bind:class="sidebarTerbuka ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex items-center gap-3 px-6 pb-2 pt-7">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand text-white">
                <x-icon name="shield" class="h-6 w-6" />
            </span>
            <span class="text-[22px] font-bold tracking-tight text-ink">FinShield</span>
            <button type="button" class="ml-auto rounded-md p-1 text-slate-400 hover:text-slate-600 lg:hidden" x-on:click="sidebarTerbuka = false" aria-label="Tutup menu">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <nav class="mt-6 px-4" aria-label="Navigasi utama">
            <p class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Ruang belajar</p>
            <ul class="space-y-2">
                @foreach ($navigasi as $item)
                    @php $aktif = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           @if ($aktif) aria-current="page" @endif
                           class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] transition {{ $aktif ? 'bg-brand-mint font-semibold text-brand' : 'text-slate-600 hover:bg-slate-50 hover:text-ink' }}">
                            <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                            <span>{{ $item['label'] }}</span>
                            @if ($aktif)
                                <span class="ml-auto h-5 w-1 rounded-full bg-brand"></span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="mx-4 mt-8 rounded-2xl bg-slate-50 p-4">
            <x-icon name="sprout" class="h-5 w-5 text-brand" />
            <p class="mt-3 text-sm font-semibold leading-snug text-ink">Pelan-pelan, lebih paham.</p>
            <p class="mt-2 text-xs leading-relaxed text-slate-500">Keputusan kecil hari ini bisa melindungi rencana besok.</p>
        </div>

        <div class="relative mt-auto px-6 pb-6">
            <div class="absolute bottom-full left-4 right-4 mb-2 rounded-xl border border-slate-200 bg-white p-3 text-xs leading-relaxed text-slate-600 shadow-lg"
                 x-show="bantuan" x-cloak x-transition x-on:click.outside="bantuan = false">
                Perlu cek legalitas atau menyampaikan pengaduan? Hubungi <strong>Kontak OJK 157</strong> melalui kanal resmi OJK.
            </div>
            <button type="button" class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-ink" x-on:click="bantuan = !bantuan" x-bind:aria-expanded="bantuan">
                <x-icon name="help" class="h-5 w-5 text-slate-400" />
                Butuh bantuan?
            </button>
            <p class="mt-2 text-[11px] text-slate-400">FinShield • Literasi untuk semua</p>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="sticky top-0 z-20 flex h-[72px] items-center gap-3 border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-9">
            <button type="button" class="-ml-1 rounded-md p-1.5 text-slate-600 hover:bg-slate-100 lg:hidden" x-on:click="sidebarTerbuka = true" aria-label="Buka menu">
                <x-icon name="menu" class="h-6 w-6" />
            </button>

            <nav aria-label="Breadcrumb" class="flex items-center gap-2.5 text-sm">
                <span class="text-slate-500">Ruang belajar</span>
                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                <span class="font-semibold text-ink">@yield('page', 'Beranda')</span>
            </nav>

            <div class="ml-auto flex items-center gap-4">
                <span class="fs-chip bg-brand-mint text-brand">Mode edukasi</span>
                <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-iris-soft text-[11px] font-bold text-iris">FS</span>
                    <span class="hidden text-sm text-slate-500 sm:inline">Teman FinShield</span>
                </div>
            </div>
        </header>

        <main class="w-full max-w-[1320px] flex-1 px-5 py-8 sm:px-9">
            @yield('content')
        </main>

        <footer class="flex flex-col gap-1 px-5 pb-8 pt-2 text-xs text-slate-400 sm:flex-row sm:justify-between sm:px-9">
            <span>© {{ date('Y') }} FinShield. Pahami risikonya, pegang kendalinya.</span>
            <span>Edukasi, bukan nasihat keuangan personal</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
