@extends('layouts.app')

@section('content')

    <div class="flex items-center justify-between mb-2">
        <form action="{{ route('aset.index') }}" method="GET" class="mb-4">
            <div class="relative w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari..." 
                    value="{{ $search ?? '' }}" 
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </form>
        @can('manage-aset')
            <a href="{{ route('aset.create') }}" class="flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </a>
        @endcan
    </div>

    <div class="overflow-x-auto bg-white p-5 rounded-lg shadow-md">
        <table class="w-full text-base text-left">
            <thead class="text-base text-gray-800 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Kode Aset</th>
                    <th scope="col" class="px-6 py-3">Nama Aset</th>
                    <th scope="col" class="px-6 py-3">Kategori</th>
                    <th scope="col" class="px-6 py-3">Lokasi</th>
                    @can ('manage-aset')
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse ($asets as $aset)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $aset->kode_aset }}</td>
                    <td class="px-6 py-4">{{ $aset->nama_aset }}</td>
                    <td class="px-6 py-4">{{ $aset->kategori->nama_kategori }}</td>
                    <td class="px-6 py-4">{{ $aset->lokasi }}</td>
                    @can ('manage-aset')
                        <td class="px-6 py-4 flex space-x-2">
                            <a href="{{ route('aset.edit', $aset) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </a>
                            <form action="{{ route('aset.destroy', $aset) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus aset ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $asets->links() }}
    </div>
@endsection