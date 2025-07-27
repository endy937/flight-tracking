@extends('layouts.user')

@section('title', 'Flight Tracking')

@section('maps')
    <div id="mapContainer" style="position: relative; width: 100%; height: 100%;">

        <!-- MAP -->
        <div id="map" class="map">
            <!-- popup info pesawat -->
            <div class="left-popup-menu" id="leftPopupMenuP">
                <div class="popup-pesawat-header">
                    <h4>Aircraft Informasion</h4>
                    <button class="close-btn" onclick="toggleLeftPopupMenuP()">&times;</button>
                </div>
                <div class="popup-content-pesawat" id="popupContentP"></div>
                <div class="popup-pesawat-footer">
                    <button class="follow-btn" id="followButton" onclick="followAircraft()">Follow</button>
                </div>
            </div>

            {{-- pop-up follow --}}
            <div class="right-popup-menu" id="rightPopupMenu" style="display: none;">
                <div class="popup-follow-header">
                    <h4>Follow Aircraft</h4>

                    <button class="close-btn" onclick="toggleRightPopupMenu()">&times;</button>
                </div>
                <div class="popup-content-follow" id="popupContentFollow">
                </div>
                <template id="followTemplate">
                    <div class="follow-item">
                        <div><strong>Callsign:</strong> <span class="follow-callsign"></span></div>
                        <div><strong>Registration:</strong> <span class="follow-registration"></span></div>
                        <div><strong>ICAO 24-bit:</strong> <span class="follow-icao"></span></div>
                        <div><strong>Latitude:</strong> <span class="follow-lat"></span></div>
                        <div><strong>Longitude:</strong> <span class="follow-lng"></span></div>
                    </div>
                </template>


                <button class="save-follow" id="saveFollowedBtn" style="margin-top: 10px;">Simpan Following</button>
                <button class="reset-follow-btn" onclick="resetFollowedAircrafts()">Reset</button>

            </div>
            <!-- geolocation -->
            <div id="custom-locate-btn" title="Lokasi Saya">
                <img src="{{ asset('home/assets/images/locate.png') }}" style="width: 70px; height: 70px;"
                    alt="geolocation">
            </div>

            <!-- Toolbar Custom di Bawah Tengah -->
            <div id="custom-toolbar">
                <!-- Polygon -->
                <div class="draw-btn" title="Draw Polygon" id="btn-draw-polygon">
                    <!-- SVG Polygon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M5 3L3 7l4 4 4-1 3 3-1 4 4 4 4-2 1-4-4-4-4 1-3-3 1-4-4-4z" />
                    </svg>
                </div>

                <!-- Polyline -->
                <div class="draw-btn" title="Draw Polyline" id="btn-draw-polyline">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M4 17l5-5 6 6 5-5" stroke="white" stroke-width="2" fill="none" />
                        <circle cx="4" cy="17" r="1.5" />
                        <circle cx="9" cy="12" r="1.5" />
                        <circle cx="15" cy="18" r="1.5" />
                        <circle cx="20" cy="13" r="1.5" />
                    </svg>
                </div>

                <!-- Rectangle -->
                <div class="draw-btn" title="Draw Rectangle" id="btn-draw-rectangle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <rect x="4" y="6" width="16" height="12" stroke="white" stroke-width="2" fill="none" />
                    </svg>
                </div>

                <!-- Circle -->
                <div class="draw-btn" title="Draw Circle" id="btn-draw-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2" fill="none" />
                    </svg>
                </div>

                <!-- Marker -->
                <div class="draw-btn" title="Draw Marker" id="btn-draw-marker">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" />
                    </svg>
                </div>

                <!-- Edit -->
                <div class="draw-btn" title="Edit Shapes" id="btn-edit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.41l-2.34-2.34a1.003 1.003 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                    </svg>
                </div>

                <!-- Delete -->
                <div class="draw-btn" title="Delete Shapes" id="btn-delete">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M6 7h12v2H6zm2 3h8v10H8zm5-9v2h5v2H6V3h5V1h2z" />
                    </svg>
                </div>

                <!-- Download -->
                <div class="draw-btn" title="Download GeoJSON" id="custom-download-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M5 20h14a1 1 0 0 0 1-1V7.41a1 1 0 0 0-.29-.7l-2.42-2.42A1 1 0 0 0 17 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1zm7-3a1 1 0 0 1-1-1v-4H9l3-3 3 3h-2v4a1 1 0 0 1-1 1z" />
                    </svg>
                </div>
            </div>


        </div>
    </div>

    <!-- Map mode button -->
    <div id="mapModeBtn" class="" style="bottom: 50px; left: 10px; position: absolute; z-index: 1000;">
        <img src="{{ asset('home/assets/images/layers.png') }}" style="width: 30px; height: 30px;" alt="Map Mode">
    </div>

    <!-- Map mode menu -->
    <div id="mapModeMenu"
        style="display: none; position: absolute; bottom: 60px; left: 10px; background: rgba(0,0,0,0.8); color: white; padding: 5px; border-radius: 5px; z-index: 1001; cursor: pointer;">
        <div class="mode-option" data-mode="streets">Streets</div>
        <div class="mode-option" data-mode="satellite">Satellite</div>
        <div class="mode-option" data-mode="dark">Theme</div>
        <div class="mode-option" data-mode="dark2">Dark</div>
        <div class="mode-option" data-mode="topographic">Topographic</div>
        <div class="mode-option" data-mode="hybrid">Hybrid</div>
    </div>
    {{-- <div>
        <label
            style="position: absolute; top: 20px; right: 70px; z-index: 9999; background: rgba(255,255,255,0.7); padding: 5px 10px; border-radius: 8px;">
            <input type="checkbox" id="toggleSave" />
            Simpan ke Database
        </label>
    </div> --}}
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content"
                style="background: transparent; border: solid 1px; color: white; border-color: gray; border-radius: 4px;">

                <div class="modal-header justify-content-center flex-column text-center" style="border: none;">
                    <!-- ICON USER -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="white"
                        viewBox="0 0 24 24" class="mb-2">
                        <path
                            d="M12 2a5 5 0 0 1 5 5a5 5 0 0 1-5 5a5 5 0 0 1-5-5a5 5 0 0 1 5-5zm0 12c3.33 0 10 1.67 10 5v1H2v-1c0-3.33 6.67-5 10-5z" />
                    </svg>

                    <!-- TEKS KOMANDAN -->
                    <p class="mb-0">Komandan <strong>{{ Auth::user()->name ?? 'User' }}</strong></p>
                </div>

                <div class="modal-body text-center mt-0">
                    <div class="d-grid gap-2">
                        <a href="{{ url('/logout') }}" class="btn btn-danger btn-sm border border-gray">Sign out</a>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
