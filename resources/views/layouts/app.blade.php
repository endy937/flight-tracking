<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title')</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet" />
    {{-- <link href="{{ asset('home/assets/css/admin.css') }}" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">


</head>

<body>
    <header class="px-4 py-2 shadow bg-black opacity-90">
        <div class="flex justify-between">
            <div class="flex items-center">
                <button data-menu class="p-4 -ml-3 focus:outline-none text-white" type="button">
                    <svg class="fill-current w-5" viewBox="0 -21 384 384">
                        <path
                            d="M362.668 0H21.332C9.578 0 0 9.578 0 21.332V64c0 11.754 9.578 21.332 21.332 21.332h341.336C374.422 85.332 384 75.754 384 64V21.332C384 9.578 374.422 0 362.668 0zm0 0M362.668 128H21.332C9.578 128 0 137.578 0 149.332V192c0 11.754 9.578 21.332 21.332 21.332h341.336c11.754 0 21.332-9.578 21.332-21.332v-42.668c0-11.754-9.578-21.332-21.332-21.332zm0 0M362.668 256H21.332C9.578 256 0 265.578 0 277.332V320c0 11.754 9.578 21.332 21.332 21.332h341.336c11.754 0 21.332-9.578 21.332-21.332v-42.668c0-11.754-9.578-21.332-21.332-21.332zm0 0" />
                    </svg>
                </button>

                <button data-search class="p-4 md:hidden focus:outline-none" type="button">
                    <svg data-search-icon class="fill-current w-4" viewBox="0 0 512 512"
                        style="top: 0.7rem; left: 1rem;">
                        <path
                            d="M225.474 0C101.151 0 0 101.151 0 225.474c0 124.33 101.151 225.474 225.474 225.474 124.33 0 225.474-101.144 225.474-225.474C450.948 101.151 349.804 0 225.474 0zm0 409.323c-101.373 0-183.848-82.475-183.848-183.848S124.101 41.626 225.474 41.626s183.848 82.475 183.848 183.848-82.475 183.849-183.848 183.849z" />
                        <path
                            d="M505.902 476.472L386.574 357.144c-8.131-8.131-21.299-8.131-29.43 0-8.131 8.124-8.131 21.306 0 29.43l119.328 119.328A20.74 20.74 0 00491.187 512a20.754 20.754 0 0014.715-6.098c8.131-8.124 8.131-21.306 0-29.43z" />
                    </svg>
                </button>

                <div data-search-form class="relative mr-3 hidden md:inline-block">
                    <div class="text-gray-500">
                        <svg data-search-icon class="absolute fill-current w-4" viewBox="0 0 512 512"
                            style="top: 0.7rem; left: 1rem;">
                            <path
                                d="M225.474 0C101.151 0 0 101.151 0 225.474c0 124.33 101.151 225.474 225.474 225.474 124.33 0 225.474-101.144 225.474-225.474C450.948 101.151 349.804 0 225.474 0zm0 409.323c-101.373 0-183.848-82.475-183.848-183.848S124.101 41.626 225.474 41.626s183.848 82.475 183.848 183.848-82.475 183.849-183.848 183.849z" />
                            <path
                                d="M505.902 476.472L386.574 357.144c-8.131-8.131-21.299-8.131-29.43 0-8.131 8.124-8.131 21.306 0 29.43l119.328 119.328A20.74 20.74 0 00491.187 512a20.754 20.754 0 0014.715-6.098c8.131-8.124 8.131-21.306 0-29.43z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search" name="search" id="search"
                        class="h-auto pl-10 py-2 bg-gray-200 text-sm border border-gray-500 rounded-full focus:outline-none focus:bg-white">
                </div>
            </div>

            <div class="flex items-center text-white">
                <button data-dropdown class="flex items-center px-3 py-2 focus:outline-none" type="button"
                    x-data="{ open: false }" @click="open = true" :class="{ 'bg-gray-200 rounded-md': open }">
                    {{-- <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=100&h=100&q=80"
                        alt="Profle" class="h-8 w-8 rounded-full"> --}}

                    <span class="ml-4 text-sm hidden md:inline-block text-white">
                        {{ Auth::user()->name ?? 'Guest' }}
                    </span>
                    <svg class="fill-current w-3 ml-4" viewBox="0 0 407.437 407.437">
                        <path
                            d="M386.258 91.567l-182.54 181.945L21.179 91.567 0 112.815 203.718 315.87l203.719-203.055z" />
                    </svg>

                    <div data-dropdown-items
                        class="text-sm text-left absolute top-0 right-0 mt-16 mr-4 bg-black opacity-90 rounded border border-gray-400 shadow"
                        x-show="open" @click.away="open = false">
                        <ul>
                            <li class="px-4 py-3 border-b hover:bg-gray-200"><a href="#">My Profile</a></li>
                            <li class="px-4 py-3 border-b hover:bg-gray-200"><a href="#">Settings</a></li>
                            <li class="px-4 py-3 hover:bg-gray-200"><a href="{{ route('logout') }}">Log out</a></li>
                        </ul>
                    </div>
                </button>
            </div>
        </div>
    </header>

    <div class="flex flex-row ">
        <div
            class="flex flex-col w-64 h-screen overflow-y-auto bg-black opacity-90 border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">
            <div class="sidebar text-center bg-black opacity-90">
                <div class="text-gray-100 text-xl">
                    <div class="p-2.5 mt-1 flex items-center">
                        <i class="bi bi-app-indicator px-2 py-1 rounded-md  bg-green-400"></i>
                        <h1 class="font-bold text-gray-200 text-[15px] ml-3">Admin</h1>
                    </div>
                    <div class="my-2 bg-gray-600 h-[1px]"></div>
                </div>
                {{-- <div class="p-2.5 flex items-center rounded-md px-4 duration-300 cursor-pointer bg-gray-700 text-white">
                    <i class="bi bi-search text-sm"></i>
                    <input type="text" placeholder="Search"
                        class="text-[15px] ml-4 w-full bg-transparent focus:outline-none" />
                </div> --}}

                <a href="{{ route('admin/home') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-house-door-fill"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">Dashboard</span>
                    </div>
                </a>

                <a href="{{ route('user_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">User Management</span>
                    </div>
                </a>
                <a href="{{ route('adsb_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">Aircrast Data</span>
                    </div>
                </a>
                <a href="{{ route('follow_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">Follow Aircraft</span>
                    </div>
                </a>
                <a href="{{ route('logsave_index') }}">
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-bookmark-fill"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">log Json</span>
                    </div>
                </a>
                <a href="{{ route('logout') }}">
                    <div class="my-4 bg-gray-600 h-[1px]"></div>
                    <div
                        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-green-400 text-white">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">Logout</span>
                    </div>
                </a>
            </div>
        </div>


        <div class="flex flex-col w-full px-4 py-8">
            <div>@yield('contents')</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('home/assets/js/admin.js') }}"></script>
    <script src="{{ asset('home/assets/js/chart.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script>
        window.followedChartData = @json($chartData);
    </script> --}}
    @include('sweetalert::alert')


</body>

</html>
