@extends('layouts.app')

@section('title', 'User Management')

@section('contents')
    <div style="width: 100%; height: 100%;">
        <div class="header">
            <h2>table user</h2>
        </div>
        <div class="justify-between mb-5">
            <a href="{{ route('user_create') }}"
                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 m-5 ">Tambah</a>
        </div>
        <table class="min-w-full border border-gray-200 text-sm text-left">
            <thead class="bg-gray-100 font-bold">
                <tr>
                    <th class="border px-4 py-2">Username</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Role</th>
                    <th class="border px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr class="hover:bg-gray-50 ">
                        <td class="border px-4 py-2">{{ $item->name ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->email ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item->type }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center items-center gap-5">
                                <a href="{{ route('user_edit', $item->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24">
                                        <path fill="#000"
                                            d="m4 15.76l-1 4A1 1 0 0 0 3.75 21a1 1 0 0 0 .49 0l4-1a1 1 0 0 0 .47-.26L17 11.41l1.29 1.3l1.42-1.42l-1.3-1.29L21 7.41a2 2 0 0 0 0-2.82L19.41 3a2 2 0 0 0-2.82 0L14 5.59l-1.3-1.3l-1.42 1.42L12.58 7l-8.29 8.29a1 1 0 0 0-.29.47m1.87.75L14 8.42L15.58 10l-8.09 8.1l-2.12.53z" />
                                    </svg>
                                </a>
                                <a href="{{ route('user_delete', $item->id) }}" data-confirm-delete="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
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
    </div>
@endsection
