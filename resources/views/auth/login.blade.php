<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="min-h-screen bg-black bg-opacity-50 bg-cover bg-center flex items-center justify-center"
        style="background-image: url('{{ asset('home/assets/images/bg-lg.png') }}');
        background-color: rgba(0, 0, 0, 0.7);
        background-blend-mode: darken;
        background-size: cover;
        background-position: center;">
        {{-- <div
            class="w-full max-w-sm bg-black bg-opacity-10 backdrop-blur-md rounded-lg shadow-lg p-8 border border-gray-500"> --}}
        <div
            class="w-full max-w-xs bg-white bg-opacity-5 backdrop-blur-lg rounded-xl shadow-xl p-6 border border-white/20">
            <h1 class="text-3xl font-bold text-white mb-2 text-center">Sign In</h1>
            <p class="text-gray-300 text-center mb-5 text-sm">For your protection, please verify your identity.</p>
            <form class="space-y-4" method="post" action="{{ route('login.action') }}">
                @csrf
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><span class="block sm:inline">{{ $error }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-300">Email Address *</label>
                    <input type="email" name="email" id="email"
                        class="bg-transparent border border-gray-400 text-gray-200 text-sm rounded-md focus:ring-green-400 focus:border-green-400 block w-full p-2.5"
                        required>
                </div>
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-300">Password *</label>
                    <input type="password" name="password" id="password"
                        class="bg-transparent border border-gray-400 text-gray-200 text-sm rounded-md focus:ring-green-400 focus:border-green-400 block w-full p-2.5"
                        required>
                </div>
                <button type="submit"
                    class="w-full mt-2 border border-green-400 text-green-400 px-3 py-2 rounded-md font-semibold hover:bg-green-400 hover:text-white transition">Sign
                    In</button>
            </form>
        </div>
    </div>
</body>

</html>
