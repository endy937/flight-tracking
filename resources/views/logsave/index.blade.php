@extends('layouts.app')

@section('title', 'Data Log Pesawat')

@section('contents')
    <div style="width: 100%; height: 100%;">
        <div class="mb-3">
            <h2>Table Log Json Pesawat</h2>
        </div>
        <table class="min-w-full border border-gray-200 text-sm text-left">
            <thead class="bg-gray-100 font-bold">
                <tr>
                    <th class="border px-4 py-2 text-center">No</th>
                    <th class="border px-4 py-2">Name log File</th>
                    <th class="border px-4 py-2">Waktu</th>
                    <th class="border px-4 py-2">Data</th>
                    <th class="border px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>
                        <td class="border px-4 py-2">{{ $item->log_id ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->timestamp ?? '-' }}</td>
                        <td class="border px-4 py-2">
                            <div x-data="{ expanded: false }" class="max-w-xs break-words">
                                <span x-show="!expanded">
                                    {{ Str::limit(json_encode($item->data), 50) }}
                                    <button @click="expanded = true" class="text-blue-500 hover:underline ml-1">Lihat
                                        Selengkapnya</button>
                                </span>
                                <span x-show="expanded" x-cloak>
                                    {{ json_encode($item->data, JSON_PRETTY_PRINT) }}
                                    <button @click="expanded = false"
                                        class="text-blue-500 hover:underline ml-1">Sembunyikan</button>
                                </span>
                            </div>
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center items-center gap-5">
                                {{-- Tombol Hapus --}}
                                <a href="{{ route('flightlog_delete', $item->id) }}" data-confirm-delete="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24">
                                        <path fill="#000"
                                            d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7zm4 12H8v-9h2zm6 0h-2v-9h2zm.618-15L15 2H9L7.382 4H3v2h18V4z" />
                                    </svg>
                                </a>

                                {{-- Tombol Lihat di Peta --}}
                                <a href="{{ route('flightlog_show', $item->id) }}"
                                    class="bg-black text-white px-3 py-1 rounded hover:bg-blue-600 inline-flex items-center justify-center">
                                    <i class="fas fa-play"></i>
                                </a>

                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tampilkan pagination -->
        <div class="mt-4">
            {{ $data->links() }}
        </div>


    </div>
@endsection
