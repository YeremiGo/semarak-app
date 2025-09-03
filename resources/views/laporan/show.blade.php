@extends('layouts.app')

@section('content')

<div class="bg-white rounded-lg shadow-md p-3">
    
    <div class="flex items-center justify-between pb-3 border-b">
        <h1 class="text-xl font-semibold text-gray-800">
            {{ $laporan->tipe_laporan == 'pemeliharaan' ? 'Laporan Pemeliharaan' : 'Laporan Tindak Lanjut' }}
        </h1>
        <div class="flex items-center space-x-2">
            <button class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                Hapus
            </button>
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">
                Kembali
            </a>
        </div>
    </div>

    <div class="w-full h-1.5 {{ $laporan->tipe_laporan == 'pemeliharaan' ? 'bg-green-500' : 'bg-red-500' }} rounded-b-md"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mt-4 text-base">
        <div>
            <label class="block text-gray-500 mb-1">Tanggal Laporan</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->created_at->format('d F Y') }}</p>
        </div>

        @if($laporan->tipe_laporan == 'tindak_lanjut')
        <div>
            <label class="block text-gray-500 mb-1">Status</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->status }}</p>
        </div>
        @endif

        <div>
            <label class="block text-gray-500 mb-1">Nama Aset</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->aset->nama_aset }}</p>
        </div>
        <div>
            <label class="block text-gray-500 mb-1">Nama Teknisi</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->nama_teknisi }}</p>
        </div>
        <div>
            <label class="block text-gray-500 mb-1">Lokasi</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->aset->lokasi }}</p>
        </div>
        <div>
            <label class="block text-gray-500 mb-1">Kategori</label>
            <p class="font-semibold text-gray-800 px-3 py-2 border border-gray-300 rounded-md">{{ $laporan->aset->kategori->nama_kategori ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-6">
        @php
            // Memisahkan pertanyaan "Catatan" dari pertanyaan checklist lainnya
            $pertanyaanCatatan = $laporan->jawabans->first(function ($jawaban) {
                return Str::contains(strtolower($jawaban->pertanyaan->pertanyaan), 'catatan');
            });
            $pertanyaanChecklist = $laporan->jawabans->reject(function ($jawaban) {
                return Str::contains(strtolower($jawaban->pertanyaan->pertanyaan), 'catatan');
            });
        @endphp

        <h3 class="text-base font-semibold text-gray-800 mb-2">
            {{ $laporan->tipe_laporan == 'pemeliharaan' ? 'Pertanyaan Pemeliharaan' : 'Pertanyaan Tindak Lanjut' }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm border-t pt-4">
            @forelse($pertanyaanChecklist as $jawaban)
                <div class="flex justify-between">
                    <span class="text-gray-700">{{ $jawaban->pertanyaan->pertanyaan }}</span>
                    <span class="font-semibold text-gray-800">{{ $jawaban->jawaban }}</span>
                </div>
            @empty
                <p class="text-gray-500 col-span-2">Tidak ada data checklist.</p>
            @endforelse
        </div>

        @if($pertanyaanCatatan)
            <div class="mt-6">
                 <h3 class="text-base font-semibold text-gray-800 mb-2">{{ $pertanyaanCatatan->pertanyaan->pertanyaan }}</h3>
                 <div class="w-full p-3 bg-gray-50 border rounded-md text-sm text-gray-700">
                    {{ $pertanyaanCatatan->jawaban }}
                 </div>
            </div>
        @endif
    </div>

    @if($laporan->dokumentasis->count() > 0)
        <div class="mt-6">
            <h3 class="text-base font-bold text-gray-800 mb-2">Dokumentasi</h3>
            <div class="border rounded-md p-3 space-y-3">
                @foreach($laporan->dokumentasis as $doc)
                    <div class="flex items-center justify-between text-sm bg-gray-50 p-2 rounded-md">
                        {{-- Nama File (Link untuk melihat) --}}
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 truncate">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="truncate">{{ $doc->file_name }}</span>
                        </a>

                        {{-- Tombol Download --}}
                        <a href="{{ asset('storage/' . $doc->file_path) }}" 
                           download="{{ $doc->file_name }}"
                           class="ml-4 flex-shrink-0 px-3 py-1 bg-blue-600 text-white font-semibold text-xs rounded-md hover:bg-blue-700 transition">
                           Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection