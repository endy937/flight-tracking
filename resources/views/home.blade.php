@extends('layouts.user')

@section('title', 'Flight Tracking')

@section('maps')
    <div id="mapContainer" style="position: relative; width: 100%; height: 100%;">

        <!-- MAP -->
        <div id="map" class="map">
            <!-- popup info pesawat -->
            <div class="left-popup-menu" id="leftPopupMenuP">
                <div class="popup-pesawat-header">
                    <div>
                        <button class="close-btn" onclick="toggleLeftPopupMenuP()">&times;</button>
                    </div>
                </div>
                <div class="popup-content-pesawat" id="popupContentP"></div>
                <button class="follow-btn" id="followButton" onclick="followAircraft()">Follow</button>
            </div>
            <div class="right-popup-menu" id="rightPopupMenu">
                <div class="popup-follow-header">
                    <h4>Di-follow</h4>
                    <button class="close-btn" onclick="toggleRightPopupMenu()">&times;</button>
                </div>
                <div class="popup-content-follow" id="popupContentFollow"></div>
                <!-- Tombol reset di popup kanan -->
                <button class="reset-follow-btn" onclick="resetFollowedAircrafts()">Reset</button>

            </div>
        </div>
        <!-- Tombol buka popup kanan -->
        <button class="open-follow-list" onclick="toggleRightPopupMenu()"
            style="position: absolute; top: 10px; right: 10px; z-index: 3000;">Lihat Daftar Follow</button>




    </div>




    <!-- Map mode button -->
    {{-- <div id="mapModeBtn" class="leaflet-bar leaflet-control leaflet-control-custom"
        style="bottom: 50px; left: 10px; position: absolute; z-index: 1000;">
        <img src="{{ asset('home/assets/images/layers.png') }}" style="width: 30px; height: 30px;" alt="Map Mode">
    </div> --}}

    <!-- Map mode menu -->
    <div id="mapModeMenu"
        style="display: none; position: absolute; bottom: 60px; left: 10px; background: rgba(0,0,0,0.8); color: white; padding: 5px; border-radius: 5px; z-index: 1001;">
        <div class="mode-option" data-mode="streets">Streets</div>
        <div class="mode-option" data-mode="satellite">Satellite</div>
        <div class="mode-option" data-mode="dark">Theme</div>
        <div class="mode-option" data-mode="dark2">Dark</div>
        <div class="mode-option" data-mode="topographic">Topographic</div>
        <div class="mode-option" data-mode="hybrid">Hybrid</div>
    </div>

    <!-- Fullscreen button -->
    <div id="fullscreenBtn" class="leaflet-bar leaflet-control leaflet-control-custom"
        style="bottom: 20px; right: 10px; position: absolute; z-index: 1000; cursor: pointer;">
        <img id="fullscreenIcon" src="{{ asset('home/assets/images/full.png') }}" style="width: 30px; height: 30px;"
            alt="Fullscreen">
    </div>

    <!-- Save to DB checkbox -->
    <div>
        <label
            style="position: absolute; top: 20px; right: 70px; z-index: 9999; background: rgba(255,255,255,0.7); padding: 5px 10px; border-radius: 8px;">
            <input type="checkbox" id="toggleSave" />
            Simpan ke Database
        </label>
    </div>

    </div>
@endsection
