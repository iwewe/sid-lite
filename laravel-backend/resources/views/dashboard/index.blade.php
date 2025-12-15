@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1a202c;">Dashboard</h1>
    <p style="color: #718096;">Selamat datang, {{ auth()->user()->name }}!</p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
        <h3 style="font-size: 2.5rem; margin-bottom: 10px;">{{ number_format($stats['total_warga']) }}</h3>
        <p style="opacity: 0.9;">Total Warga</p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #38a169, #2d7a50); color: white;">
        <h3 style="font-size: 2.5rem; margin-bottom: 10px;">{{ number_format($stats['total_modules']) }}</h3>
        <p style="opacity: 0.9;">Modul Aktif</p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #3182ce, #2c5282); color: white;">
        <h3 style="font-size: 2.5rem; margin-bottom: 10px;">{{ number_format($stats['total_responses']) }}</h3>
        <p style="opacity: 0.9;">Total Responses</p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #dd6b20, #c05621); color: white;">
        <h3 style="font-size: 2.5rem; margin-bottom: 10px;">{{ $stats['verification_rate'] }}%</h3>
        <p style="opacity: 0.9;">Tingkat Verifikasi</p>
    </div>
</div>

<!-- Module Statistics -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Statistik per Modul</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Modul</th>
                <th>Total Responses</th>
                <th>Terverifikasi</th>
                <th>Pending</th>
                <th>Tingkat Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($moduleStats as $module)
            <tr>
                <td>
                    <strong>{{ $module['icon'] }} {{ $module['name'] }}</strong>
                </td>
                <td>{{ number_format($module['total_responses']) }}</td>
                <td style="color: #38a169;">{{ number_format($module['verified']) }}</td>
                <td style="color: #dd6b20;">{{ number_format($module['pending']) }}</td>
                <td>
                    <div style="background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="background: #38a169; height: 100%; width: {{ $module['verification_rate'] }}%;"></div>
                    </div>
                    <small style="color: #718096;">{{ $module['verification_rate'] }}%</small>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #718096;">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Recent Responses -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Response Terbaru</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Warga</th>
                <th>Modul</th>
                <th>Status</th>
                <th>Score</th>
                <th>Submitted By</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentResponses as $response)
            <tr>
                <td>
                    <strong>{{ $response['warga_nama'] }}</strong><br>
                    <small style="color: #718096;">NIK: {{ $response['warga_nik'] }}</small>
                </td>
                <td>{{ $response['module_name'] }}</td>
                <td>
                    @if($response['is_verified'])
                        <span style="background: #f0fff4; color: #38a169; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            ✓ Terverifikasi
                        </span>
                    @else
                        <span style="background: #fffaf0; color: #dd6b20; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            ⚠ Pending
                        </span>
                    @endif
                </td>
                <td>{{ $response['score'] }} / {{ $response['min_verified'] }}</td>
                <td>{{ $response['submitted_by'] }}</td>
                <td>{{ $response['submitted_at'] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #718096;">Belum ada response</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Wilayah Statistics -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Statistik per Wilayah</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Dusun</th>
                <th>Jumlah Warga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($wilayahStats as $wilayah)
            <tr>
                <td><strong>{{ $wilayah->dusun ?: 'Tidak ada dusun' }}</strong></td>
                <td>{{ number_format($wilayah->total) }} warga</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #718096;">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
