<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title')</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body class="min-h-screen">

    <header class="px-4 py-2 shadow bg-black opacity-90">
        <div class="flex justify-between">
            <div class="flex items-center">
                <button data-menu class="p-4 -ml-3 focus:outline-none text-white" type="button">
                    <svg class="fill-current w-5" viewBox="0 -21 384 384">
                        <path
                            d="M362.668 0H21.332C9.578 0 0 9.578 0 21.332V64c0 11.754 9.578 21.332 21.332 21.332h341.336C374.422 85.332 384 75.754 384 64V21.332C384 9.578 374.422 0 362.668 0zm0 0M362.668 128H21.332C9.578 128 0 137.578 0 149.332V192c0 11.754 9.578 21.332 21.332 21.332h341.336c11.754 0 21.332-9.578 21.332-21.332v-42.668c0-11.754-9.578-21.332-21.332-21.332zm0 0M362.668 256H21.332C9.578 256 0 265.578 0 277.332V320c0 11.754 9.578 21.332 21.332 21.332h341.336c11.754 0 21.332-9.578 21.332-21.332v-42.668c0-11.754-9.578-21.332-21.332-21.332zm0 0" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center text-white">
                <button class="flex items-center px-3 py-2 focus:outline-none" type="button">
                    <span class="ml-4 text-sm hidden md:inline-block text-white">
                        {{ Auth::user()->name ?? 'Guest' }}
                    </span>
                    <!-- Icon user -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a5 5 0 100 10 5 5 0 000-10zM2 18a8 8 0 0116 0H2z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

        </div>
    </header>

    <div class="flex flex-row min-h-screen h-screen">
        <!-- Sidebar -->
        <div id="sidebar"
            class="flex flex-col w-64 h-full bg-black opacity-90 border-r dark:bg-gray-900 dark:border-gray-700">
            <div class="sidebar text-center bg-black opacity-90">
                <a href="{{ route('admin/home') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-house-door-fill"></i>
                        <span class="menu-text text-[15px] ml-4 text-gray-200 font-bold">Dashboard</span>
                    </div>
                </a>

                <a href="{{ route('user_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="menu-text text-[15px] ml-4 text-gray-200 font-bold">User Management</span>
                    </div>
                </a>

                <a href="{{ route('follow_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="menu-text text-[15px] ml-4 text-gray-200 font-bold">Follow Aircraft</span>
                    </div>
                </a>

                <a href="{{ route('logsave_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="menu-text text-[15px] ml-4 text-gray-200 font-bold">Log JSON</span>
                    </div>
                </a>

                <a href="{{ route('logout') }}">
                    <div class="my-4 bg-gray-600 h-[1px]"></div>
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="menu-text text-[15px] ml-4 text-gray-200 font-bold">Logout</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Konten utama -->
        <div class="flex flex-col w-full px-4 py-8 overflow-auto">
            <div>@yield('contents')</div>
        </div>
    </div>

    <footer class="bg-black  opacity-90 text-white dark:bg-gray-900 dark:border-gray-700 py-4 mt-auto">
        <div class="text-center">
            <p class="text-sm">&copy; 2025 Flight Tracking Admin. All rights reserved.</p>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('home/assets/js/admin.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('sweetalert::alert')
</body>

</html>
