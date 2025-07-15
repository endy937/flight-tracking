<nav class="navbar navbar-expand-lg fixed-top navbar-transparent">
    <div class="container position-relative">

        <!-- Logo di tengah -->
        <a class="navbar-brand mx-auto" href="#">
            <img src="{{ asset('home/assets/images/logorti.png') }}" alt="Logo" height="48">
        </a>
    </div>

    <!-- Form search di luar container -->
    <div class="search-container">
        <form id="search-form">
            <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari lokasi...">
        </form>
    </div>

    <div class="d-flex align-items-center position-absolute end-0 me-3">
        @if (Route::has('login'))
            @auth
                <div class="dropdown">
                    <button class="btn" data-bs-toggle="dropdown">
                        <img src="{{ asset('home/assets/images/info.png') }}" alt="Icon"
                            style="width: 30px; height: 30px;">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item">Your Profile</a></li>
                        <li><a href="{{ url('/logout') }}" class="dropdown-item">Sign out</a></li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light ms-2">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-outline-light ms-2">Register</a>
                @endif
            @endauth
        @endif
    </div>
</nav>
