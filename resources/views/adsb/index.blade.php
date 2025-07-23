@extends('layouts.app')

@section('title', 'AirCraft Data')

@section('contents')
    <div style="width: 100%; height: 100%;">
        <div class="mb-3">
            <h2>Table Aircrat</h2>
        </div>
        <table class="min-w-full border border-gray-200 text-sm text-left">
            <thead class="bg-gray-100 font-bold">
                <tr>
                    <th class="border px-4 py-2 text-center">No</th>
                    <th class="border px-4 py-2">Callsign</th>
                    <th class="border px-4 py-2">Lattitude</th>
                    <th class="border px-4 py-2">Longitude</th>
                    <th class="border px-4 py-2">Altitude</th>
                    <th class="border px-4 py-2">Speed</th>
                    <th class="border px-4 py-2">Heading</th>
                    <th class="border px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>
                        <td class="border px-4 py-2">{{ $item->callsign ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->lat ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->lon ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->altitude ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->speed ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->heading ?? '-' }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center items-center gap-5">
                                <a href="{{ route('adsb_delete', $item->id) }}" data-confirm-delete="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24">
                                        <path fill="#000"
                                            d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7zm4 12H8v-9h2zm6 0h-2v-9h2zm.618-15L15 2H9L7.382 4H3v2h18V4z" />
                                    </svg>
                                </a>
                            </div>
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
