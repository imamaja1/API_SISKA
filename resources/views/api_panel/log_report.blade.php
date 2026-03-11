@extends('layouts.app')

@section('title', 'API Log Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    .method-badge {
        display: inline-block;
        padding: .18em .52em;
        border-radius: 4px;
        font-size: .72rem;
        font-weight: 700;
        color: #fff;
    }

    .status-badge {
        display: inline-block;
        padding: .18em .5em;
        border-radius: 4px;
        font-size: .72rem;
        font-weight: 600;
        color: #fff;
    }

    .body-btn {
        cursor: pointer;
        font-family: 'Fira Code', monospace;
        font-size: .73rem;
        color: #495057;
        background: #f4f5f7;
        border: 1px solid #e2e4e8;
        border-radius: 4px;
        padding: .15em .45em;
        max-width: 220px;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
        text-decoration: none;
    }

    .body-btn:hover {
        background: #e9ecef;
        color: #212529;
    }

    /* Summary cards */
    .stat-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: .55rem 1rem;
        min-width: 80px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    .stat-card .sv {
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-card .sl {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
    }

    /* Filter card */
    .filter-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: .9rem 1.2rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    /* DataTable overrides */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: .3rem .6rem;
        font-size: .82rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        font-size: .82rem;
    }

    table.dataTable thead th {
        background: #1a1d21 !important;
        color: #b0b8c4 !important;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: none !important;
        white-space: nowrap;
    }

    table.dataTable tbody tr:hover td {
        background: #f9fafb !important;
    }

    table.dataTable tbody td {
        font-size: .8rem;
        vertical-align: middle;
    }

    .dt-buttons .btn {
        font-size: .78rem;
        border-radius: 6px;
        background: #fff !important;
        color: #495057 !important;
        border-color: #dee2e6 !important;
    }

    .dt-buttons .btn:hover {
        background: #f8f9fa !important;
        color: #212529 !important;
        border-color: #adb5bd !important;
    }

    /* Page layout */
    .log-page-wrapper {
        padding: 2rem 0 3rem;
        margin: 0 5%;
    }

    .log-page-title {
        font-size: 1.3rem;
    }

    /* Filter */
    .filter-label {
        font-size: .77rem;
        font-weight: 600;
    }

    .filter-select {
        min-width: 130px;
    }

    /* Empty state */
    .empty-state-icon {
        opacity: .3;
        margin-bottom: .75rem;
    }

    /* Table cells */
    .td-num {
        font-size: .75rem;
    }

    .td-timestamp {
        white-space: nowrap;
        font-family: 'Fira Code', monospace;
        font-size: .73rem;
        color: #868e96;
    }

    .td-path {
        max-width: 280px;
        font-size: .79rem;
    }

    .td-ip {
        font-family: 'Fira Code', monospace;
        font-size: .73rem;
        white-space: nowrap;
    }

    .td-user {
        font-size: .79rem;
    }

    /* Modal */
    .modal-content-rounded {
        border-radius: 10px;
    }

    .modal-header-dark {
        background: #1a1d21;
        color: #c8cdd3;
        border-radius: 10px 10px 0 0;
    }

    .modal-pre-body {
        font-size: .8rem;
        max-height: 400px;
        overflow: auto;
        border-radius: 0 0 10px 10px;
    }
</style>
@endpush

@section('content')
@php
$total = count($entries);
$methods = collect($entries)->groupBy('method')->map->count();
$statusGroups = collect($entries)->groupBy(fn ($e) => (int) floor(($e['status'] ?? 0) / 100) * 100)->map->count();
$methodColors = [
'POST' => '#198754',
'PUT' => '#fd7e14',
'PATCH' => '#d39e00',
'DELETE' => '#dc3545',
];
@endphp

<div class="log-page-wrapper">

    {{-- Page header --}}
    <div class="d-flex align-items-start justify-content-between mb-4">
        <div>
            <h2 class="mb-0 fw-bold log-page-title">API Write-Log Report</h2>
            <small class="text-muted">Mencatat POST / PUT / PATCH / DELETE &mdash; diakumulasi per bulan</small>
        </div>
        <a href="{{ route('api_panel.log_report.data', ['source' => $source, 'date' => $date]) }}" target="_blank"
            class="btn btn-sm btn-outline-secondary">
            &darr; Raw JSON
        </a>
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('api_panel.log_report') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1 filter-label">Sumber API</label>
                <select name="source" class="form-select form-select-sm filter-select">
                    @foreach ($sources as $s)
                    <option value="{{ $s }}" @selected($s===$source)>{{ strtoupper($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-1 filter-label">Bulan</label>
                <input type="month" name="date" class="form-control form-control-sm" value="{{ $date }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-dark px-3">Tampilkan</button>
            </div>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <div class="stat-card">
            <div class="sv">{{ $total }}</div>
            <div class="sl">Total</div>
        </div>

        @foreach ($methods as $m => $cnt)
        @php $mc = $methodColors[$m] ?? '#6c757d'; @endphp
        <div class="stat-card" style="border-top: 3px solid {{ $mc }}">
            <div class="sv" style="color: {{ $mc }}">{{ $cnt }}</div>
            <div class="sl">{{ $m }}</div>
        </div>
        @endforeach

        @foreach ($statusGroups as $grp => $cnt)
        @php
        $sc = match (true) {
        $grp >= 500 => '#dc3545',
        $grp >= 400 => '#fd7e14',
        $grp >= 300 => '#0dcaf0',
        default => '#198754',
        };
        @endphp
        <div class="stat-card" style="border-top: 3px solid {{ $sc }}">
            <div class="sv" style="color: {{ $sc }}">{{ $cnt }}</div>
            <div class="sl">{{ $grp }}x</div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    @if ($total === 0)
    <div class="text-center py-5 text-muted">
        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="empty-state-icon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 17v-2m3 2v-4m3 4v-6M4 6h16M4 10h16M4 14h10" />
        </svg>
        <p class="mb-0">
            Tidak ada data log untuk
            <strong>{{ strtoupper($source) }}</strong> pada bulan <strong>{{ $date }}</strong>.
        </p>
    </div>
    @else
    <div class="table-responsive">
        <table id="log-table" class="table table-sm table-bordered table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Method</th>
                    <th>Path</th>
                    <th>Status</th>
                    <th>IP</th>
                    <th>User</th>
                    <th>Body</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $i => $e)
                @php
                $statusCode = (int) ($e['status'] ?? 0);
                $rowClass = match (true) {
                $statusCode >= 500 => 'table-danger',
                $statusCode >= 400 => 'table-warning',
                default => '',
                };
                $mc = $methodColors[$e['method'] ?? ''] ?? '#6c757d';
                $sc = match (true) {
                $statusCode >= 500 => '#dc3545',
                $statusCode >= 400 => '#fd7e14',
                default => '#198754',
                };
                $bodyJson = ! empty($e['body'])
                ? json_encode($e['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;
                $modalId = 'bm-' . $i;
                $tsShort = isset($e['timestamp'])
                ? \Carbon\Carbon::parse($e['timestamp'])->format('d/m H:i:s')
                : '-';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-muted td-num">{{ $total - $i }}</td>
                    <td class="td-timestamp" title="{{ $e['timestamp'] ?? '' }}">
                        {{ $tsShort }}
                    </td>
                    <td>
                        <span class="method-badge" style="background: {{ $mc }}">
                            {{ $e['method'] ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span title="{{ $e['url'] ?? '' }}" class="d-inline-block text-truncate td-path">
                            /{{ ltrim($e['path'] ?? $e['url'] ?? '-', '/') }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge" style="background: {{ $sc }}">{{ $statusCode }}</span>
                    </td>
                    <td class="td-ip">{{ $e['ip'] ?? '-' }}</td>
                    <td class="text-center td-user">{{ $e['user_id'] ?? '&mdash;' }}</td>
                    <td>
                        @if ($bodyJson)
                        <a class="body-btn" href="#" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                            {{ $bodyJson }}
                        </a>

                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content modal-content-rounded">
                                    <div class="modal-header modal-header-dark">
                                        <h6 class="modal-title mb-0">
                                            <span class="method-badge me-2" style="background: {{ $mc }}">
                                                {{ $e['method'] ?? '' }}
                                            </span>
                                            /{{ ltrim($e['path'] ?? '', '/') }}
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <pre
                                            class="m-0 p-3 bg-light modal-pre-body">{{ json_encode($e['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function () {
            @if ($total > 0)
                $('#log-table').DataTable({
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    order: [], // pertahankan urutan server (terbaru di atas)
                    dom:
                        "<'row align-items-center mb-2'<'col-auto'l><'col-auto'B><'col ms-auto'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row mt-2 align-items-center'<'col-auto'i><'col ms-auto'p>>",
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '&darr; Excel',
                            className: 'btn btn-sm btn-outline-success',
                            title: 'Log-{{ $source }}-{{ $date }}',
                        },
                        {
                            extend: 'csvHtml5',
                            text: '&darr; CSV',
                            className: 'btn btn-sm btn-outline-secondary',
                            title: 'Log-{{ $source }}-{{ $date }}',
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            className: 'btn btn-sm btn-outline-secondary',
                        },
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Cari...',
                        lengthMenu: 'Tampilkan _MENU_ baris',
                        info: 'Menampilkan _START_&ndash;_END_ dari _TOTAL_ entri',
                        infoEmpty: 'Tidak ada data',
                        paginate: { previous: '&lsaquo;', next: '&rsaquo;' },
                    },
                    columnDefs: [
                        { orderable: false, targets: [2, 7] }, // Method & Body tidak bisa di-sort
                    ],
                });
            @endif
        });
</script>
@endpush