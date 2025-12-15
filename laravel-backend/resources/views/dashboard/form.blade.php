@extends('layouts.app')

@section('title', 'Form Pendataan')

@section('content')
<div style="margin-bottom: 20px;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1a202c;">Form Pendataan Warga</h1>
    <p style="color: #718096;">Isi data verifikasi sarana prasarana rumah tinggal</p>
</div>

<div class="card">
    <iframe
        src="/mockup.html"
        style="width: 100%; height: 1000px; border: none; border-radius: 12px;"
        title="Form Pendataan"
    ></iframe>
</div>

<div class="card" style="background: #ebf8ff; border: 2px solid #3182ce;">
    <h3 style="color: #2c5282; margin-bottom: 15px;">📝 Cara Menggunakan Form</h3>
    <ol style="color: #2c5282; line-height: 2;">
        <li>Cari warga menggunakan NIK atau nama di kolom pencarian</li>
        <li>Pilih warga dari hasil pencarian</li>
        <li>Pilih tab modul (Jamban Septic, RTLH, atau PAH)</li>
        <li>Isi semua pertanyaan wajib pada modul yang dipilih</li>
        <li>Klik "💾 Simpan Draft" untuk menyimpan sementara di browser</li>
        <li>Klik "✓ Simpan ke Database" untuk menyimpan permanen</li>
    </ol>
</div>

@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        iframe {
            height: 1200px !important;
        }
    }
</style>
@endpush
