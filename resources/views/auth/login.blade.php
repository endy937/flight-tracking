<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen flex items-center justify-center">

    <div class="min-h-screen w-full bg-black bg-opacity-60 flex items-center justify-center bg-cover bg-center"
        style="
        background-image: url('{{ asset('home/assets/images/bg-lg.png') }}');
        background-color: rgba(0, 0, 0, 0.7);
        background-blend-mode: darken;
        ">

        <div class="w-full max-w-sm bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl p-8">

            <h1 class="text-3xl font-extrabold text-green-400 mb-2 text-center tracking-wide">
                Sign In
            </h1>
            <p class="text-gray-300 text-center mb-6 text-sm">
                For your protection, please verify your identity.
            </p>

            <form class="space-y-5" method="post" action="{{ route('login.action') }}">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md">
                        <strong class="font-bold">Error!</strong>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-200">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email" required
                        class="bg-transparent border border-gray-400 text-gray-100 text-sm rounded-md focus:ring-green-400 focus:border-green-400 block w-full p-2.5 placeholder-gray-500"
                        placeholder="your@email.com">
                </div>

                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-200">
                        Password
                    </label>
                    <input type="password" name="password" id="password" required
                        class="bg-transparent border border-gray-400 text-gray-100 text-sm rounded-md focus:ring-green-400 focus:border-green-400 block w-full p-2.5 placeholder-gray-500"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full mt-2 border border-green-400 text-green-400 px-3 py-2 rounded-md font-semibold transition duration-200 hover:bg-green-400 hover:text-black shadow-md">
                    Sign In
                </button>

            </form>
        </div>
    </div>

</body>

</html>
