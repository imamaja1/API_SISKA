<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>API SISKA | API Documentation &amp; Live Tester</title>
    <!-- Tailwind CSS v3 with Plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Tailwind Configuration matching Stitch Design System -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        navy: {
                            950: '#060a12',
                            900: '#0a101d',
                            850: '#0d1527',
                            800: '#111c34',
                            750: '#152340',
                            700: '#1e2c4d',
                        },
                        accent: {
                            blue: '#2563eb',
                            cyan: '#06b6d4',
                            emerald: '#10b981',
                            amber: '#f59e0b',
                            purple: '#6366f1',
                        }
                    }
                }
            }
        }
    </script>

    <style data-purpose="custom-scrollbars">
        /* Custom Sleek Scrollbar for Developer Tool Feel */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #080e1a;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>

    <style data-purpose="ui-subtle-glow">
        .glow-dot {
            box-shadow: 0 0 8px #10b981;
        }
        .code-editor-bg {
            background-color: #060a13;
        }
        /* Toast notification animation (Top-Right Slide) */
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-12px); }
            15% { opacity: 1; transform: translateY(0); }
            85% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-12px); }
        }
        .toast-animate {
            animation: fadeInOut 2.4s forwards;
        }
    </style>
</head>

<body class="bg-[#070d19] text-slate-300 font-sans h-screen flex flex-col overflow-hidden select-none">

    <!-- ======================================================== -->
    <!-- BEGIN: TopNavbar                                         -->
    <!-- ======================================================== -->
    <header class="h-12 border-b border-slate-800/80 bg-[#091120] flex items-center justify-between px-3.5 z-20 flex-shrink-0">
        <!-- Brand / Gateway Info -->
        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 rounded bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M4 7V4h16v3M9 20h6M12 4v16"></path>
                    </svg>
                </div>
                <span class="font-bold text-white tracking-wide text-xs">API SISKA</span>
            </div>
            <div class="flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-emerald-950/60 border border-emerald-700/50 text-[11px] font-mono text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse glow-dot"></span>
                <span class="font-semibold tracking-wider">GATEWAY v1.0</span>
                <span class="text-emerald-500/80 font-normal">Sanctum SPA</span>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center space-x-2.5">
            <!-- Reset Layout Button -->
            <button id="btnResetLayout" class="flex items-center space-x-1.5 text-xs text-slate-300 hover:text-white bg-slate-800/70 hover:bg-slate-700/80 border border-slate-700/60 px-2.5 py-1 rounded transition cursor-pointer" type="button" title="Reset layout dan pencarian">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 10h10a5 5 0 0 1 5 5v2m0 0l-3-3m3 3l3-3M3 10l3-3M3 10l3 3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span>Reset Layout</span>
            </button>

            <!-- Tester Toggle Button -->
            <button id="btnToggleTester" class="flex items-center space-x-1.5 text-xs text-slate-300 hover:text-white bg-slate-800/70 hover:bg-slate-700/80 border border-emerald-600/50 px-2.5 py-1 rounded transition cursor-pointer" type="button" title="Tampilkan / Sembunyikan Live API Tester">
                <span class="text-emerald-400 font-bold text-xs">⚡</span>
                <span class="text-emerald-300 font-medium">Tester</span>
            </button>

            <div class="h-4 w-[1px] bg-slate-800"></div>

            <!-- Current Logged In Administrator -->
            <div class="flex items-center space-x-1.5 text-xs text-slate-300 px-1.5 cursor-pointer hover:text-white transition">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5.121 17.804A9 9 0 0118.879 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span class="font-medium">{{ $user->name ?? 'Administrator' }}</span>
            </div>

            <!-- Link to Filament Admin Panel -->
            <a href="{{ url('/sa_api') }}" class="flex items-center space-x-1.5 text-xs text-slate-200 bg-slate-800/90 hover:bg-slate-700 border border-slate-700/70 px-2.5 py-1 rounded transition" title="Buka Filament Admin Panel">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16m-7 6h7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span>Admin Panel</span>
            </a>

            <!-- Logout Button -->
            <form action="{{ route('api_user.logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-slate-400 hover:text-rose-400 p-1 rounded transition cursor-pointer" title="Logout" type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            </form>
        </div>
    </header>
    <!-- END: TopNavbar -->

    <!-- ======================================================== -->
    <!-- BEGIN: WorkspaceLayout                                   -->
    <!-- ======================================================== -->
    <div class="flex flex-1 overflow-hidden">

        <!-- ==================================================== -->
        <!-- BEGIN: LeftSidebar (Endpoints Navigation)            -->
        <!-- ==================================================== -->
        <aside id="leftSidebar" class="w-72 flex-shrink-0 bg-[#080e1a] border-r border-slate-800/80 flex flex-col text-xs" data-purpose="endpoint-explorer">
            <!-- Section Title & Counter -->
            <div class="p-2.5 border-b border-slate-800/70 flex items-center justify-between">
                <div class="flex items-center space-x-1.5 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 10h16M4 14h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <span>Seksi &amp; Endpoint</span>
                </div>
                <span id="routesBadge" class="bg-blue-950/70 text-blue-400 border border-blue-800/40 px-1.5 py-0.5 rounded text-[10px] font-mono">
                    {{ $totalRoutes }} Routes
                </span>
            </div>

            <!-- Quick Search Bar -->
            <div class="p-2.5 pb-2">
                <div class="relative flex items-center">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <input id="searchInput" class="w-full bg-[#0d1627] text-slate-200 placeholder-slate-500 pl-8 pr-6 py-1.5 rounded border border-slate-800 focus:outline-none focus:border-blue-500 text-xs font-mono" placeholder="Cari endpoint..." type="text" value="" />
                    <span class="absolute right-2 text-[10px] text-slate-500 border border-slate-700 px-1 rounded bg-slate-800/50 cursor-pointer" title="Tekan '/' untuk mencari">/</span>
                </div>
            </div>

            <!-- HTTP Method Filters -->
            <div class="px-2.5 pb-2.5 flex space-x-1 border-b border-slate-800/60 font-mono text-[10px]" id="methodFiltersContainer">
                <button data-method="ALL" class="method-filter-btn flex-1 py-0.5 bg-blue-600 text-white font-semibold rounded text-center transition cursor-pointer" type="button">ALL</button>
                <button data-method="GET" class="method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-blue-400 rounded text-center transition cursor-pointer" type="button">GET</button>
                <button data-method="POST" class="method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-emerald-400 rounded text-center transition cursor-pointer" type="button">POST</button>
                <button data-method="PUT" class="method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-amber-400 rounded text-center transition cursor-pointer" type="button">PUT</button>
                <button data-method="DELETE" class="method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-rose-400 rounded text-center transition cursor-pointer" type="button">DEL</button>
            </div>

            <!-- Endpoint List Tree (Scrollable) -->
            <div id="endpointTree" class="flex-1 overflow-y-auto px-1.5 py-2 space-y-3 font-sans">
                @foreach ($categories as $cat)
                <div class="category-block" data-cat-id="{{ $cat['id'] }}">
                    <!-- Category Header Accordion Button -->
                    <div class="cat-header-btn flex items-center justify-between px-2 py-1 text-slate-300 font-medium cursor-pointer hover:text-white group select-none" data-target="cat-content-{{ $cat['id'] }}">
                        <div class="flex items-center space-x-1.5 truncate">
                            <svg class="w-3.5 h-3.5 text-blue-400 fill-current flex-shrink-0" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                            </svg>
                            <span class="font-semibold text-xs tracking-tight truncate">{{ $cat['name'] }}</span>
                        </div>
                        <div class="flex items-center space-x-1 flex-shrink-0">
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-slate-800 text-slate-400">{{ $cat['count'] }}</span>
                        </div>
                    </div>

                    <!-- Category Items List -->
                    <div id="cat-content-{{ $cat['id'] }}" class="cat-content mt-0.5 space-y-0.5">
                        @foreach ($cat['endpoints'] as $ep)
                        <div class="endpoint-item flex items-center justify-between px-2 py-1 rounded cursor-pointer transition select-none group {{ $ep['id'] === $initialEndpoint['id'] ? 'bg-blue-900/25 border-l-2 border-blue-500' : 'hover:bg-slate-800/40' }}"
                            data-ep-id="{{ $ep['id'] }}"
                            data-method="{{ $ep['method'] }}"
                            data-path="{{ $ep['path'] }}"
                            data-cat="{{ $cat['name'] }}">
                            <div class="flex items-center space-x-2 truncate min-w-0">
                                @if ($ep['method'] === 'GET')
                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-blue-950 text-blue-400 border border-blue-800/50 flex-shrink-0">GET</span>
                                @elseif ($ep['method'] === 'POST')
                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-emerald-950 text-emerald-400 border border-emerald-800/50 flex-shrink-0">POST</span>
                                @elseif ($ep['method'] === 'PUT')
                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-amber-950 text-amber-400 border border-amber-800/50 flex-shrink-0">PUT</span>
                                @elseif ($ep['method'] === 'DELETE')
                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-rose-950 text-rose-400 border border-rose-800/50 flex-shrink-0">DEL</span>
                                @else
                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-purple-950 text-purple-400 border border-purple-800/50 flex-shrink-0">{{ $ep['method'] }}</span>
                                @endif
                                <span class="ep-path-text text-xs font-mono truncate {{ $ep['id'] === $initialEndpoint['id'] ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}">{{ $ep['path'] }}</span>
                            </div>
                            <span class="active-dot w-1.5 h-1.5 rounded-full bg-emerald-400 ml-1.5 flex-shrink-0 glow-dot {{ $ep['id'] === $initialEndpoint['id'] ? '' : 'hidden' }}"></span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Left Sidebar Footer Status -->
            <div class="p-2.5 bg-[#060a13] border-t border-slate-800/80 text-[11px] font-mono flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5">
                        <span id="gatewayStatusDot" class="w-2 h-2 rounded-full bg-emerald-400 glow-dot animate-pulse"></span>
                        <span class="text-slate-400 font-semibold tracking-wider text-[10px]">API GATEWAY</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span id="gatewayPingLatency" class="text-[10px] text-slate-400 font-mono">--ms</span>
                        <span id="gatewayStatusLabel" class="text-emerald-400 font-bold text-[10px]">ONLINE</span>
                    </div>
                </div>
                <div class="flex items-center justify-between text-slate-500 mt-1 text-[10px]">
                    <span>Engine: Laravel {{ app()->version() }}</span>
                    <span class="text-slate-400 font-medium">Sanctum SPA</span>
                </div>
            </div>
        </aside>
        <!-- END: LeftSidebar -->

        <!-- Left Splitter Gutter (Resizable) -->
        <div id="gutter-left" class="w-1 hover:w-1.5 bg-slate-800/80 hover:bg-blue-500 active:bg-blue-400 cursor-col-resize transition-all flex-shrink-0 relative group z-10" title="Geser untuk mengatur lebar sidebar (Double click untuk reset)">
            <div class="absolute inset-y-0 -left-1.5 -right-1.5 cursor-col-resize"></div>
        </div>

        <!-- ==================================================== -->
        <!-- BEGIN: MainContent (Endpoint Documentation)          -->
        <!-- ==================================================== -->
        <main id="centerDocPanel" class="flex-1 overflow-y-auto bg-[#0a101d] px-8 py-5 border-r border-slate-800/80 min-w-[350px]" data-purpose="endpoint-documentation">
            <!-- Endpoint Header Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <span id="docMethodBadge" class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-blue-600 text-white tracking-wide shadow">
                        {{ $initialEndpoint['method'] }}
                    </span>
                    <span id="docPath" class="font-mono text-sm text-slate-100 font-medium tracking-tight">
                        {{ $initialEndpoint['path'] }}
                    </span>
                    <span id="docStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-950 text-emerald-400 border border-emerald-800/50">
                        {{ $initialEndpoint['status'] }}
                    </span>
                </div>
                <button id="btnCopyUrl" class="flex items-center space-x-1.5 text-xs text-slate-300 bg-slate-800 hover:bg-slate-700/80 border border-slate-700 px-3 py-1 rounded transition cursor-pointer" type="button">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <span>Salin URL</span>
                </button>
            </div>

            <!-- Title & Description -->
            <div class="mt-5">
                <div id="docBreadcrumb" class="text-[10px] font-mono tracking-widest text-slate-400 font-medium uppercase">
                    {{ $initialEndpoint['breadcrumb'] }}
                </div>
                <h1 id="docTitle" class="text-2xl font-bold text-white tracking-tight mt-1">
                    {{ $initialEndpoint['judul'] }}
                </h1>
                <p id="docDescription" class="text-xs text-slate-400 mt-2 font-normal leading-relaxed">
                    {{ $initialEndpoint['description'] }}
                </p>
            </div>

            <!-- Sanctum Info Notice Box -->
            <div id="docSanctumBox" class="mt-5 bg-gradient-to-r from-blue-950/40 to-slate-900/60 border border-blue-900/60 rounded-md p-3.5 flex items-start space-x-3">
                <div class="text-blue-400 flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <div class="text-xs leading-relaxed text-slate-300">
                    <strong class="text-blue-300 font-semibold">Autentikasi &amp; Proteksi Sanctum:</strong>
                    <span id="docSanctumNote">{{ $initialEndpoint['sanctum_note'] }}</span>
                </div>
            </div>

            <!-- Request Headers Section -->
            <div class="mt-7">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="w-1.5 h-3.5 bg-blue-500 rounded-sm"></span>
                        <h2 class="text-sm font-bold text-white tracking-tight">Request Headers</h2>
                    </div>
                    <span class="text-[10px] font-mono text-slate-500">HTTP 1.1 / HTTP 2</span>
                </div>
                <div class="mt-2.5 overflow-hidden rounded border border-slate-800">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-900/80 text-[10px] font-mono text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                                <th class="py-2 px-3 font-semibold">HEADER KEY</th>
                                <th class="py-2 px-3 font-semibold">TIPE</th>
                                <th class="py-2 px-3 font-semibold">STATUS</th>
                                <th class="py-2 px-3 font-semibold">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody id="docHeadersTableBody" class="divide-y divide-slate-800/80 font-mono text-[11px]">
                            <tr class="hover:bg-slate-900/30">
                                <td class="py-2 px-3 font-semibold text-sky-400">Accept</td>
                                <td class="py-2 px-3 text-slate-400 font-sans">string</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-amber-950/80 text-amber-400 border border-amber-800/50 font-sans font-medium">Wajib</span>
                                </td>
                                <td class="py-2 px-3 text-slate-300 font-sans">Harus bernilai application/json</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Request Parameters Section (Query & Path for GET / DELETE) -->
            <div id="docParamsSection" class="mt-7">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="w-1.5 h-3.5 bg-blue-500 rounded-sm"></span>
                        <h2 id="docParamsHeading" class="text-sm font-bold text-white tracking-tight">Request Parameters (Query &amp; Path)</h2>
                    </div>
                    <span id="docParamsBadge" class="text-[10px] font-mono px-2 py-0.5 rounded bg-blue-950 text-blue-400 border border-blue-800/50">URL Parameters</span>
                </div>
                <!-- Parameters 5-Column Table -->
                <div id="docParamsTableContainer" class="mt-2.5 overflow-hidden rounded border border-slate-800 hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-900/80 text-[10px] font-mono text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                                <th class="py-2 px-3 font-semibold">FIELD</th>
                                <th class="py-2 px-3 font-semibold">TIPE</th>
                                <th class="py-2 px-3 font-semibold">VALIDASI</th>
                                <th class="py-2 px-3 font-semibold">CONTOH / DESKRIPSI</th>
                                <th class="py-2 px-3 font-semibold">ASAL NILAI (SOURCE)</th>
                            </tr>
                        </thead>
                        <tbody id="docParamsTableBody" class="divide-y divide-slate-800/80 font-mono text-[11px]"></tbody>
                    </table>
                </div>
                <!-- Raw Parameters Block -->
                <div class="mt-2.5 bg-[#060a13] border border-slate-800 rounded">
                    <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-800/80 bg-slate-900/40 text-[11px] text-slate-400 font-mono">
                        <span id="docParamsSchemaLabel">Parameters Schema</span>
                        <button id="btnCopyParams" class="text-slate-400 hover:text-white flex items-center space-x-1 cursor-pointer transition" type="button">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                    <pre id="docParamsBlock" class="p-3 text-slate-500 font-mono text-xs leading-relaxed overflow-x-auto">{{ $initialEndpoint['params_schema'] }}</pre>
                </div>
            </div>

            <!-- Request Body Schema Section (JSON Payload for POST / PUT / PATCH) -->
            <div id="docBodySection" class="mt-7 hidden">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="w-1.5 h-3.5 bg-emerald-500 rounded-sm"></span>
                        <h2 id="docBodyHeading" class="text-sm font-bold text-white tracking-tight">Request Body (JSON Schema)</h2>
                    </div>
                    <span id="docBodyBadge" class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800/50">application/json</span>
                </div>
                <!-- Body 5-Column Table -->
                <div id="docBodyTableContainer" class="mt-2.5 overflow-hidden rounded border border-slate-800">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-900/80 text-[10px] font-mono text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                                <th class="py-2 px-3 font-semibold">FIELD</th>
                                <th class="py-2 px-3 font-semibold">TIPE</th>
                                <th class="py-2 px-3 font-semibold">VALIDASI</th>
                                <th class="py-2 px-3 font-semibold">CONTOH / DESKRIPSI</th>
                                <th class="py-2 px-3 font-semibold">ASAL NILAI (SOURCE)</th>
                            </tr>
                        </thead>
                        <tbody id="docBodyTableBody" class="divide-y divide-slate-800/80 font-mono text-[11px]"></tbody>
                    </table>
                </div>
                <!-- Raw JSON Body Block -->
                <div class="mt-2.5 bg-[#060a13] border border-slate-800 rounded">
                    <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-800/80 bg-slate-900/40 text-[11px] text-slate-400 font-mono">
                        <span>Payload Schema</span>
                        <button id="btnCopyBody" class="text-slate-400 hover:text-white flex items-center space-x-1 cursor-pointer transition" type="button">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span>Salin JSON</span>
                        </button>
                    </div>
                    <pre id="docBodyRawBlock" class="p-3 text-slate-300 font-mono text-xs leading-relaxed overflow-x-auto"></pre>
                </div>
            </div>

            <!-- Response Specification Section -->
            <div class="mt-7">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="w-1.5 h-3.5 bg-blue-500 rounded-sm"></span>
                        <h2 class="text-sm font-bold text-white tracking-tight">Contoh Response Spesifikasi</h2>
                    </div>
                    <!-- Status Codes Filter -->
                    <div class="flex space-x-1 font-mono text-[10px]" id="responseStatusTabs">
                        <button data-status="200" class="resp-status-btn px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold transition cursor-pointer" type="button">200 OK</button>
                        <button data-status="401" class="resp-status-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer" type="button">401 Unauthorized</button>
                        <button data-status="403" class="resp-status-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer" type="button">403 Forbidden</button>
                        <button data-status="422" class="resp-status-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer" type="button">422 Unprocessable</button>
                    </div>
                </div>
                <div class="mt-2.5 bg-[#060a13] border border-slate-800 rounded">
                    <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-800/80 bg-slate-900/40 text-[11px] text-slate-400 font-mono">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 glow-dot"></span>
                            <span>Response Body (JSON)</span>
                        </div>
                        <button id="btnCopyResponse" class="text-slate-400 hover:text-white flex items-center space-x-1 cursor-pointer transition" type="button">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span>Salin Respons</span>
                        </button>
                    </div>
                    <pre id="docResponseBlock" class="p-3.5 font-mono text-xs text-slate-300 leading-relaxed overflow-x-auto">{{ $initialEndpoint['responses']['200'] }}</pre>
                </div>
            </div>

            <!-- Code Snippets Implementation -->
            <div class="mt-7 mb-6">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="w-1.5 h-3.5 bg-blue-500 rounded-sm"></span>
                        <h2 class="text-sm font-bold text-white tracking-tight">Code Implementation Snippets</h2>
                    </div>
                    <div class="flex space-x-1 font-mono text-[10px]" id="snippetLangTabs">
                        <button data-lang="curl" class="snippet-lang-btn px-2 py-0.5 rounded bg-blue-600 text-white font-semibold transition cursor-pointer" type="button">CURL</button>
                        <button data-lang="js" class="snippet-lang-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer" type="button">JavaScript (Fetch)</button>
                        <button data-lang="php" class="snippet-lang-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer" type="button">PHP (Laravel Http)</button>
                    </div>
                </div>
                <div class="mt-2.5 bg-[#060a13] border border-slate-800 rounded">
                    <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-800/80 bg-slate-900/40 text-[11px] text-slate-400 font-mono">
                        <span id="snippetLangLabel">cURL Command</span>
                        <button id="btnCopySnippet" class="text-slate-400 hover:text-white flex items-center space-x-1 cursor-pointer transition" type="button">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span>Salin Kode</span>
                        </button>
                    </div>
                    <pre id="docSnippetBlock" class="p-3.5 font-mono text-xs text-slate-300 leading-relaxed overflow-x-auto"></pre>
                </div>
            </div>
        </main>
        <!-- END: MainContent -->

        <!-- Right Splitter Gutter (Resizable) -->
        <div id="gutter-right" class="w-1 hover:w-1.5 bg-slate-800/80 hover:bg-blue-500 active:bg-blue-400 cursor-col-resize transition-all flex-shrink-0 relative group z-10" title="Geser untuk mengatur lebar Live API Tester (Double click untuk reset)">
            <div class="absolute inset-y-0 -left-1.5 -right-1.5 cursor-col-resize"></div>
        </div>

        <!-- ==================================================== -->
        <!-- BEGIN: LiveApiTester (Right Panel)                   -->
        <!-- ==================================================== -->
        <aside id="rightTesterPanel" class="w-[500px] flex-shrink-0 bg-[#080e1a] flex flex-col" data-purpose="live-api-tester">
            <!-- Panel Header -->
            <div class="p-2.5 border-b border-slate-800/80 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 glow-dot animate-pulse"></span>
                    <span class="text-xs font-bold text-white tracking-wider">LIVE API TESTER</span>
                </div>
                <span class="bg-slate-800 text-slate-400 border border-slate-700/60 px-1.5 py-0.5 rounded text-[10px] font-mono">HTTP Client</span>
            </div>

            <!-- Request Input URL Bar -->
            <div class="p-3 border-b border-slate-800/80 flex-shrink-0">
                <div class="flex rounded border border-slate-700/80 bg-[#0c1424] overflow-hidden focus-within:border-blue-500">
                    <select id="testerMethod" class="px-2.5 py-1.5 bg-emerald-950/70 border-0 border-r border-slate-700/80 text-emerald-400 font-mono font-bold text-xs focus:ring-0 cursor-pointer">
                        <option value="GET" selected>GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                    <input id="testerUrl" class="w-full bg-transparent border-0 px-2.5 py-1 text-slate-200 font-mono text-xs focus:ring-0 truncate placeholder-slate-500" type="text" value="{{ $initialTester['url'] }}" />
                </div>
            </div>

            <!-- Request Config Tabs -->
            <div class="px-3 border-b border-slate-800 flex justify-between items-center text-xs flex-shrink-0">
                <div class="flex space-x-4 font-medium" id="testerRequestTabs">
                    <button data-tab="params" class="tester-tab-btn py-2 text-white border-b-2 border-blue-500 font-medium cursor-pointer transition" type="button">Params</button>
                    <button data-tab="body" class="tester-tab-btn py-2 text-slate-400 hover:text-slate-200 cursor-pointer transition flex items-center space-x-1" type="button">
                        <span>Body</span>
                        <span id="tabBodyIndicator" class="w-1.5 h-1.5 rounded-full bg-amber-400 hidden"></span>
                    </button>
                    <button data-tab="headers" class="tester-tab-btn py-2 text-slate-400 hover:text-slate-200 cursor-pointer transition" type="button">Headers</button>
                    <button data-tab="cookies" class="tester-tab-btn py-2 text-slate-400 hover:text-slate-200 cursor-pointer transition flex items-center space-x-1" type="button">
                        <span>Cookies Debug</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse glow-dot"></span>
                    </button>
                </div>
                <span id="testerTabHint" class="text-[10px] font-mono text-slate-500 truncate max-w-[150px]">URL • Query &amp; Path Params</span>
            </div>

            <!-- Auto-Chained Notification Bar -->
            <div id="autoChainBar" class="hidden px-3 py-1 bg-emerald-950/70 border-b border-emerald-800/50 text-[10px] font-mono text-emerald-300 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-1.5 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse glow-dot"></span>
                    <span class="font-bold text-emerald-400">Auto-Chained:</span>
                    <span id="autoChainText" class="truncate text-slate-200 font-sans"></span>
                </div>
                <button id="btnClearAutoChain" class="text-emerald-400 hover:text-emerald-200 underline text-[9px] cursor-pointer ml-2 flex-shrink-0" type="button" title="Reset parameter auto-chain">Reset</button>
            </div>

            <!-- Request Params JSON Body Editor Area -->
            <div class="flex-1 flex flex-col code-editor-bg border-b border-slate-800 min-h-[140px]">
                <textarea id="testerEditor" rows="6" class="w-full flex-1 p-3.5 font-mono text-xs text-slate-300 bg-transparent border-0 focus:ring-0 resize-none leading-relaxed overflow-y-auto custom-scrollbar">{{ $initialTester['params'] }}</textarea>
                <div class="px-3 py-1.5 border-t border-slate-800/80 bg-[#091120] flex items-center justify-between text-[10px] font-mono text-slate-400 flex-shrink-0">
                    <span id="testerEditorLabel">Params URL / Query String</span>
                    <span id="testerCharsCounter">Chars: 0</span>
                </div>
            </div>

            <!-- Send Request Call-to-Action -->
            <div class="p-3 border-b border-slate-800/80 bg-[#0a1122] flex space-x-2 flex-shrink-0">
                <button id="btnSendRequest" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-medium py-2 px-4 rounded flex items-center justify-center space-x-2 transition shadow-lg shadow-indigo-900/30 cursor-pointer active:scale-[0.99]" type="button">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    <span class="text-xs font-semibold">Kirim Request (Ctrl+Enter)</span>
                </button>
                <button id="btnResetTester" class="px-2.5 py-2 rounded bg-slate-800/80 hover:bg-slate-700 border border-slate-700/80 text-slate-300 transition cursor-pointer" title="Reload / Reset" type="button">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            </div>

            <!-- Ready Status Badge, Latency, Size & Copy Response -->
            <div class="px-3 py-1.5 border-b border-slate-800/60 bg-[#070d18] flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span id="testerResponseStatus" class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300 border border-slate-700">Siap Kirim</span>
                    <span id="testerResponseSize" class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800/80 text-slate-300 border border-slate-700/60 hidden">-- KB</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span id="testerResponseTime" class="text-[10px] font-mono text-slate-500"></span>
                    <button id="btnCopyTesterResponse" class="hidden text-slate-400 hover:text-white flex items-center space-x-1 cursor-pointer transition text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-800 hover:bg-slate-700 border border-slate-700" type="button" title="Salin respons JSON">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        <span>Salin</span>
                    </button>
                </div>
            </div>

            <!-- Response Panel -->
            <div class="h-48 flex flex-col bg-[#050912] flex-shrink-0">
                <div class="px-3 border-b border-slate-800/80 flex space-x-4 text-xs font-medium bg-[#070d18] flex-shrink-0">
                    <button id="btnRespBodyTab" class="py-1.5 text-white border-b-2 border-blue-500 font-semibold text-[11px] cursor-pointer" type="button">Body (JSON)</button>
                    <button id="btnRespHeadersTab" class="py-1.5 text-slate-400 hover:text-slate-200 text-[11px] cursor-pointer" type="button">Headers</button>
                </div>
                <div id="testerResponseBox" class="flex-1 p-3.5 font-mono text-xs overflow-y-auto custom-scrollbar">
                    <p id="testerPlaceholder" class="text-emerald-500/90 tracking-wide text-xs">
                        Klik "Kirim Request" untuk mengeksekusi endpoint ini.
                    </p>
                    <pre id="testerResponseBody" class="hidden text-emerald-300 whitespace-pre-wrap leading-relaxed"></pre>
                    <pre id="testerResponseHeaders" class="hidden text-slate-300 whitespace-pre-wrap leading-relaxed"></pre>
                </div>
            </div>

            <!-- Right Panel Bottom Footer -->
            <div class="p-2 border-t border-slate-800/90 bg-[#060b14] flex items-center space-x-2 text-[10px] text-slate-400 font-sans flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span class="truncate">Credentials &amp; Session otomatis disertakan dalam pengujian API.</span>
            </div>
        </aside>
        <!-- END: LiveApiTester -->

    </div>
    <!-- END: WorkspaceLayout -->

    <!-- Toast Notification Container (Top-Right) -->
    <div id="toastContainer" class="fixed top-14 right-5 z-50 pointer-events-none space-y-2"></div>

    <!-- Data Injection from Laravel -->
    <script>
        const CATEGORIES_DATA = @json($categories);
        const INITIAL_ENDPOINT = @json($initialEndpoint);
        const INITIAL_TESTER = @json($initialTester);
        const BASE_URL = @json($baseUrl);
        const PROXY_URL = "{{ route('api_panel.proxy') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    </script>

    <!-- Client-side Interactive Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // State
            let currentEndpoint = INITIAL_ENDPOINT;
            let currentMethodFilter = 'ALL';
            let currentActiveSnippetLang = 'curl';
            let currentActiveResponseStatus = '200';
            let currentTesterTab = 'params';
            let isTesterVisible = true;

            // In-Memory Global Auto-Captured Parameters (with localStorage sync)
            let AUTO_CAPTURED_PARAMS = {};
            try {
                AUTO_CAPTURED_PARAMS = JSON.parse(localStorage.getItem('siska_auto_captured_params') || '{}');
            } catch (e) {
                AUTO_CAPTURED_PARAMS = {};
            }

            // Cache of request editor contents per tab
            let testerData = {
                params: INITIAL_TESTER.params || '{}',
                body: '{}',
                headers: JSON.stringify({ "Accept": "application/json" }, null, 2),
                cookies: ''
            };

            // DOM Elements
            const leftSidebar = document.getElementById('leftSidebar');
            const rightTesterPanel = document.getElementById('rightTesterPanel');
            const gutterLeft = document.getElementById('gutter-left');
            const gutterRight = document.getElementById('gutter-right');
            const searchInput = document.getElementById('searchInput');
            const routesBadge = document.getElementById('routesBadge');

            // Doc Panel Elements
            const docMethodBadge = document.getElementById('docMethodBadge');
            const docPath = document.getElementById('docPath');
            const docStatusBadge = document.getElementById('docStatusBadge');
            const docBreadcrumb = document.getElementById('docBreadcrumb');
            const docTitle = document.getElementById('docTitle');
            const docDescription = document.getElementById('docDescription');
            const docSanctumBox = document.getElementById('docSanctumBox');
            const docSanctumNote = document.getElementById('docSanctumNote');
            const docHeadersTableBody = document.getElementById('docHeadersTableBody');
            
            // Parameters & Body Schema Elements
            const docParamsSection = document.getElementById('docParamsSection');
            const docParamsHeading = document.getElementById('docParamsHeading');
            const docParamsBadge = document.getElementById('docParamsBadge');
            const docParamsTableContainer = document.getElementById('docParamsTableContainer');
            const docParamsTableBody = document.getElementById('docParamsTableBody');
            const docParamsBlock = document.getElementById('docParamsBlock');

            const docBodySection = document.getElementById('docBodySection');
            const docBodyHeading = document.getElementById('docBodyHeading');
            const docBodyBadge = document.getElementById('docBodyBadge');
            const docBodyTableContainer = document.getElementById('docBodyTableContainer');
            const docBodyTableBody = document.getElementById('docBodyTableBody');
            const docBodyRawBlock = document.getElementById('docBodyRawBlock');

            const docResponseBlock = document.getElementById('docResponseBlock');
            const docSnippetBlock = document.getElementById('docSnippetBlock');
            const snippetLangLabel = document.getElementById('snippetLangLabel');

            // Tester Elements
            const testerMethod = document.getElementById('testerMethod');
            const testerUrl = document.getElementById('testerUrl');
            const testerEditor = document.getElementById('testerEditor');
            const testerEditorLabel = document.getElementById('testerEditorLabel');
            const testerCharsCounter = document.getElementById('testerCharsCounter');
            const testerTabHint = document.getElementById('testerTabHint');
            const tabBodyIndicator = document.getElementById('tabBodyIndicator');
            const autoChainBar = document.getElementById('autoChainBar');
            const autoChainText = document.getElementById('autoChainText');
            const btnClearAutoChain = document.getElementById('btnClearAutoChain');

            const testerResponseStatus = document.getElementById('testerResponseStatus');
            const testerResponseSize = document.getElementById('testerResponseSize');
            const testerResponseTime = document.getElementById('testerResponseTime');
            const btnCopyTesterResponse = document.getElementById('btnCopyTesterResponse');
            const testerPlaceholder = document.getElementById('testerPlaceholder');
            const testerResponseBody = document.getElementById('testerResponseBody');
            const testerResponseHeaders = document.getElementById('testerResponseHeaders');
            const btnRespBodyTab = document.getElementById('btnRespBodyTab');
            const btnRespHeadersTab = document.getElementById('btnRespHeadersTab');

            // Buttons
            const btnToggleTester = document.getElementById('btnToggleTester');
            const btnResetLayout = document.getElementById('btnResetLayout');
            const btnCopyUrl = document.getElementById('btnCopyUrl');
            const btnCopyParams = document.getElementById('btnCopyParams');
            const btnCopyBody = document.getElementById('btnCopyBody');
            const btnCopyResponse = document.getElementById('btnCopyResponse');
            const btnCopySnippet = document.getElementById('btnCopySnippet');
            const btnSendRequest = document.getElementById('btnSendRequest');
            const btnResetTester = document.getElementById('btnResetTester');

            function findEndpointById(id) {
                for (const cat of CATEGORIES_DATA) {
                    for (const ep of cat.endpoints) {
                        if (ep.id === id) return ep;
                    }
                }
                return null;
            }

            function findEndpointByPath(path) {
                if (!path) return null;
                const clean = path.trim().toLowerCase();
                for (const cat of CATEGORIES_DATA) {
                    for (const ep of cat.endpoints) {
                        if (ep.path.toLowerCase() === clean) return ep;
                    }
                }
                for (const cat of CATEGORIES_DATA) {
                    for (const ep of cat.endpoints) {
                        if (ep.path.toLowerCase().includes(clean)) return ep;
                    }
                }
                return null;
            }

            function showToast(message, type = 'info') {
                const container = document.getElementById('toastContainer');
                if (!container) return;
                container.innerHTML = ''; // Hapus alert sebelumnya agar tidak ada alert ganda / menumpuk

                const toast = document.createElement('div');
                toast.className = 'toast-animate px-3.5 py-2 rounded text-xs font-mono shadow-xl border flex items-center space-x-2 pointer-events-auto bg-[#0a1122] border-slate-700 text-slate-200';
                if (type === 'success') {
                    toast.className += ' border-emerald-600/60 text-emerald-300';
                } else if (type === 'error') {
                    toast.className += ' border-rose-600/60 text-rose-300';
                }
                toast.innerHTML = `<svg class="w-3.5 h-3.5 ${type === 'error' ? 'text-rose-400' : 'text-emerald-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>${message}</span>`;
                container.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            }

            function updateCharsCounter() {
                testerCharsCounter.textContent = `Chars: ${testerEditor.value.length}`;
                if (currentTesterTab === 'body') {
                    updateBodyDotIndicator();
                }
            }

            function updateBodyDotIndicator() {
                if (tabBodyIndicator) {
                    const content = testerData.body || testerEditor.value;
                    if (content && content.trim() !== '' && content.trim() !== '{}') {
                        tabBodyIndicator.classList.remove('hidden');
                    } else {
                        tabBodyIndicator.classList.add('hidden');
                    }
                }
            }

            function renderAutoChainUI() {
                if (!autoChainBar || !autoChainText) return;
                const keys = Object.keys(AUTO_CAPTURED_PARAMS);
                if (keys.length > 0) {
                    autoChainBar.classList.remove('hidden');
                    autoChainText.textContent = keys.map(k => `${k}=${String(AUTO_CAPTURED_PARAMS[k]).substring(0, 18)}`).join(', ');
                } else {
                    autoChainBar.classList.add('hidden');
                }
            }

            function inferSource(key, ep, location) {
                const lk = key.toLowerCase();
                // 1. Session / Token
                if (lk === 'token' || lk === 'access_token' || lk === 'authorization' || lk.endsWith('_token')) {
                    return {
                        type: 'session',
                        label: 'Sesi / Token',
                        badgeClass: 'bg-purple-950/80 text-purple-300 border border-purple-800/60',
                        icon: `<svg class="w-3 h-3 text-purple-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>`
                    };
                }
                // 2. From other endpoint
                if (lk === 'nim' || lk === 'nip') {
                    if (ep.path.includes('/login')) {
                        return {
                            type: 'manual',
                            label: 'Input Manual',
                            badgeClass: 'bg-slate-800/90 text-slate-400 border border-slate-700/60',
                            icon: `<svg class="w-3 h-3 text-slate-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>`
                        };
                    }
                    return {
                        type: 'endpoint',
                        label: '/api/auth/mhs/login',
                        sourceEndpoint: '/api/auth/mhs/login',
                        badgeClass: 'bg-blue-950/80 text-blue-300 border border-blue-800/60',
                        icon: `<svg class="w-3 h-3 text-blue-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>`
                    };
                }
                if (lk.includes('krs') || lk === 'kode_krs') {
                    return {
                        type: 'endpoint',
                        label: '/api/mhs/krs',
                        sourceEndpoint: '/api/mhs/krs',
                        badgeClass: 'bg-blue-950/80 text-blue-300 border border-blue-800/60',
                        icon: `<svg class="w-3 h-3 text-blue-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>`
                    };
                }
                if (lk.includes('semester') || lk.includes('tahun_akademik')) {
                    return {
                        type: 'endpoint',
                        label: '/api/mhs/semester',
                        sourceEndpoint: '/api/mhs/semester',
                        badgeClass: 'bg-blue-950/80 text-blue-300 border border-blue-800/60',
                        icon: `<svg class="w-3 h-3 text-blue-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>`
                    };
                }
                if (lk.includes('kurikulum')) {
                    return {
                        type: 'endpoint',
                        label: '/api/mhs/kurikulum',
                        sourceEndpoint: '/api/mhs/kurikulum',
                        badgeClass: 'bg-blue-950/80 text-blue-300 border border-blue-800/60',
                        icon: `<svg class="w-3 h-3 text-blue-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>`
                    };
                }
                if (lk.includes('kelas') || lk === 'id_kelas') {
                    return {
                        type: 'endpoint',
                        label: '/api/staff/akademik/kelas',
                        sourceEndpoint: '/api/staff/akademik/kelas',
                        badgeClass: 'bg-blue-950/80 text-blue-300 border border-blue-800/60',
                        icon: `<svg class="w-3 h-3 text-blue-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>`
                    };
                }
                // 3. Manual Input Default
                return {
                    type: 'manual',
                    label: 'Input Manual',
                    badgeClass: 'bg-slate-800/90 text-slate-400 border border-slate-700/60',
                    icon: `<svg class="w-3 h-3 text-slate-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>`
                };
            }

            function createSchemaRow(key, val, location, isRequired = true) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/30';

                const valType = Array.isArray(val) ? 'array' : typeof val;
                const valStr = typeof val === 'object' ? JSON.stringify(val) : String(val !== undefined && val !== null ? val : '');
                const source = inferSource(key, currentEndpoint, location);

                const locBadgeClass = location === 'body' 
                    ? 'bg-emerald-950/70 text-emerald-400 border border-emerald-800/40' 
                    : (location === 'path' ? 'bg-amber-950/70 text-amber-400 border border-amber-800/40' : 'bg-blue-950/70 text-blue-400 border border-blue-800/40');

                const reqBadgeClass = isRequired 
                    ? 'bg-rose-950/80 text-rose-400 border border-rose-800/50' 
                    : 'bg-slate-800 text-slate-400 border border-slate-700';

                let sourceHtml = '';
                if (source.type === 'endpoint') {
                    sourceHtml = `
                        <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded text-[10px] ${source.badgeClass} font-sans">
                            ${source.icon}
                            <span class="font-mono text-[9px] truncate max-w-[120px]">${source.label}</span>
                            <button type="button" class="btn-play-source text-blue-300 hover:text-white bg-blue-700/60 hover:bg-blue-600 px-1.5 py-0.2 rounded text-[9px] font-semibold transition cursor-pointer" data-ep-path="${source.sourceEndpoint}" title="Buka endpoint ini di Tester">▶ Play</button>
                        </div>
                    `;
                } else {
                    sourceHtml = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] ${source.badgeClass} font-sans">
                            ${source.icon}
                            <span>${source.label}</span>
                        </span>
                    `;
                }

                tr.innerHTML = `
                    <td class="py-2 px-3 font-semibold text-slate-200 font-mono">
                        <div class="flex items-center space-x-1.5">
                            <span>${key}</span>
                            <span class="px-1 py-0.2 rounded text-[9px] font-mono ${locBadgeClass}">(${location})</span>
                        </div>
                    </td>
                    <td class="py-2 px-3 text-slate-400 font-mono">${valType}</td>
                    <td class="py-2 px-3">
                        <span class="px-2 py-0.5 rounded text-[10px] ${reqBadgeClass} font-sans font-medium">${isRequired ? 'Wajib' : 'Opsional'}</span>
                    </td>
                    <td class="py-2 px-3 text-slate-300 font-mono truncate max-w-[200px]" title="${valStr}">${valStr || '<span class="text-slate-500 italic">null</span>'}</td>
                    <td class="py-2 px-3">${sourceHtml}</td>
                `;
                return tr;
            }

            function renderDocumentation(ep) {
                currentEndpoint = ep;

                // Deep linking: sync URL parameter ?id=...
                try {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('id', ep.id);
                    window.history.replaceState({ endpointId: ep.id }, '', newUrl.toString());
                } catch (e) {}

                // Update method badge
                docMethodBadge.textContent = ep.method;
                docPath.textContent = ep.path;
                docStatusBadge.textContent = ep.status || 'AKTIF';
                docBreadcrumb.textContent = ep.breadcrumb || `${ep.category.toUpperCase()} • API GATEWAY`;
                docTitle.textContent = ep.judul;
                docDescription.textContent = ep.description;

                // Sanctum notice
                if (ep.is_sanctum) {
                    docSanctumBox.classList.remove('hidden');
                    docSanctumNote.textContent = ep.sanctum_note || 'Endpoint terlindungi memerlukan session cookie dan CSRF token.';
                } else {
                    docSanctumBox.classList.add('hidden');
                }

                // Headers table (4 columns: Key, Tipe, Status, Keterangan)
                docHeadersTableBody.innerHTML = '';
                const headersList = ep.headers || [
                    { key: 'Accept', type: 'string', status: 'Wajib', keterangan: 'Harus bernilai application/json' }
                ];

                headersList.forEach(h => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-slate-900/30';

                    let statusBadge = h.status || 'Wajib';
                    let statusClass = 'bg-rose-950/80 text-rose-400 border border-rose-800/50';

                    const k = (h.key || '').toLowerCase();
                    if (k === 'x-xsrf-token' || k === 'authorization' || k === 'cookie') {
                        statusBadge = 'Auto';
                        statusClass = 'bg-purple-950/80 text-purple-300 border border-purple-800/50';
                    } else if (statusBadge === 'Custom') {
                        statusClass = 'bg-sky-950/80 text-sky-400 border border-sky-800/50';
                    } else if (statusBadge === 'Opsional') {
                        statusClass = 'bg-slate-800 text-slate-400 border border-slate-700';
                    }

                    row.innerHTML = `
                        <td class="py-2 px-3 font-semibold text-sky-400">${h.key}</td>
                        <td class="py-2 px-3 text-slate-400 font-sans">${h.type}</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 rounded text-[10px] ${statusClass} font-sans font-medium">${statusBadge}</span></td>
                        <td class="py-2 px-3 text-slate-300 font-sans">${h.keterangan}</td>
                    `;
                    docHeadersTableBody.appendChild(row);
                });

                // Request Parameters Section (Query & Path for GET / DELETE)
                let hasQueryParams = false;
                let parsedParams = null;
                if (ep.default_params) {
                    parsedParams = typeof ep.default_params === 'string' ? JSON.parse(ep.default_params) : ep.default_params;
                    if (parsedParams && typeof parsedParams === 'object' && Object.keys(parsedParams).length > 0) {
                        hasQueryParams = true;
                    }
                }

                if (hasQueryParams) {
                    docParamsTableContainer.classList.remove('hidden');
                    docParamsTableBody.innerHTML = '';
                    Object.entries(parsedParams).forEach(([k, v]) => {
                        const row = createSchemaRow(k, v, 'query', true);
                        docParamsTableBody.appendChild(row);
                    });
                    docParamsBlock.textContent = JSON.stringify(parsedParams, null, 2);
                } else {
                    docParamsTableContainer.classList.add('hidden');
                    docParamsBlock.textContent = ep.params_schema || '// Endpoint ini tidak memerlukan parameter URL.';
                }

                // Request Body Section (JSON Payload for POST / PUT / PATCH)
                if (ep.default_body && ep.default_body.trim() !== '' && ep.default_body.trim() !== '{}') {
                    docBodySection.classList.remove('hidden');
                    let parsedBody = null;
                    try {
                        parsedBody = typeof ep.default_body === 'string' ? JSON.parse(ep.default_body) : ep.default_body;
                    } catch (e) {}

                    if (parsedBody && typeof parsedBody === 'object' && !Array.isArray(parsedBody)) {
                        docBodyTableContainer.classList.remove('hidden');
                        docBodyTableBody.innerHTML = '';
                        Object.entries(parsedBody).forEach(([key, val]) => {
                            const row = createSchemaRow(key, val, 'body', true);
                            docBodyTableBody.appendChild(row);
                        });
                        docBodyRawBlock.textContent = JSON.stringify(parsedBody, null, 2);
                    } else {
                        docBodyTableContainer.classList.add('hidden');
                        docBodyRawBlock.textContent = typeof ep.default_body === 'string' ? ep.default_body : JSON.stringify(ep.default_body, null, 2);
                    }
                } else if (ep.method !== 'GET') {
                    docBodySection.classList.remove('hidden');
                    docBodyTableContainer.classList.add('hidden');
                    docBodyRawBlock.textContent = '// Endpoint ini tidak memerlukan request body.';
                } else {
                    docBodySection.classList.add('hidden');
                }

                // Sample Response
                renderResponseSample(currentActiveResponseStatus);

                // Code Snippets
                renderSnippet(currentActiveSnippetLang);

                // Bind Play button clicks inside schema tables
                document.querySelectorAll('.btn-play-source').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const p = btn.getAttribute('data-ep-path');
                        const targetEp = findEndpointByPath(p);
                        if (targetEp) {
                            renderDocumentation(targetEp);
                            syncEndpointToTester(targetEp);
                            showToast(`Memuat endpoint sumber: ${targetEp.path}`, 'info');
                        } else {
                            showToast(`Endpoint sumber tidak ditemukan: ${p}`, 'error');
                        }
                    });
                });

                // Highlight active item in sidebar tree
                document.querySelectorAll('.endpoint-item').forEach(el => {
                    const isActive = el.getAttribute('data-ep-id') === ep.id;
                    const dot = el.querySelector('.active-dot');
                    const text = el.querySelector('.ep-path-text');
                    if (isActive) {
                        el.className = 'endpoint-item flex items-center justify-between px-2 py-1 rounded cursor-pointer transition select-none group bg-blue-900/25 border-l-2 border-blue-500';
                        if (text) text.className = 'ep-path-text text-xs font-mono truncate text-white';
                        if (dot) dot.classList.remove('hidden');
                    } else {
                        el.className = 'endpoint-item flex items-center justify-between px-2 py-1 rounded cursor-pointer transition select-none group hover:bg-slate-800/40';
                        if (text) text.className = 'ep-path-text text-xs font-mono truncate text-slate-400 group-hover:text-slate-200';
                        if (dot) dot.classList.add('hidden');
                    }
                });
            }

            function renderResponseSample(status) {
                currentActiveResponseStatus = status;
                let body = '{}';
                if (currentEndpoint.responses && currentEndpoint.responses[status]) {
                    body = currentEndpoint.responses[status];
                } else if (status === '403') {
                    body = JSON.stringify({ status: false, message: "Aksi tidak diizinkan untuk peran ini (Forbidden 403)." }, null, 2);
                } else if (currentEndpoint.responses && currentEndpoint.responses['200']) {
                    body = currentEndpoint.responses['200'];
                }
                
                try {
                    const parsed = JSON.parse(body);
                    docResponseBlock.textContent = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    docResponseBlock.textContent = body;
                }

                // Text coloring based on HTTP status
                if (status.startsWith('2')) {
                    docResponseBlock.className = 'p-3.5 font-mono text-xs text-emerald-300 leading-relaxed overflow-x-auto';
                } else if (status.startsWith('4')) {
                    docResponseBlock.className = 'p-3.5 font-mono text-xs text-amber-300 leading-relaxed overflow-x-auto';
                } else {
                    docResponseBlock.className = 'p-3.5 font-mono text-xs text-rose-400 leading-relaxed overflow-x-auto';
                }

                document.querySelectorAll('.resp-status-btn').forEach(btn => {
                    const s = btn.getAttribute('data-status');
                    if (s === status) {
                        btn.className = s.startsWith('2') 
                            ? 'resp-status-btn px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold transition cursor-pointer'
                            : 'resp-status-btn px-2 py-0.5 rounded bg-amber-950 text-amber-400 border border-amber-800 font-bold transition cursor-pointer';
                    } else {
                        btn.className = 'resp-status-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer';
                    }
                });
            }

            function renderSnippet(lang) {
                currentActiveSnippetLang = lang;
                const method = currentEndpoint.method;
                const url = currentEndpoint.full_url || `${BASE_URL}${currentEndpoint.path}`;
                const bodyStr = currentEndpoint.default_body ? currentEndpoint.default_body : null;

                let code = '';
                if (lang === 'curl') {
                    snippetLangLabel.textContent = 'cURL Command';
                    code = `curl -X ${method} "${url}" \\\n  -H "Accept: application/json"`;
                    if (bodyStr && method !== 'GET') {
                        code += ` \\\n  -H "Content-Type: application/json" \\\n  -d '${bodyStr.replace(/'/g, "\\'")}'`;
                    }
                } else if (lang === 'js') {
                    snippetLangLabel.textContent = 'JavaScript (Fetch API)';
                    code = `const response = await fetch("${url}", {\n  method: "${method}",\n  headers: {\n    "Accept": "application/json"${bodyStr && method !== 'GET' ? ',\n    "Content-Type": "application/json"' : ''}\n  },\n  credentials: "include"${bodyStr && method !== 'GET' ? ',\n  body: JSON.stringify(' + bodyStr + ')' : ''}\n});\n\nconst data = await response.json();\nconsole.log(data);`;
                } else if (lang === 'php') {
                    snippetLangLabel.textContent = 'PHP (Laravel Http Client)';
                    if (method === 'GET') {
                        code = `use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::withHeaders([\n    'Accept' => 'application/json',\n])->get('${url}');\n\n$data = $response->json();`;
                    } else {
                        code = `use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::withHeaders([\n    'Accept' => 'application/json',\n])->${(method || '').toLowerCase()}('${url}'${bodyStr ? ', ' + bodyStr : ''});\n\n$data = $response->json();`;
                    }
                }

                docSnippetBlock.textContent = code;

                document.querySelectorAll('.snippet-lang-btn').forEach(btn => {
                    const l = btn.getAttribute('data-lang');
                    if (l === lang) {
                        btn.className = 'snippet-lang-btn px-2 py-0.5 rounded bg-blue-600 text-white font-semibold transition cursor-pointer';
                    } else {
                        btn.className = 'snippet-lang-btn px-2 py-0.5 rounded bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800 transition cursor-pointer';
                    }
                });
            }

            function syncEndpointToTester(ep) {
                testerMethod.value = ep.method;
                
                let targetUrl = ep.full_url || (ep.path.startsWith('http') ? ep.path : `${BASE_URL}${ep.path.startsWith('/') ? '' : '/'}${ep.path}`);
                
                // Merge auto-captured params into URL query if GET
                let epParams = ep.default_params ? (typeof ep.default_params === 'object' ? { ...ep.default_params } : JSON.parse(ep.default_params || '{}')) : {};
                let epBody = ep.default_body ? (typeof ep.default_body === 'object' ? { ...ep.default_body } : JSON.parse(ep.default_body || '{}')) : {};

                // Auto-fill from AUTO_CAPTURED_PARAMS
                let appliedCount = 0;
                Object.keys(AUTO_CAPTURED_PARAMS).forEach(k => {
                    if (epParams && k in epParams) {
                        epParams[k] = AUTO_CAPTURED_PARAMS[k];
                        appliedCount++;
                    }
                    if (epBody && k in epBody) {
                        epBody[k] = AUTO_CAPTURED_PARAMS[k];
                        appliedCount++;
                    }
                });

                if (ep.method === 'GET' && epParams && Object.keys(epParams).length > 0) {
                    const searchParams = new URLSearchParams();
                    Object.entries(epParams).forEach(([k, v]) => searchParams.append(k, String(v)));
                    const qs = searchParams.toString();
                    if (!targetUrl.includes('?')) {
                        targetUrl += '?' + qs;
                    }
                }
                
                testerUrl.value = targetUrl;
                testerData.params = Object.keys(epParams).length > 0 ? JSON.stringify(epParams, null, 2) : '{}';
                testerData.body = Object.keys(epBody).length > 0 ? JSON.stringify(epBody, null, 2) : '{}';

                const hdr = { "Accept": "application/json" };
                if (ep.method !== 'GET') {
                    hdr["Content-Type"] = "application/json";
                }
                
                const savedToken = AUTO_CAPTURED_PARAMS.token || localStorage.getItem('siska_tester_token');
                if (savedToken && ep.headers && ep.headers.some(h => (h.key || '').toLowerCase() === 'authorization')) {
                    hdr["Authorization"] = `Bearer ${savedToken}`;
                }
                
                testerData.headers = JSON.stringify(hdr, null, 2);

                if (ep.method === 'GET') {
                    switchTesterTab('params');
                } else {
                    switchTesterTab('body');
                }

                updateBodyDotIndicator();
                renderAutoChainUI();

                if (appliedCount > 0) {
                    showToast(`⚡ ${appliedCount} nilai auto-chained otomatis terisi!`, 'success');
                }

                testerResponseStatus.textContent = 'Siap Kirim';
                testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300 border border-slate-700';
                testerResponseSize.classList.add('hidden');
                btnCopyTesterResponse.classList.add('hidden');
                testerResponseTime.textContent = '';
                updateCharsCounter();
            }

            function switchTesterTab(tab) {
                testerData[currentTesterTab] = testerEditor.value;
                currentTesterTab = tab;

                document.querySelectorAll('.tester-tab-btn').forEach(btn => {
                    const t = btn.getAttribute('data-tab');
                    if (t === tab) {
                        btn.className = 'tester-tab-btn py-2 text-white border-b-2 border-blue-500 font-medium cursor-pointer transition flex items-center space-x-1';
                    } else {
                        btn.className = 'tester-tab-btn py-2 text-slate-400 hover:text-slate-200 cursor-pointer transition flex items-center space-x-1';
                    }
                });

                if (tab === 'params') {
                    testerTabHint.textContent = 'URL • Query & Path Params';
                    testerEditorLabel.textContent = 'Params URL / Query String';
                    testerEditor.value = testerData.params;
                    testerEditor.readOnly = false;
                } else if (tab === 'body') {
                    testerTabHint.textContent = 'JSON Request Payload';
                    testerEditorLabel.textContent = 'Content-Type: application/json';
                    testerEditor.value = testerData.body;
                    testerEditor.readOnly = false;
                } else if (tab === 'headers') {
                    testerTabHint.textContent = 'HTTP Request Headers';
                    testerEditorLabel.textContent = 'Custom Request Headers (JSON)';
                    testerEditor.value = testerData.headers;
                    testerEditor.readOnly = false;
                } else if (tab === 'cookies') {
                    testerTabHint.textContent = 'Active Cookies & CSRF';
                    testerEditorLabel.textContent = 'Browser Cookie Jar & Sanctum';
                    const xsrf = getXsrfToken();
                    const hasSession = document.cookie.includes('laravel_session') || document.cookie.includes('session');
                    const allCookies = document.cookie ? document.cookie.split(';').map(c => '  • ' + c.trim()).join('\n') : '  (Tidak ada cookie terdeteksi)';
                    testerEditor.value = `=== MONITORING AUTENTIKASI BROWSER (SANCTUM SPA) ===\n\n` +
                        `[1] X-XSRF-TOKEN CSRF Token:\n` +
                        (xsrf ? `    Status: ✅ Terpasang\n    Nilai:  ${xsrf.substring(0, 36)}...` : `    Status: ⚠️ Belum ada cookie XSRF-TOKEN (Akan otomatis diambil)`) + `\n\n` +
                        `[2] Laravel Session Cookie:\n` +
                        (hasSession ? `    Status: ✅ Aktif (laravel-session)` : `    Status: ℹ️ Sesi browser`) + `\n\n` +
                        `[3] Document Cookies Aktif:\n${allCookies}\n\n` +
                        `[4] Catatan Sanctum:\n    Stateful Domain: ${window.location.host}\n    Cookie dan CSRF dikirim otomatis pada setiap request same-origin.`;
                    testerEditor.readOnly = true;
                }

                updateCharsCounter();
            }

            function getXsrfToken() {
                const match = document.cookie.match(new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'));
                return match ? decodeURIComponent(match[2]) : null;
            }

            function filterEndpoints() {
                const query = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;

                document.querySelectorAll('.category-block').forEach(catBlock => {
                    let catHasVisible = false;
                    const items = catBlock.querySelectorAll('.endpoint-item');

                    items.forEach(item => {
                        const method = item.getAttribute('data-method');
                        const path = item.getAttribute('data-path').toLowerCase();
                        const cat = item.getAttribute('data-cat').toLowerCase();

                        const methodMatch = (currentMethodFilter === 'ALL' || method === currentMethodFilter);
                        const queryMatch = !query || path.includes(query) || cat.includes(query);

                        if (methodMatch && queryMatch) {
                            item.classList.remove('hidden');
                            catHasVisible = true;
                            visibleCount++;
                        } else {
                            item.classList.add('hidden');
                        }
                    });

                    if (catHasVisible) {
                        catBlock.classList.remove('hidden');
                    } else {
                        catBlock.classList.add('hidden');
                    }
                });

                routesBadge.textContent = `${visibleCount} Routes`;
            }

            async function pingGateway() {
                const pingEl = document.getElementById('gatewayPingLatency');
                const labelEl = document.getElementById('gatewayStatusLabel');
                const dotEl = document.getElementById('gatewayStatusDot');
                const startTime = performance.now();
                try {
                    const res = await fetch('/api/auth/csrf-cookie', { method: 'GET', credentials: 'include' });
                    const elapsed = Math.round(performance.now() - startTime);
                    if (pingEl) pingEl.textContent = `${elapsed}ms`;
                    if (labelEl) {
                        labelEl.textContent = 'ONLINE';
                        labelEl.className = 'text-emerald-400 font-bold text-[10px]';
                    }
                    if (dotEl) dotEl.className = 'w-2 h-2 rounded-full bg-emerald-400 glow-dot animate-pulse';
                } catch (e) {
                    if (pingEl) pingEl.textContent = '--';
                    if (labelEl) {
                        labelEl.textContent = 'OFFLINE';
                        labelEl.className = 'text-rose-400 font-bold text-[10px]';
                    }
                    if (dotEl) dotEl.className = 'w-2 h-2 rounded-full bg-rose-500';
                }
            }

            async function executeRequest() {
                testerData[currentTesterTab] = testerEditor.value;

                const method = testerMethod.value;
                let url = testerUrl.value.trim();

                if (!url) {
                    showToast('URL tidak boleh kosong!', 'error');
                    return;
                }

                let headers = { "Accept": "application/json" };
                try {
                    if (testerData.headers && testerData.headers.trim() !== '') {
                        headers = { ...headers, ...JSON.parse(testerData.headers) };
                    }
                } catch (e) {
                    showToast('Format Headers harus JSON valid!', 'error');
                    return;
                }

                let params = {};
                try {
                    if (testerData.params && testerData.params.trim() !== '' && testerData.params.trim() !== '{}') {
                        params = JSON.parse(testerData.params);
                    }
                } catch (e) {
                    showToast('Format Params harus JSON valid!', 'error');
                    return;
                }

                let body = null;
                try {
                    if (method !== 'GET' && testerData.body && testerData.body.trim() !== '' && testerData.body.trim() !== '{}') {
                        body = JSON.parse(testerData.body);
                        headers["Content-Type"] = "application/json";
                    }
                } catch (e) {
                    showToast('Format Body harus JSON valid!', 'error');
                    return;
                }

                if (method !== 'GET' && !headers['X-XSRF-TOKEN']) {
                    const xsrf = getXsrfToken();
                    if (xsrf) headers['X-XSRF-TOKEN'] = xsrf;
                }

                // Dynamic Path Placeholder replacement: {param}, :param, <param>
                const combinedValues = { ...AUTO_CAPTURED_PARAMS, ...params };
                url = url.replace(/\{([a-zA-Z0-9_]+)\}/g, (match, p) => combinedValues[p] !== undefined ? encodeURIComponent(combinedValues[p]) : match);
                url = url.replace(/:([a-zA-Z0-9_]+)/g, (match, p) => combinedValues[p] !== undefined ? encodeURIComponent(combinedValues[p]) : match);
                url = url.replace(/<([a-zA-Z0-9_]+)>/g, (match, p) => combinedValues[p] !== undefined ? encodeURIComponent(combinedValues[p]) : match);
                testerUrl.value = url;

                // Set Loading State
                btnSendRequest.disabled = true;
                btnSendRequest.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-xs font-semibold">Mengirim Request...</span>
                `;

                testerResponseStatus.textContent = 'Mengirim request...';
                testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-amber-950 text-amber-400 border border-amber-800 animate-pulse';
                testerResponseTime.textContent = '';
                testerResponseSize.classList.add('hidden');
                btnCopyTesterResponse.classList.add('hidden');
                testerPlaceholder.classList.add('hidden');
                testerResponseBody.classList.remove('hidden');
                testerResponseBody.textContent = 'Memuat response...';
                testerResponseHeaders.classList.add('hidden');

                const startTime = performance.now();

                let finalUrl = url;
                if (method === 'GET' && Object.keys(params).length > 0) {
                    try {
                        const urlObj = new URL(finalUrl, window.location.origin);
                        Object.entries(params).forEach(([k, v]) => {
                            urlObj.searchParams.set(k, String(v));
                        });
                        finalUrl = finalUrl.startsWith('http') ? urlObj.toString() : (urlObj.pathname + (urlObj.search ? urlObj.search : ''));
                    } catch (e) {
                        const searchParams = new URLSearchParams();
                        Object.entries(params).forEach(([k, v]) => searchParams.append(k, String(v)));
                        finalUrl += (finalUrl.includes('?') ? '&' : '?') + searchParams.toString();
                    }
                }

                const isSameOrigin = finalUrl.startsWith('/') 
                    || finalUrl.startsWith(window.location.origin)
                    || finalUrl.startsWith('http://127.0.0.1:8000')
                    || finalUrl.startsWith('http://localhost:8000');

                try {
                    let responseData = null;
                    let responseHeaders = {};
                    let status = 200;
                    let statusText = 'OK';
                    let elapsed = 0;
                    let rawContent = '';

                    if (isSameOrigin) {
                        const res = await fetch(finalUrl, {
                            method: method,
                            headers: headers,
                            credentials: 'include',
                            body: (method !== 'GET' && body) ? JSON.stringify(body) : undefined
                        });
                        elapsed = Math.round(performance.now() - startTime);
                        status = res.status;
                        statusText = res.statusText || (res.ok ? 'OK' : 'Error');

                        res.headers.forEach((v, k) => { responseHeaders[k] = v; });

                        const ct = res.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            responseData = await res.json().catch(() => null);
                            rawContent = JSON.stringify(responseData);
                        } else {
                            rawContent = await res.text().catch(() => '');
                            responseData = rawContent;
                        }
                    } else {
                        const proxyRes = await fetch(PROXY_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({
                                method: method,
                                url: url,
                                headers: headers,
                                params: params,
                                body: body
                            })
                        });

                        const proxyData = await proxyRes.json();
                        elapsed = proxyData.time_ms || Math.round(performance.now() - startTime);
                        status = proxyData.status;
                        statusText = proxyData.statusText;
                        responseHeaders = proxyData.headers || {};
                        responseData = proxyData.body || proxyData.error || proxyData.raw_body;
                        rawContent = typeof responseData === 'object' ? JSON.stringify(responseData) : String(responseData || '');
                    }

                    // Format Response Size
                    const sizeBytes = new Blob([rawContent]).size;
                    const sizeStr = sizeBytes < 1024 ? `${sizeBytes} B` : `${(sizeBytes / 1024).toFixed(1)} KB`;
                    testerResponseSize.textContent = sizeStr;
                    testerResponseSize.classList.remove('hidden');
                    btnCopyTesterResponse.classList.remove('hidden');

                    testerResponseTime.textContent = `${elapsed}ms`;
                    if (status >= 200 && status < 300) {
                        testerResponseStatus.textContent = `${status} ${statusText}`;
                        testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold';
                    } else if (status >= 400 && status < 500) {
                        testerResponseStatus.textContent = `${status} ${statusText}`;
                        testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-amber-950 text-amber-400 border border-amber-800 font-bold';
                    } else {
                        testerResponseStatus.textContent = `${status} ${statusText}`;
                        testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-rose-950 text-rose-400 border border-rose-800 font-bold';
                    }

                    if (typeof responseData === 'object' && responseData !== null) {
                        testerResponseBody.textContent = JSON.stringify(responseData, null, 2);
                        testerResponseBody.className = status >= 200 && status < 300 ? 'text-emerald-300 whitespace-pre-wrap leading-relaxed' : 'text-amber-300 whitespace-pre-wrap leading-relaxed';

                        // Auto-Chain Key Capture on 2xx
                        if (status >= 200 && status < 300) {
                            const newChained = [];
                            function extractChained(obj) {
                                if (!obj || typeof obj !== 'object') return;
                                const keysToLook = ['token', 'access_token', 'id', 'nim', 'nip', 'kode_krs', 'kode', 'code', 'semester', 'tahun_akademik', 'prodi_id', 'kelas_id', 'id_kelas'];
                                Object.entries(obj).forEach(([k, v]) => {
                                    if (v && typeof v !== 'object') {
                                        const lk = k.toLowerCase();
                                        if (keysToLook.includes(lk) || lk.startsWith('kode_') || lk.endsWith('_id')) {
                                            AUTO_CAPTURED_PARAMS[k] = v;
                                            newChained.push(`${k}=${v}`);
                                        }
                                    } else if (v && typeof v === 'object' && !Array.isArray(v)) {
                                        extractChained(v);
                                    }
                                });
                            }
                            extractChained(responseData);

                            if (newChained.length > 0) {
                                localStorage.setItem('siska_auto_captured_params', JSON.stringify(AUTO_CAPTURED_PARAMS));
                                renderAutoChainUI();
                            }

                            const tok = responseData.token || responseData.data?.token;
                            if (tok && typeof tok === 'string') {
                                localStorage.setItem('siska_tester_token', tok);
                            }
                        }
                    } else {
                        testerResponseBody.textContent = String(responseData || '(empty response)');
                        testerResponseBody.className = 'text-slate-300 whitespace-pre-wrap leading-relaxed';
                    }

                    testerResponseHeaders.textContent = JSON.stringify(responseHeaders, null, 2);
                    showToast(`Selesai: ${status} ${statusText} (${elapsed}ms, ${sizeStr})`, status < 400 ? 'success' : 'info');

                } catch (err) {
                    const elapsed = Math.round(performance.now() - startTime);
                    testerResponseTime.textContent = `${elapsed}ms`;
                    testerResponseStatus.textContent = 'FETCH ERROR';
                    testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-rose-950 text-rose-400 border border-rose-800 font-bold';
                    testerResponseBody.textContent = `Gagal mengirim request: ${err.message}`;
                    testerResponseBody.className = 'text-rose-400 whitespace-pre-wrap leading-relaxed';
                    showToast('Gagal mengirim request', 'error');
                } finally {
                    btnSendRequest.disabled = false;
                    btnSendRequest.innerHTML = `
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                        <span class="text-xs font-semibold">Kirim Request (Ctrl+Enter)</span>
                    `;
                }
            }

            // Event Listeners
            document.querySelectorAll('.cat-header-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const content = document.getElementById(targetId);
                    if (content) {
                        content.classList.toggle('hidden');
                    }
                });
            });

            document.querySelectorAll('.endpoint-item').forEach(item => {
                item.addEventListener('click', () => {
                    const epId = item.getAttribute('data-ep-id');
                    const ep = findEndpointById(epId);
                    if (ep) {
                        renderDocumentation(ep);
                        syncEndpointToTester(ep);
                    }
                });
            });

            document.querySelectorAll('.method-filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentMethodFilter = btn.getAttribute('data-method');

                    document.querySelectorAll('.method-filter-btn').forEach(b => {
                        const m = b.getAttribute('data-method');
                        if (m === currentMethodFilter) {
                            b.className = 'method-filter-btn flex-1 py-0.5 bg-blue-600 text-white font-semibold rounded text-center transition cursor-pointer';
                        } else {
                            b.className = 'method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-slate-400 rounded text-center transition cursor-pointer';
                        }
                    });

                    filterEndpoints();
                });
            });

            searchInput.addEventListener('input', filterEndpoints);

            window.addEventListener('keydown', (e) => {
                if (e.key === '/' && document.activeElement !== searchInput && document.activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                } else if (e.key === 'Enter' && e.ctrlKey) {
                    e.preventDefault();
                    executeRequest();
                }
            });

            document.querySelectorAll('.resp-status-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    renderResponseSample(btn.getAttribute('data-status'));
                });
            });

            document.querySelectorAll('.snippet-lang-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    renderSnippet(btn.getAttribute('data-lang'));
                });
            });

            document.querySelectorAll('.tester-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    switchTesterTab(btn.getAttribute('data-tab'));
                });
            });

            testerEditor.addEventListener('input', updateCharsCounter);

            btnRespBodyTab.addEventListener('click', () => {
                btnRespBodyTab.className = 'py-1.5 text-white border-b-2 border-blue-500 font-semibold text-[11px] cursor-pointer';
                btnRespHeadersTab.className = 'py-1.5 text-slate-400 hover:text-slate-200 text-[11px] cursor-pointer';
                testerResponseBody.classList.remove('hidden');
                testerResponseHeaders.classList.add('hidden');
            });

            btnRespHeadersTab.addEventListener('click', () => {
                btnRespHeadersTab.className = 'py-1.5 text-white border-b-2 border-blue-500 font-semibold text-[11px] cursor-pointer';
                btnRespBodyTab.className = 'py-1.5 text-slate-400 hover:text-slate-200 text-[11px] cursor-pointer';
                testerResponseHeaders.classList.remove('hidden');
                testerResponseBody.classList.add('hidden');
            });

            btnSendRequest.addEventListener('click', executeRequest);

            btnResetTester.addEventListener('click', () => {
                testerResponseStatus.textContent = 'Siap Kirim';
                testerResponseStatus.className = 'px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300 border border-slate-700';
                testerResponseTime.textContent = '';
                testerPlaceholder.classList.remove('hidden');
                testerResponseBody.classList.add('hidden');
                testerResponseHeaders.classList.add('hidden');
                syncEndpointToTester(currentEndpoint);
                showToast('Tester direset', 'info');
            });

            btnToggleTester.addEventListener('click', () => {
                isTesterVisible = !isTesterVisible;
                if (isTesterVisible) {
                    rightTesterPanel.classList.remove('hidden');
                    btnToggleTester.className = 'flex items-center space-x-1.5 text-xs text-slate-300 hover:text-white bg-slate-800/70 hover:bg-slate-700/80 border border-emerald-600/50 px-2.5 py-1 rounded transition cursor-pointer';
                } else {
                    rightTesterPanel.classList.add('hidden');
                    btnToggleTester.className = 'flex items-center space-x-1.5 text-xs text-slate-400 hover:text-white bg-slate-800/70 hover:bg-slate-700/80 border border-slate-700 px-2.5 py-1 rounded transition cursor-pointer';
                }
            });

            btnResetLayout.addEventListener('click', () => {
                searchInput.value = '';
                currentMethodFilter = 'ALL';
                document.querySelectorAll('.method-filter-btn').forEach(b => {
                    if (b.getAttribute('data-method') === 'ALL') {
                        b.className = 'method-filter-btn flex-1 py-0.5 bg-blue-600 text-white font-semibold rounded text-center transition cursor-pointer';
                    } else {
                        b.className = 'method-filter-btn flex-1 py-0.5 bg-slate-800/80 hover:bg-slate-700/80 text-slate-400 rounded text-center transition cursor-pointer';
                    }
                });

                document.querySelectorAll('.cat-content').forEach(c => c.classList.remove('hidden'));
                filterEndpoints();
                renderDocumentation(INITIAL_ENDPOINT);

                if (!isTesterVisible) {
                    isTesterVisible = true;
                    rightTesterPanel.classList.remove('hidden');
                    btnToggleTester.className = 'flex items-center space-x-1.5 text-xs text-slate-300 hover:text-white bg-slate-800/70 hover:bg-slate-700/80 border border-emerald-600/50 px-2.5 py-1 rounded transition cursor-pointer';
                }

                // Reset panel widths & localStorage
                leftSidebar.style.width = '288px';
                rightTesterPanel.style.width = '500px';
                localStorage.removeItem('siska_panel_left_w');
                localStorage.removeItem('siska_panel_right_w');

                testerMethod.value = INITIAL_TESTER.method;
                testerUrl.value = INITIAL_TESTER.url;
                testerData.params = INITIAL_TESTER.params;
                switchTesterTab('params');

                showToast('Layout & Grid berhasil di-reset ke default', 'info');
            });

            btnCopyUrl.addEventListener('click', () => {
                const fullUrl = currentEndpoint.full_url || `${BASE_URL}${currentEndpoint.path}`;
                navigator.clipboard.writeText(fullUrl).then(() => {
                    showToast('URL endpoint disalin!', 'success');
                });
            });

            btnCopyParams.addEventListener('click', () => {
                navigator.clipboard.writeText(docParamsBlock.textContent).then(() => {
                    showToast('Schema parameter disalin!', 'success');
                });
            });

            if (btnCopyBody) {
                btnCopyBody.addEventListener('click', () => {
                    navigator.clipboard.writeText(docBodyRawBlock.textContent).then(() => {
                        showToast('Body JSON disalin!', 'success');
                    });
                });
            }

            btnCopyResponse.addEventListener('click', () => {
                navigator.clipboard.writeText(docResponseBlock.textContent).then(() => {
                    showToast('Response body disalin!', 'success');
                });
            });

            btnCopySnippet.addEventListener('click', () => {
                navigator.clipboard.writeText(docSnippetBlock.textContent).then(() => {
                    showToast('Code snippet disalin!', 'success');
                });
            });

            if (btnCopyTesterResponse) {
                btnCopyTesterResponse.addEventListener('click', () => {
                    navigator.clipboard.writeText(testerResponseBody.textContent).then(() => {
                        showToast('Respons API disalin!', 'success');
                    });
                });
            }

            if (btnClearAutoChain) {
                btnClearAutoChain.addEventListener('click', () => {
                    AUTO_CAPTURED_PARAMS = {};
                    localStorage.removeItem('siska_auto_captured_params');
                    renderAutoChainUI();
                    showToast('Parameter auto-chain dibersihkan!', 'info');
                });
            }

            // Splitter Resizable Logic
            function initPanelSplitters() {
                if (!leftSidebar || !rightTesterPanel || !gutterLeft || !gutterRight) return;

                // Load saved widths from localStorage
                const savedLeft = localStorage.getItem('siska_panel_left_w');
                const savedRight = localStorage.getItem('siska_panel_right_w');

                if (savedLeft) {
                    const w = parseInt(savedLeft, 10);
                    if (w >= 180 && w <= 480) {
                        leftSidebar.style.width = `${w}px`;
                    }
                }
                if (savedRight) {
                    const w = parseInt(savedRight, 10);
                    if (w >= 320 && w <= 850) {
                        rightTesterPanel.style.width = `${w}px`;
                    }
                }

                // Drag Left Splitter
                let isDraggingLeft = false;
                gutterLeft.addEventListener('pointerdown', (e) => {
                    isDraggingLeft = true;
                    gutterLeft.setPointerCapture(e.pointerId);
                    document.body.classList.add('cursor-col-resize', 'select-none');
                });

                gutterLeft.addEventListener('pointermove', (e) => {
                    if (!isDraggingLeft) return;
                    const newW = Math.min(Math.max(e.clientX, 180), 480);
                    leftSidebar.style.width = `${newW}px`;
                });

                const stopDragLeft = (e) => {
                    if (isDraggingLeft) {
                        isDraggingLeft = false;
                        try { gutterLeft.releasePointerCapture(e.pointerId); } catch (_) {}
                        document.body.classList.remove('cursor-col-resize', 'select-none');
                        const w = parseInt(leftSidebar.style.width, 10);
                        if (w) localStorage.setItem('siska_panel_left_w', String(w));
                    }
                };
                gutterLeft.addEventListener('pointerup', stopDragLeft);
                gutterLeft.addEventListener('pointercancel', stopDragLeft);

                // Double-click resets left sidebar width
                gutterLeft.addEventListener('dblclick', () => {
                    leftSidebar.style.width = '288px';
                    localStorage.removeItem('siska_panel_left_w');
                    showToast('Lebar sidebar di-reset (288px)', 'info');
                });

                // Drag Right Splitter
                let isDraggingRight = false;
                gutterRight.addEventListener('pointerdown', (e) => {
                    isDraggingRight = true;
                    gutterRight.setPointerCapture(e.pointerId);
                    document.body.classList.add('cursor-col-resize', 'select-none');
                });

                gutterRight.addEventListener('pointermove', (e) => {
                    if (!isDraggingRight) return;
                    const containerW = window.innerWidth;
                    const newW = Math.min(Math.max(containerW - e.clientX, 320), 850);
                    rightTesterPanel.style.width = `${newW}px`;
                });

                const stopDragRight = (e) => {
                    if (isDraggingRight) {
                        isDraggingRight = false;
                        try { gutterRight.releasePointerCapture(e.pointerId); } catch (_) {}
                        document.body.classList.remove('cursor-col-resize', 'select-none');
                        const w = parseInt(rightTesterPanel.style.width, 10);
                        if (w) localStorage.setItem('siska_panel_right_w', String(w));
                    }
                };
                gutterRight.addEventListener('pointerup', stopDragRight);
                gutterRight.addEventListener('pointercancel', stopDragRight);

                // Double-click resets right panel width
                gutterRight.addEventListener('dblclick', () => {
                    rightTesterPanel.style.width = '500px';
                    localStorage.removeItem('siska_panel_right_w');
                    showToast('Lebar Live Tester di-reset (500px)', 'info');
                });
            }

            // Initialize panel splitters
            initPanelSplitters();

            // Check URL parameters for Deep Linking (?id=... atau ?path=...)
            const urlParams = new URLSearchParams(window.location.search);
            const targetId = urlParams.get('id');
            const targetPath = urlParams.get('path');
            const targetMethod = urlParams.get('method');

            let initialTarget = INITIAL_ENDPOINT;
            if (targetId) {
                const found = findEndpointById(targetId);
                if (found) initialTarget = found;
            } else if (targetPath) {
                for (const cat of CATEGORIES_DATA) {
                    for (const ep of cat.endpoints) {
                        if (ep.path === targetPath && (!targetMethod || ep.method.toUpperCase() === targetMethod.toUpperCase())) {
                            initialTarget = ep;
                            break;
                        }
                    }
                }
            }

            renderDocumentation(initialTarget);
            syncEndpointToTester(initialTarget);
            filterEndpoints();
            renderAutoChainUI();
            updateBodyDotIndicator();
            pingGateway();
            setInterval(pingGateway, 30000);

            // Auto-expand category accordion containing initialTarget
            const activeNavEl = document.querySelector(`.endpoint-item[data-ep-id="${initialTarget.id}"]`);
            if (activeNavEl) {
                const parentBlock = activeNavEl.closest('.category-block');
                if (parentBlock) {
                    const contentEl = parentBlock.querySelector('.cat-content');
                    if (contentEl) contentEl.classList.remove('hidden');
                }
                activeNavEl.scrollIntoView({ block: 'nearest' });
            }
        });
    </script>
</body>

</html>
