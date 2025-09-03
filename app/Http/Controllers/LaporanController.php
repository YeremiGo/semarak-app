<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Dokumentasi;
use App\Models\Jawaban;
use App\Models\Laporan;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Menampilkan page 1 : memilih tipe laporan
    public function create(Aset $aset) {
        return view('laporan.create', compact('aset'));
    }

    // Memproses page 1 dan Menampilkan page 2
    public function next(Request $request) {
        // Validasi input page 1
        $request->validate([
            'aset_id' => 'required|exists:asets,id_aset',
            'nama_teknisi' => 'required|string|max:255',
            'tipe_laporan' => 'required|in:pemeliharaan,tindak_lanjut',
        ]);

        $aset = Aset::find($request->aset_id);
        $pertanyaans = null;

        if ($request->tipe_laporan == 'pemeliharaan') {
            $pertanyaans = Pertanyaan::where('kategori_id', $aset->kategori_id)
            ->whereIn('jenis_pertanyaan', ['pemeliharaan', 'tugas'])
            ->orderBy('urutan', 'asc')
            ->get();
        } elseif ($request->tipe_laporan == 'tindak_lanjut') {
            $pertanyaans = Pertanyaan::where('kategori_id', $aset->kategori_id)
            ->where('jenis_pertanyaan', 'tindak_lanjut')
            ->orderBy('urutan', 'asc')
            ->get();
        }

        // Menampilkan page 2
        return view('laporan.next', [
            'aset' => $aset,
            'nama_teknisi' => $request->nama_teknisi,
            'tipe_laporan' => $request->tipe_laporan,
            'pertanyaans' => $pertanyaans,
        ]);
    }

    // Simpan data page 2 ke database
    public function store(Request $request) {
        // Validasi data umum
        $request->validate([
            'aset_id' => 'required|exists:asets,id_aset',
            'nama_teknisi' => 'required|string|max:255',
            'tipe_laporan' => 'required|in:pemeliharaan,tindak_lanjut',
        ]);

        $laporan = Laporan::create([
            'aset_id' => $request->aset_id,
            'nama_teknisi' => $request->nama_teknisi,
            'tipe_laporan' => $request->tipe_laporan,
            'status' => 'Belum Proses',
        ]);

        if($request->has('jawaban')) {
            foreach($request->jawaban as $pertanyaan_id => $jawaban_text) {
                if (!empty($jawaban_text)) {
                    Jawaban::create([
                        'laporan_id' => $laporan->id_laporan,
                        'pertanyaan_id' => $pertanyaan_id,
                        'jawaban' => $jawaban_text,
                    ]);
                }
            }
        }

        if($request->hasFile('dokumentasi')) {
            foreach($request->file('dokumentasi') as $file) {
                $path = $file->store('laporan_dokumentasi', 'public');
                Dokumentasi::create([
                    'laporan_id' => $laporan->id_laporan,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect('/')->with('success', 'Laporan berhasil disimpan!');
    }

    public function show(Laporan $laporan) {
        $laporan->load('aset', 'jawabans.pertanyaan', 'dokumentasis');

        return view('laporan.show', compact('laporan'));
    }

    // Menampilkan halaman formulir
    public function export() {
        return view('laporan.export');
    }

    public function download(Request $request) {
        $query = Laporan::with(['aset', 'aset.kategori']);

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('tipe_laporan')) {
            $query->where('tipe_laporan', $request->tipe_laporan);
        }

        $laporans = $query->get();

        $fileName = 'laporan_' . date('Y-m-d_H-i-s');

        Excel::create($fileName, function($excel) use ($laporans) {

            $excel->sheet('Laporan', function($sheet) use ($laporans) {

                // Menyiapkan semua data dalam format array
                $dataForExcel = [];
                
                // Menambahkan baris header
                $dataForExcel[] = [
                    'ID Laporan', 'Nama Aset', 'Lokasi', 'Kategori',
                    'Nama Teknisi', 'Tipe Laporan', 'Status', 'Tanggal Laporan'
                ];

                // Menambahkan baris data untuk setiap laporan
                foreach ($laporans as $laporan) {
                    $dataForExcel[] = [
                        $laporan->id_laporan,
                        $laporan->aset->nama_aset,
                        $laporan->aset->lokasi,
                        $laporan->aset->kategori->nama_kategori ?? 'N/A',
                        $laporan->nama_teknisi,
                        ucfirst(str_replace('_', ' ', $laporan->tipe_laporan)),
                        $laporan->status,
                        $laporan->created_at->format('Y-m-d H:i:s'),
                    ];
                }

                // Memasukkan semua data array ke dalam sheet Excel
                $sheet->fromArray($dataForExcel, null, 'A1', false, false);
            });

        })->download('xlsx');
    }
}
