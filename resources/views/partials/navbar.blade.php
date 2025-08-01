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

    <!-- Waktu & User -->
    <div class="d-flex align-items-center position-absolute end-0 me-3" style="gap: 10px;">
        <!-- Tampilkan waktu zona WIB -->
        <span id="current-time" class="text-white small" style="margin-top: -5px;"></span>

        @if (Route::has('login'))
            @auth
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#userModal">
                    <img src="{{ asset('home/assets/images/user.png') }}" alt="Icon"
                        style="width: 30px; height: 30px; margin-right:-15px;margin-top:-10px;">
                </button>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light ms-2">Log in</a>
            @endauth
        @endif
    </div>
</nav>
