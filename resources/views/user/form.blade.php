@extends('layouts.app')

@section('title', 'User Management')

@section('contents')
    <div style="width: 100%; height: 100%;">
        <div class="header">
            <h2>tambah user</h2>
        </div>
        <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md space-y-4">
            <form action="{{ route('user_store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-1 font-medium">Name</label>
                    <input type="text" name="name" placeholder="Enter name"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required value="{{ old('name', $data->name ?? '') }}">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Email</label>
                    <input type="email" name="email" placeholder="Enter email"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required value="{{ old('email', $data->email ?? '') }}">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Password</label>
                    <input type="password" name="password" placeholder="Enter password"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Role</label>
                    <select name="type"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled @selected(old('type', $data->type ?? '') === '')>Choose role</option>
                        <option value="1" @selected(old('type', $data->type ?? '') == '1')>Admin</option>
                        <option value="0" @selected(old('type', $data->type ?? '') == '0')>User</option>
                    </select>
                </div>


                <div class="flex justify-between items-center">
                    <input type="hidden" name="id" value="{{ old('id', $data->id ?? '') }}">
                    <a href="{{ route('user_index') }}" class="bg-yellow-400 px-4 py-2 rounded-lg text-gray-600">Kembali</a>
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
                </div>

            </form>
        </div>

    </div>
@endsection
