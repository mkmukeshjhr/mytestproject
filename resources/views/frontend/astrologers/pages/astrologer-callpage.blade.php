@extends('frontend.astrologers.layout.master')

<!-- <link rel="stylesheet" href="{{ asset('public/frontend/agora/index.css') }}"> -->
<style>
    /* Same CSS as user page */
    @media only screen and (max-width: 767px) {
        #local-player div:first-child,
        #remote-playerlist div:first-child {
            min-height: 0px !important;
            position: unset !important;
        }
    }
    .dIzgYQV4CBbzZxzJbwbS{
        display: none !important;
    }

    .eLS4omBUBKIdRuH3vIbv{
        display: none!important;
    }
    .QeMJj1LEulq1ApqLHxuM{
        display: none!important;
    }

    .video-call-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        height: 500px;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
    }

    .video-participant {
        position: relative;
        background: #2a2a2a;
        border-radius: 8px;
        overflow: hidden;
    }

    .player, video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .name-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
    }

    .video-action-button {
        margin: 5px;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background: #007bff;
        color: white;
        cursor: pointer;
    }

    .video-action-button.muted,
    .video-action-button.off {
        background-color: #f44336 !important;
    }

    .video-action-button.endcall {
        background: #dc3545;
    }

    .navigation {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
    }

    #remainingTime {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #dc3545;
    }

    /* Provider containers */
    .agora-container, .hms-container, .zegocloud-container {
        display: none;
    }

    /* Zegocloud UIKit Container */
    #zegocloudUIKitContainer {
        width: 100%;
        height: 500px;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Loading states */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 9999;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .avatar-fallback {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }

    .btn-kundali {
        background: linear-gradient(45deg, #FFD700, #FFA500);
        color: #000;
        border: none;
        font-weight: bold;
    }
</style>

@section('content')
@php
use Symfony\Component\HttpFoundation\Session\Session;
@endphp
@if (astroauthcheck())
@php
$session = new Session();
$token = $session->get('astrotoken');
$userId = $callrequest->userId;
$astrologerId = astroauthcheck()['astrologerId'];
$callId = request()->query('callId');
$call_type = request()->query('call_type');
@endphp
@endif

<div class="pt-1 pb-1 bg-red d-none d-md-block astroway-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize;">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.astrologerindex') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Call</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Kundali Report Modal -->
<div class="modal fade" id="kundaliModal" tabindex="-1" role="dialog" aria-labelledby="kundaliModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kundaliModalLabel">Kundali Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="kundaliContent">
                <!-- Content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input id="appid" type="hidden" value="{{ $agoraAppIdValue->value }}">
<input id="token" type="hidden" value="{{ $callrequest->token }}">
<input id="channel" type="hidden" value="{{ $callrequest->channelName }}">
<input id="callMethod" type="hidden" value="{{ $callrequest->call_method }}">
<input id="astrologerId" type="hidden" value="{{ $astrologerId }}">
<input id="callType" type="hidden" value="{{ $call_type }}">

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p id="loadingText">Initializing call...</p>
</div>

<section class="container">
    <div class="row">
        <div class="col-md-2 col-sm-12 order-md-0 order-2 bottom-sm-0 bottom-buttons">
            <div class="navigation flex-sm-column h-100">
                <span id="remainingTime" class="color-red">{{ $callrequest->call_duration }}</span>
                
                <!-- Agora Controls -->
                <div id="agoraControls" class="agora-container">
                    <button class="video-action-button mic" onclick="toggleMic()" id="mic-icon">
                        <i class="fas fa-microphone"></i>
                    </button>
                    @if($call_type==11)
                    <button class="video-action-button camera" onclick="toggleVideo()" id="video-icon">
                        <i class="fas fa-video"></i>
                    </button>
                    @endif
                    <button class="video-action-button endcall" onclick="endCall()" id="leave">Leave</button>
                </div>

                <!-- Zegocloud UIKit Controls -->
                <div id="zegocloudControls" class="zegocloud-container" style="display: none;">
                    <!-- Zegocloud UIKit provides its own controls -->
                    <button class="video-action-button endcall" onclick="endCall()">Leave Call</button>
                </div>

                <button type="button" class="btn btn-kundali mb-2" id="kundaliButton">
                    <i class="fa-solid fa-file"></i> Kundali
                </button>
            </div>
        </div>

        <div class="app-main col-md-9 col-sm-12 order-sm-0">
            <!-- Agora Video Container -->
            <div class="video-call-wrapper shadow agora-container" id="agoraContainer">
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="local-player-name">{{ astroauthcheck()['name'] }}</a>
                    <div id="local-player" class="player"></div>
                    <div class="avatar-fallback" id="local-avatar">
                        {{ substr(astroauthcheck()['name'], 0, 1) }}
                    </div>
                </div>
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="remote-player-name">User</a>
                    <div id="remote-playerlist"></div>
                    <div class="avatar-fallback" id="remote-avatar">
                        U
                    </div>
                </div>
            </div>

            <!-- Zegocloud UIKit Container -->
            <div class="shadow zegocloud-container" id="zegocloudContainer" style="display: none;">
                <div id="zegocloudUIKitContainer"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>

<script>
    // Global variables for astrologer
    let currentProvider = "{{ $callrequest->call_method }}";
    let callDuration = {{ $callrequest->call_duration }};
    let remainingTime = callDuration;
    let timerInterval;
    let callEnded = false;
    
    // Agora variables
    let agoraClient = null;
    let localAudioTrack = null;
    let localVideoTrack = null;
    
    // Zegocloud UIKit variables
    let zegoUIKit = null;
    let zegoJoined = false;

    $(document).ready(function() {
        console.log('Initializing astrologer call with provider:', currentProvider);
        initializeCall();
        startTimer();
        setupKundaliButton();
    });

    function initializeCall() {
        showLoading('Initializing call...');
        
        if (currentProvider === 'agora') {
            initializeAgora();
        } else if (currentProvider === 'zegocloud') {
            initializeZegocloudUIKit();
        } else {
            // Default to Agora
            currentProvider = 'agora';
            initializeAgora();
        }
    }

    
    // ========== ZEGOCLOUD UIKIT IMPLEMENTATION ==========
    async function initializeZegocloudUIKit() {
        try {
            showProviderUI('zegocloud');
            showLoading('Connecting to Zegocloud...');

            // Zegocloud configuration from environment
            const appID = "{{ systemflag('zegoAppId') }}";
            const serverSecret = "{{ systemflag('zegoServerSecret') }}";
            const userID = "{{ $astrologerId }}";
            const userName = "{{astroauthcheck()['name']}}";
            const roomID = document.getElementById('channel').value;
            const isVideoCall = "{{ $call_type }}" == "11";

            console.log('Zegocloud UIKit Config:', { 
                appID, 
                userID, 
                userName, 
                roomID,
                isVideoCall 
            });

            // Validate configuration
            if (!appID) {
                throw new Error('Zegocloud App ID is missing');
            }
            if (!serverSecret) {
                throw new Error('Zegocloud Server Secret is missing or invalid');
            }
            if (!roomID) {
                throw new Error('Room ID is missing');
            }

            // Generate kit token
            const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
                parseInt(appID),
                serverSecret,
                roomID,
                userID,
                userName
            );

            // Create ZegoUIKit instance
            zegoUIKit = ZegoUIKitPrebuilt.create(kitToken);

            // Configuration for the call
            const config = {
                container: document.querySelector("#zegocloudUIKitContainer"),
                scenario: {
                    mode: isVideoCall ? ZegoUIKitPrebuilt.VideoCall : ZegoUIKitPrebuilt.VoiceCall,
                },
                showPreJoinView: false,
                turnOnCameraWhenJoining: isVideoCall,
                turnOnMicrophoneWhenJoining: true,
                useFrontFacingCamera: true,
                showMyCameraToggleButton: isVideoCall,
                showMyMicrophoneToggleButton: true,
                showAudioVideoSettingsButton: true,
                showTextChat: false,
                showUserList: false,
                showRoomTimer: true,
                maxUsers: 2,
                layout: "Auto",
                showLayoutButton: false,
                showScreenSharingButton: false,
                videoResolutionDefault: ZegoUIKitPrebuilt.VideoResolution_360P,
                
                onJoinRoom: () => {
                    console.log('✅ Zegocloud UIKit: Astrologer joined room successfully');
                    zegoJoined = true;
                    hideLoading();
                    
                    // Start call timer
                    callStartTime = Date.now();
                    console.log('Astrologer call started at:', new Date(callStartTime));
                },
                
                onLeaveRoom: () => {
                    console.log('Zegocloud UIKit: Astrologer left room');
                    if (zegoJoined) {
                        endCall();
                    }
                },
                
                onUserJoin: (users) => {
                    console.log('User joined:', users);
                    users.forEach(user => {
                        console.log('Remote user joined:', user.userName);
                    });
                },
                
                onUserLeave: (users) => {
                    console.log('User left:', users);
                    users.forEach(user => {
                        console.log('Remote user left:', user.userName);
                    });
                    
                    // If the user leaves, end the call after a delay
                    setTimeout(() => {
                        if (zegoJoined) {
                            console.log('User left, ending call...');
                            endCall();
                        }
                    }, 1000);
                },

                onError: (error) => {
                    console.error('Zegocloud UIKit Error:', error);
                    showError('Zegocloud error: ' + error.message);
                }
            };

            // Join the room
            console.log('Astrologer joining Zegocloud room...');
            zegoUIKit.joinRoom(config);

        } catch (error) {
            console.error('Zegocloud UIKit initialization failed:', error);
            showError('Failed to initialize Zegocloud: ' + (error.message || 'Unknown error'));
            hideLoading();
        }
    }

    function leaveZegocloudCall() {
        if (zegoUIKit && zegoJoined) {
            zegoUIKit.leaveRoom();
            endCall();
        }
    }

    // ========== COMMON FUNCTIONS ==========
    function showProviderUI(provider) {
        // Hide all containers first
        document.querySelectorAll('.agora-container, .hms-container, .zegocloud-container').forEach(el => {
            el.style.display = 'none';
        });

        // Show selected provider
        if (provider === 'zegocloud') {
            document.getElementById('zegocloudContainer').style.display = 'block';
            document.getElementById('zegocloudControls').style.display = 'block';
        } else {
            document.getElementById(provider + 'Container').style.display = 'grid';
            document.getElementById(provider + 'Controls').style.display = 'block';
        }
    }

    function showLoading(message) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
            if (loadingText && message) {
                loadingText.textContent = message;
            }
        }
    }

    function hideLoading() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }

    function showError(message) {
        console.error('Error:', message);
        alert('Error: ' + message);
    }

    function startTimer(updateTime) {
                setupFirebaseListener();
                // var currentTime = new Date().getTime();
                // var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                // var remainingTime = callDuration - elapsedTime;
                let currentTime = remainingTime = elapsedTime='';
                $.get("{{ route('front.getDateTime') }}", function(response) {
                        // Assuming the response contains the server time in 'Y-m-d H:i:s' format
                        currentTime = new Date(response).getTime();

                        // Calculate elapsed time and remaining time
                        let elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                        remainingTime = callDuration - elapsedTime;
                        // Ensure remainingTime is not negative
                        if (remainingTime < 0) {
                            remainingTime = 0;
                        }
                    // startTimer();

                    }).fail(function() {
                        console.error("Error fetching server time");
                    });

                function updateTimer() {
                    var minutes = Math.floor(remainingTime / 60);
                    var seconds = remainingTime % 60;

                    var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') +
                        seconds;
                    document.getElementById('remainingTime').innerHTML = formattedTime;
                }

                // Initial display
                updateTimer();

                timerInterval = setInterval(function() {
                    remainingTime--;
                    if (remainingTime < 0) {
                        remainingTime = 0;
                    }
                    updateTimer();

                    if (remainingTime <= 0) {
                        endCall();
                        clearInterval(timerInterval);
                    }
                }, 1000);
            }

    async function endCall() {
        if (callEnded) return;
        callEnded = true;

        clearInterval(timerInterval);

        // Cleanup Agora
        if (agoraClient) {
            try {
                if (localAudioTrack) {
                    localAudioTrack.close();
                }
                if (localVideoTrack) {
                    localVideoTrack.close();
                }
                await agoraClient.leave();
                console.log('Agora cleanup completed');
            } catch (error) {
                console.error('Error during Agora cleanup:', error);
            }
        }

        // Cleanup Zegocloud UIKit
        if (zegoUIKit && zegoJoined) {
            try {
                zegoUIKit.leaveRoom();
                zegoJoined = false;
                console.log('Zegocloud UIKit cleanup completed');
            } catch (error) {
                console.error('Error during Zegocloud UIKit cleanup:', error);
            }
        }

        // Update astrologer status
        try {
            await fetch("{{ route('api.addCallStatus') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: 'Online',
                    token: "{{ $token }}",
                    astrologerId: "{{ $astrologerId }}"
                })
            });
            console.log('Astrologer status updated to Online');
        } catch (error) {
            console.error('Error updating call status:', error);
        }

        toastr.success('Call ended successfully');
        
        // Redirect to home
        setTimeout(() => {
            window.location.href = "{{ route('front.astrologerindex') }}";
        }, 2000);
    }

   
    // Handle page unload
    window.addEventListener('beforeunload', function(e) {
        if (!callEnded) {
            endCall();
        }
    });
</script>


<script>
        $(document).ready(function() {
            $('button.mode-switch').click(function() {
                $('body').toggleClass('dark');
            });

            $(".btn-close-right").click(function() {
                $(".right-side").removeClass("show");
                $(".expand-btn").addClass("show");
            });

            $(".expand-btn").click(function() {
                $(".right-side").addClass("show");
                $(this).removeClass("show");
            });
        });

        // function endCall() {
            
        //     $.ajax({
        //         url: "{{ route('api.addChatStatus') }}",
        //         type: 'POST',
        //         data: {
        //             status: 'Online',
        //             token:"{{$token}}",
        //             astrologerId:"{{$astrologerId}}"
        //         },
        //         success: function(response) {
        //            console.log('success');
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error updating chat status:', error);
        //         }
        //     });
            
        //     $.ajax({
        //     url: "{{ route('api.addCallStatus') }}",
        //     type: 'POST',
        //     data: {
        //          status: 'Online',
        //         token:"{{$token}}",
        //         astrologerId:"{{$astrologerId}}"
        //     },
        //         success: function(response) {
        //         toastr.success('Call Ended Successfully');
        //         window.location.href = "{{ route('front.astrologerindex') }}";
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error updating call status:', error);
        //         }
        //     });
            
        //     // toastr.success('Call Ended Successfully');
        //     // window.location.href = "{{ route('front.astrologerindex') }}";
        // }
    </script>
    <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
    <script src="{{ asset('public/frontend/agora/index.js') }}"></script>


    <script>
        $(document).ready(function() {
            var callDuration = {{ $callrequest->call_duration }};
            var timerInterval;
            var statusCheckInterval;

            $("#local-player-name").text("{{ astroauthcheck()['name'] }}");
            $("#remote-player-name").text("{{ $getUser['recordList'][0]['name'] }}");

            function fetchCallStatus() {
                $.ajax({
                    url: '{{ route('front.callStatus', ['callId' => $callrequest->id]) }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.call.callStatus === 'Confirmed') {
                            var updateTime = new Date(response.call.updated_at)
                        .getTime(); // Use updated_at from the response
                            startTimer(updateTime);
                            clearInterval(statusCheckInterval);
                        }
                    }
                });
            }

            function startTimer(updateTime) {
                setupFirebaseListener();
                // var currentTime = new Date().getTime();
                // var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                // var remainingTime = callDuration - elapsedTime;
                let currentTime = remainingTime = elapsedTime='';
                $.get("{{ route('front.getDateTime') }}", function(response) {
                        // Assuming the response contains the server time in 'Y-m-d H:i:s' format
                        currentTime = new Date(response).getTime();

                        // Calculate elapsed time and remaining time
                        let elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                        remainingTime = callDuration - elapsedTime;
                        // Ensure remainingTime is not negative
                        if (remainingTime < 0) {
                            remainingTime = 0;
                        }
                    // startTimer();

                    }).fail(function() {
                        console.error("Error fetching server time");
                    });

                function updateTimer() {
                    var minutes = Math.floor(remainingTime / 60);
                    var seconds = remainingTime % 60;

                    var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') +
                        seconds;
                    document.getElementById('remainingTime').innerHTML = formattedTime;
                }

                // Initial display
                updateTimer();

                timerInterval = setInterval(function() {
                    remainingTime--;
                    if (remainingTime < 0) {
                        remainingTime = 0;
                    }
                    updateTimer();

                    if (remainingTime <= 0) {
                        endCall();
                        clearInterval(timerInterval);
                    }
                }, 1000);
            }

            function setupFirebaseListener() {
                const callId = '{{ $callId }}'; // Your Laravel chat ID
                const db = firebase.firestore();
                
                // Listen to the specific document in 'updatechat' collection
                db.collection('updatecall').doc(callId)
                    .onSnapshot((doc) => {
                        if (doc.exists) {
                            const firebaseData = doc.data();
                            const newDuration = firebaseData.duration;
                            const previousDuration = callDuration;

                            // Update chatDuration
                            callDuration = newDuration;

                            // Adjust remaining time only if duration increased
                            if (callDuration > previousDuration) {
                                const additionalTime = callDuration - previousDuration;
                                remainingTime += additionalTime;
                                console.log("Added additional time from Firebase:", additionalTime);
                                
                                // Update UI immediately
                                updateTimer();
                            }
                        }
                    }, (error) => {
                    console.error("Firebase listener error:", error);
                });
            }


            // Initial status check
            fetchCallStatus();
            // Check the status every second
            statusCheckInterval = setInterval(fetchCallStatus, 2000);

            // Initial display of remaining time
            document.getElementById('remainingTime').innerHTML = formatTime(callDuration);

            function formatTime(seconds) {
                var minutes = Math.floor(seconds / 60);
                seconds = seconds % 60;
                return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }
        });



        $(document).ready(function() {
            $('#kundaliButton').click(function() {
                var userId = "{{ $userId }}"; // Fetch user ID from PHP

                // Show loading text
                $('#kundaliContent').html('<p>Loading...</p>');

                // Call the API
                $.ajax({
                    url: "{{ url('/api/kundali/getKundaliReport') }}",
                    type: "POST",
                    data: {
                        userId: userId
                    },
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response); // Debugging

                        if (!response || response.planet.status == 400 || response.planet
                            .status == 402) {
                                $('#kundaliModal').modal('show');
                            $('#kundaliContent').html('<h3 class="text-center mt-5 mb-5">No Kundali Found</h3>');
                            return;
                        }

                        // Populate modal content dynamically
                        var html = generateKundaliReportHTML(response);
                        $('#kundaliContent').html(html);

                        // ✅ Open modal only after successful API response
                        $('#kundaliModal').modal('show');
                    },
                    error: function() {
                        $('#kundaliContent').html('<p>Error fetching Kundali report.</p>');
                    }
                });
            });

            function generateKundaliReportHTML(response) {
                var html = `
            <ul class="nav nav-tabs" id="kundaliTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab"
                        aria-controls="basic" aria-selected="true">Basic Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="planetaryposition-tab" data-toggle="tab" href="#planetaryposition" role="tab"
                        aria-controls="planetaryposition" aria-selected="false">Planetary Position</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="predictions-tab" data-toggle="tab" href="#predictions" role="tab"
                        aria-controls="predictions" aria-selected="false">Predictions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="shodashvarga-tab" data-toggle="tab" href="#shodashvarga" role="tab"
                        aria-controls="shodashvarga" aria-selected="false">Shodashvarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="ashtakvarga-tab" data-toggle="tab" href="#ashtakvarga" role="tab"
                        aria-controls="ashtakvarga" aria-selected="false">Ashtakvarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="mahadasha-tab" data-toggle="tab" href="#mahadasha" role="tab"
                        aria-controls="mahadasha" aria-selected="false">Mahadasha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="yogini-tab" data-toggle="tab" href="#yogini" role="tab"
                        aria-controls="yogini" aria-selected="false">Yogini Dasha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="dosha-tab" data-toggle="tab" href="#dosha" role="tab"
                        aria-controls="dosha" aria-selected="false">Dosha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab"
                        aria-controls="report" aria-selected="false">Report</a>
                </li>
            </ul>

            <div class="tab-content" id="kundaliTabContent">
                <!-- Basic Details Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                    ${generateBasicDetailsHTML(response)}
                </div>

                <!-- Planetary Position Tab -->
                <div class="tab-pane fade" id="planetaryposition" role="tabpanel" aria-labelledby="planetaryposition-tab">
                    ${generatePlanetaryPositionHTML(response)}
                </div>

                <!-- Predictions Tab -->
                <div class="tab-pane fade" id="predictions" role="tabpanel" aria-labelledby="predictions-tab">
                    ${generatePredictionsHTML(response)}
                </div>

                <!-- Shodashvarga Tab -->
                <div class="tab-pane fade" id="shodashvarga" role="tabpanel" aria-labelledby="shodashvarga-tab">
                    ${generateShodashvargaHTML(response)}
                </div>

                <!-- Ashtakvarga Tab -->
                <div class="tab-pane fade" id="ashtakvarga" role="tabpanel" aria-labelledby="ashtakvarga-tab">
                    ${generateAshtakvargaHTML(response)}
                </div>

                <!-- Mahadasha Tab -->
                <div class="tab-pane fade" id="mahadasha" role="tabpanel" aria-labelledby="mahadasha-tab">
                    ${generateMahadashaHTML(response)}
                </div>

                <!-- Yogini Dasha Tab -->
                <div class="tab-pane fade" id="yogini" role="tabpanel" aria-labelledby="yogini-tab">
                    ${generateYoginiDashaHTML(response)}
                </div>

                <!-- Dosha Tab -->
                <div class="tab-pane fade" id="dosha" role="tabpanel" aria-labelledby="dosha-tab">
                    ${generateDoshaHTML(response)}
                </div>

                <!-- Report Tab -->
                <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                    ${generateReportHTML(response)}
                </div>
            </div>`;

                return html;
            }

            function generateBasicDetailsHTML(response) {
                return `
            <div class="row py-3">
                <div class="col-sm-12 mt-4">
                    <div class="table-responsive table-theme shadow-pink p-3">
                        <table class="table table-bordered border-pink font-14 mb-0">
                            <tbody>
                                <tr><th class="cellhead"><b>Name</b></th><td>${response.recordList.name || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Date</b></th><td>${response.recordList.birthDate || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Time</b></th><td>${response.recordList.birthTime || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Place</b></th><td>${response.recordList.birthPlace || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Latitude</b></th><td>${response.recordList.latitude || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Longitude</b></th><td>${response.recordList.longitude || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Timezone</b></th><td>${response.recordList.timezone || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Rasi</b></th><td>${response.planet.response.rasi || 'N/A'}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
            }

            function generatePlanetaryPositionHTML(response) {
                if (response.planet.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var filteredData = Object.keys(response.planet.response)
                    .filter(key => !isNaN(key)) // Filter numerical keys (0 to 9)
                    .map(key => response.planet.response[key]);

                var rows = filteredData.map(planet => `
        <tr>
            <td>${planet.full_name || 'N/A'}</td>
            <td>${planet.is_combust ? 'C' : ''}</td>
            <td>${planet.retro ? 'R' : ''}</td>
            <td>${planet.zodiac || 'N/A'}</td>
            <td>${planet.local_degree || 'N/A'}</td>
            <td>${planet.global_degree || 'N/A'}</td>
            <td>${planet.nakshatra || 'N/A'}</td>
            <td>${planet.nakshatra_pada || 'N/A'}</td>
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="matchV_thead bg-pink color-red">
                            <tr>
                                <th class="cellhead">Planet</th>
                                <th class="cellhead">C</th>
                                <th class="cellhead">R</th>
                                <th class="cellhead">Rashi</th>
                                <th class="cellhead">Local Degree</th>
                                <th class="cellhead">Global Degree</th>
                                <th class="cellhead">Nakshatra</th>
                                <th class="cellhead">Pada</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generatePredictionsHTML(response) {
                if (response.personal.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var predictions = response.personal.response.map((prediction, index) => {
                    var houseNumber = index + 1;
                    var houseWord = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh',
                        'Eighth', 'Ninth', 'Tenth', 'Eleventh', 'Twelfth'
                    ][houseNumber - 1] || houseNumber;

                    return `
            <div class="panel panel-default mb-3">
                <div class="panel-heading">
                    <h3 class="panel-title mb-0">
                        <a class="accordion-toggle font-weight-semi d-block py-2 colorblack font-16" data-toggle="collapse" data-parent="#accordion" href="#accAbount_${index}">
                            ${houseWord} House
                        </a>
                    </h3>
                </div>
                <div id="accAbount_${index}" class="panel-collapse collapse ${index === 0 ? 'show' : ''}" data-parent="#accordion">
                    <div class="panel-body px-0 px-md-3 py-4 border-top">
                        <p>${prediction.personalised_prediction}</p>
                    </div>
                </div>
            </div>`;
                }).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Predictions</h2>
            </div>
            <div class="col-12">
                <div class="panel-group my-1 p-3" id="accordion">${predictions}</div>
            </div>
        </div>`;
            }

            function generateShodashvargaHTML(response) {
                if (!response.charts) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var chartNames = {
                    'D1': 'Rasi',
                    'D2': 'Hora',
                    'D3': 'Drekkana',
                    'D4': 'Chaturthamsa',
                    'D5': 'Panchamamsa',
                    'D6': 'Shastamsa',
                    'D7': 'Saptamsa',
                    'D8': 'Astamsa',
                    'D9': 'Navamsa',
                    'D10': 'Dasamsa',
                    'D11': 'Rudramsa',
                    'D12': 'Dwadasamsa',
                    'D16': 'Shodasamsa',
                    'D20': 'Vimsamsa',
                    'D24': 'Siddhamsa',
                    'D27': 'Nakshatramsa',
                    'D30': 'Trimsamsa',
                    'D40': 'Khavedamsa',
                    'D45': 'Akshavedamsa',
                    'D60': 'Shastyamsa',
                    'chalit': 'Chalit',
                    'sun': 'Sun',
                    'moon': 'Moon',
                    'kp_chalit': 'Kp Chalit'
                };

                var charts = Object.keys(response.charts).map(key => `
        <div class="col-md-4 col-sm-6 col-12 mt-3">
            <p class="font-16 mb-1"><strong>${chartNames[key] || key}</strong></p>
            <div class="svg-container">${response.charts[key]}</div>
        </div>
    `).join('');

                return `
        <h2 class="p-3">Horoscope Chart</h2>
        <div class="row p-3">${charts}</div>`;
            }

            function generateAshtakvargaHTML(response) {
                if (response.ashtakvarga.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var ashtakvargaRows = response.ashtakvarga.response.ashtakvarga_order
                    .filter(name => name !== 'Ascendant')
                    .map((name, index) => `
            <tr>
                <td>${name}</td>
                ${response.ashtakvarga.response.ashtakvarga_points[index].map(point => `<td>${point}</td>`).join('')}
            </tr>
        `).join('');

                var binnashtakvargaRows = Array.from({
                    length: 12
                }, (_, i) => `
        <tr>
            ${Object.values(response.binnashtakvarga.response).map(points => `<td>${points[i]}</td>`).join('')}
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Ashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">&nbsp;</th>
                                <th>Ar</th><th>Ta</th><th>Ge</th><th>Ca</th><th>Le</th><th>Vi</th>
                                <th>Li</th><th>Sc</th><th>Sa</th><th>Ca</th><th>Aq</th><th>Pi</th>
                            </tr>
                        </thead>
                        <tbody>${ashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h2 class="font-24 p-3">Binnashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                ${Object.keys(response.binnashtakvarga.response).map(name => `<th>${name}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>${binnashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generateMahadashaHTML(response) {
                if (response.mahaDasha.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var mahadashaRows = response.mahaDasha.response.mahadasha.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.mahaDasha.response.mahadasha_order[index]}</td>
        </tr>
    `).join('');

                var predictions = response.mahaDashaPrediction.response.dashas.map(prediction => `
        <div class="prediction-block mb-4 p-3">
            <h4 class="font-18">${prediction.dasha} (${prediction.dasha_start_year} - ${prediction.dasha_end_year})</h4>
            <p class="font-14"><strong>Prediction:</strong> ${prediction.prediction}</p>
            <p class="font-14"><strong>Planet in Zodiac:</strong> ${prediction.planet_in_zodiac}</p>
        </div>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Mahadasha</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">MahaDasha</th>
                                <th class="cellhead">MahaDasha Order</th>
                            </tr>
                        </thead>
                        <tbody>${mahadashaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h3 class="font-20 mb-2 p-3">Mahadasha Predictions</h3>
            </div>
            <div class="col-12">${predictions}</div>
        </div>`;
            }

            function generateYoginiDashaHTML(response) {
                if (response.yoginiDashaMain.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var rows = response.yoginiDashaMain.response.dasha_list.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.yoginiDashaMain.response.dasha_lord_list[index]}</td>
            <td>${response.yoginiDashaMain.response.dasha_end_dates[index]}</td>
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">Dasha</th>
                                <th class="cellhead">Dasha Lord</th>
                                <th class="cellhead">End Date</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generateDoshaHTML(response) {
                var doshas = ['mangalDosh', 'kaalsarpDosh', 'manglikDosh', 'pitraDosh', 'papasamayaDosh'];
                var doshaHTML = doshas.map(dosha => {
                    if (response[dosha].status === 400) {
                        return `<p class="text-center">No Record Found for ${dosha}</p>`;
                    }

                    return `
            <div class="col-12 mb-3">
                <div class="table-responsive table-theme shadow-pink p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead" colspan="2">${dosha.replace(/([A-Z])/g, ' $1').trim()}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <p>${response[dosha].response.bot_response}</p>
                                    ${response[dosha].response.remedies ? `
                                            <h5 class="font-16">Remedies</h5>
                                            <div class="dosha-remedies">
                                                ${response[dosha].response.remedies.map(remedy => `<p>${remedy}</p>`).join('')}
                                            </div>
                                        ` : ''}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
                }).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Doshas</h2>
            </div>
            ${doshaHTML}
        </div>`;
            }

            function generateReportHTML(response) {
                var ascendantReport = response.ascendantReport.status === 200 ? `
        <div class="col-12">
            <h2 class="font-24 p-3">Ascendant Report</h2>
            <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                <table class="table table-bordered border-pink font-14 mb-0">
                    <thead class="font-13">
                        <tr class="bg-pink color-red font-weight-normal">
                            <th class="cellhead">Aspect</th>
                            <th class="cellhead">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${response.ascendantReport.response.map(ascendant => `
                                <tr><td><strong>Ascendant</strong></td><td>${ascendant.ascendant}</td></tr>
                                <tr><td><strong>Ascendant Lord</strong></td><td>${ascendant.ascendant_lord}</td></tr>
                                <tr><td><strong>Ascendant Lord Location</strong></td><td>${ascendant.ascendant_lord_location} (${ascendant.ascendant_lord_house_location}th house)</td></tr>
                                <tr><td><strong>General Prediction</strong></td><td>${ascendant.general_prediction}</td></tr>
                                <tr><td><strong>Personalized Prediction</strong></td><td>${ascendant.personalised_prediction}</td></tr>
                                <tr><td><strong>Verbal Location</strong></td><td>${ascendant.verbal_location}</td></tr>
                                <tr><td><strong>Ascendant Lord Strength</strong></td><td>${ascendant.ascendant_lord_strength}</td></tr>
                                <tr><td><strong>Symbol</strong></td><td>${ascendant.symbol}</td></tr>
                                <tr><td><strong>Zodiac Characteristics</strong></td><td>${ascendant.zodiac_characteristics}</td></tr>
                                <tr><td><strong>Lucky Gem</strong></td><td>${ascendant.lucky_gem}</td></tr>
                                <tr><td><strong>Day for Fasting</strong></td><td>${ascendant.day_for_fasting}</td></tr>
                                <tr><td><strong>Gayatri Mantra</strong></td><td>${ascendant.gayatri_mantra}</td></tr>
                                <tr><td><strong>Flagship Qualities</strong></td><td>${ascendant.flagship_qualities}</td></tr>
                                <tr><td><strong>Spirituality Advice</strong></td><td>${ascendant.spirituality_advice}</td></tr>
                                <tr><td><strong>Good Qualities</strong></td><td>${ascendant.good_qualities}</td></tr>
                                <tr><td><strong>Bad Qualities</strong></td><td>${ascendant.bad_qualities}</td></tr>
                            `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    ` : `<p class="text-center">No Ascendant Record Found</p>`;

                var planetReport = Object.keys(response.planetReport).map(planet => {
                    if (response.planetReport[planet].status === 200) {
                        return response.planetReport[planet].response.map(planetDetails => `
                <div class="col-12">
                    <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                        <table class="table table-bordered border-pink font-14 mb-0">
                            <thead class="font-13">
                                <tr class="bg-pink color-red font-weight-normal">
                                    <th class="cellhead">
                                                                        <th class="cellhead">${planet} Report</th>
                                    <th class="cellhead">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><strong>Planet Location</strong></td><td>${planetDetails.planet_location} (${planetDetails.planet_native_location}th house)</td></tr>
                                <tr><td><strong>Planet Zodiac</strong></td><td>${planetDetails.planet_zodiac}</td></tr>
                                <tr><td><strong>Zodiac Lord</strong></td><td>${planetDetails.zodiac_lord}</td></tr>
                                <tr><td><strong>Zodiac Lord Location</strong></td><td>${planetDetails.zodiac_lord_location} (${planetDetails.zodiac_lord_house_location}th house)</td></tr>
                                <tr><td><strong>General Prediction</strong></td><td>${planetDetails.general_prediction}</td></tr>
                                <tr><td><strong>Planet Definitions</strong></td><td>${planetDetails.planet_definitions}</td></tr>
                                <tr><td><strong>Gayatri Mantra</strong></td><td>${planetDetails.gayatri_mantra}</td></tr>
                                <tr><td><strong>Qualities Long</strong></td><td>${planetDetails.qualities_long}</td></tr>
                                <tr><td><strong>Qualities Short</strong></td><td>${planetDetails.qualities_short}</td></tr>
                                <tr><td><strong>Affliction</strong></td><td>${planetDetails.affliction}</td></tr>
                                <tr><td><strong>Personalized Prediction</strong></td><td>${planetDetails.personalised_prediction || ''}</td></tr>
                                <tr><td><strong>Verbal Location</strong></td><td>${planetDetails.verbal_location}</td></tr>
                                <tr><td><strong>Planet Zodiac Prediction</strong></td><td>${planetDetails.planet_zodiac_prediction}</td></tr>
                                <tr><td><strong>Character Keywords Positive</strong></td><td>${planetDetails.character_keywords_positive.join(', ')}</td></tr>
                                <tr><td><strong>Character Keywords Negative</strong></td><td>${planetDetails.character_keywords_negative.join(', ')}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `).join('');
                    } else {
                        return `<p class="text-center">No ${planet} Record Found</p>`;
                    }
                }).join('');

                return `
        <div class="row">
            ${ascendantReport}
            <div class="col-12 mt-4">
                <h2 class="font-24 p-3">Planet Report</h2>
                ${planetReport}
            </div>
        </div>`;
            }

        });
    </script>
@endsection