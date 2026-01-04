@extends('frontend.layout.master')
@php
use Symfony\Component\HttpFoundation\Session\Session;
$session = new Session();
$token = $session->get('token');
@endphp
@php
    $call_method = $callrequest->call_method ?? 'agora';
@endphp
<!-- <link rel="stylesheet" href="{{ asset('public/frontend/agora/index.css') }}"> -->
<style>
    @media only screen and (max-width: 767px) {

        #local-player div:first-child,
        #remote-playerlist div:first-child {
            min-height: 0px !important;
            position: unset !important;
        }
    }

    .dIzgYQV4CBbzZxzJbwbS {
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

    .player,
    video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .name-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
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
    .agora-container,
    .hms-container,
    .zegocloud-container {
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
        background: rgba(0, 0, 0, 0.8);
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
</style>

@section('content')
@if (authcheck())
@php
$userId = authcheck()['id'];
$astrologerId = request()->query('astrologerId');
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
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Call</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Intake Form Modal -->
<div class="modal fade mt-2 mt-md-5" id="intake" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">Intake Form</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <form class="px-3 font-14" method="get" id="intakeForm">
                    <input type="hidden" name="astrologerId" value="{{ $astrologerId }}">
                    @if (authcheck())
                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                    @endif
                    <div class="col-12 py-3">
                        <div class="form-group mb-0">
                            <label>Select Time You want to call<span class="color-red">*</span></label><br>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="180"> 3 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="300"> 5 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="600"> 10 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="900"> 15 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="1200"> 20 mins
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 py-3">
                        <div class="row">
                            <div class="col-12 pt-md-3 text-center mt-2">
                                <button class="font-weight-bold ml-0 w-100 btn btn-chat" id="loaderintakeBtn" type="button" style="display:none;" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                                </button>
                                <button type="submit" class="btn btn-block btn-chat px-4 px-md-5 mb-2" id="intakeBtn">Continue Call</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input id="appid" type="hidden" value="{{ $agoraAppIdValue }}">
<input id="token" type="hidden" value="{{ $callrequest->token }}">
<input id="channel" type="hidden" value="{{ $callrequest->channelName }}">
<input id="callMethod" type="hidden" value="{{ $callrequest->call_method }}">
<input id="userId" type="hidden" value="{{ $userId }}">
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
                @if($call_method == 'agora')
                    <button class="video-action-button mic" onclick="toggleMic()" id="mic-icon">
                        <i class="fas fa-microphone"></i>
                    </button>
                    @if($call_type == 11)
                        <button class="video-action-button camera" onclick="toggleVideo()" id="video-icon">
                            <i class="fas fa-video"></i>
                        </button>
                    @endif
                @else
                    <span></span>
                @endif


                <a 
                    data-toggle="modal" 
                    data-target="#intake" 
                    class="btn btn-report mr-3 mb-2 add-topup-btnn" 
                    id="addTopupLink">
                    Add Topup
                </a>
                <form id="endCallForm" class="d-inline-block">
                    <input type="hidden" name="callId" value="{{ $callId }}">
                    <input type="hidden" name="totalMin" id="totalMin" value="">
                    <button type="button" class="video-action-button endcall" id="leave" onclick="endCall()">Leave</button>
                    <small style="display:block">Note : call can be end after 1 min</small>
                </form>
                <div class="video-call-actions"></div>
            </div>
        </div>

        <div class="app-main col-md-9 col-sm-12 order-sm-0">
            <!-- Agora Video Container -->
            <div class="video-call-wrapper shadow agora-container" id="agoraContainer">
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="local-player-name">{{ authcheck()['name'] }}</a>
                    <div id="local-player" class="player"></div>
                    <div class="avatar-fallback" id="local-avatar">
                        {{ substr(authcheck()['name'], 0, 1) }}
                    </div>
                </div>
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="remote-player-name">Astrologer</a>
                    <div id="remote-playerlist"></div>
                    <div class="avatar-fallback" id="remote-avatar">
                        A
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

<!-- Insufficient TopUp Modal -->
<div class="modal fade" id="insufficientTopUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Update Top Up</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p>Your current session will expire soon. Please Top Up Now.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning text-white" data-dismiss="modal" data-toggle="modal" data-target="#intake">
                    Top Up Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>




{{-- Loading Overlay --}}
<div id="loadingOverlay" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
            background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; flex-direction:column;">
    <div id="loadingText" style="color:white; font-size:18px;">Loading...</div>
</div>

{{-- ===================== CALL SCRIPTS ===================== --}}
@if($call_method == 'agora')
    {{-- ===================== AGORA SCRIPTS ===================== --}}
    <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
    <script src="{{ asset('public/frontend/agora/index.js') }}"></script>

@elseif($call_method == 'zegocloud')
    {{-- ===================== ZEGOCLOUD SCRIPT ===================== --}}
<script>
        let currentProvider = "{{ $callrequest->call_method }}";
        let callDuration = "{{$callrequest->call_duration}}";
        let remainingTime = callDuration;
        let timerInterval;
        let callEnded = false;

        let agoraClient = null;
        let localAudioTrack = null;
        let localVideoTrack = null;

        let zegoUIKit = null;
        let zegoJoined = false;

        $(document).ready(function() {
            console.log('Initializing call with provider:', currentProvider);
            initializeCall();
            startTimer();
        });

        function initializeCall() {
            showLoading('Initializing call...');
            if (currentProvider === 'agora') {
                initializeAgora();
            } else if (currentProvider === 'zegocloud') {
                initializeZegocloudUIKit();
            } else {
                currentProvider = 'agora';
                initializeAgora();
            }
        }

        async function initializeZegocloudUIKit() {
            try {
                showProviderUI('zegocloud');
                showLoading('Connecting to Zegocloud...');

                const appID = "{{ systemflag('zegoAppId') }}";
                const serverSecret = "{{ systemflag('zegoServerSecret') }}";
                const userID = "{{ $userId }}";
                const userName = "{{authcheck()['name']}}";
                const roomID = document.getElementById('channel').value;
                const isVideoCall = "{{ $call_type }}" == "11";

                if (!appID) throw new Error('Zegocloud App ID is missing');
                if (!serverSecret || serverSecret === '') throw new Error('Zegocloud Server Secret is missing or invalid');
                if (!roomID) throw new Error('Room ID is missing');

                const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
                    parseInt(appID),
                    serverSecret,
                    roomID,
                    userID,
                    userName
                );

                zegoUIKit = ZegoUIKitPrebuilt.create(kitToken);

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
                        console.log('✅ Zegocloud UIKit: Joined room successfully');
                        zegoJoined = true;
                        hideLoading();
                    },

                    onLeaveRoom: () => {
                        console.log('Zegocloud UIKit: Left room');
                        if (zegoJoined) endCall();
                    },

                    onUserLeave: (users) => {
                        console.log('User left:', users);
                        setTimeout(() => {
                            if (zegoJoined) {
                                console.log('Other user left, ending call...');
                                endCall();
                            }
                        }, 1000);
                    },

                    onError: (error) => {
                        console.error('Zegocloud UIKit Error:', error);
                        showError('Zegocloud error: ' + error.message);
                    }
                };

                zegoUIKit.joinRoom(config);
            } catch (error) {
                console.error('Zegocloud UIKit initialization failed:', error);
                showError('Failed to initialize Zegocloud: ' + (error.message || 'Unknown error'));
                hideLoading();
            }
        }

        function showProviderUI(provider) {
            document.querySelectorAll('.agora-container, .zegocloud-container').forEach(el => {
                el.style.display = 'none';
            });
            if (provider === 'zegocloud') {
                document.getElementById('zegocloudContainer').style.display = 'block';
            } else {
                document.getElementById(provider + 'Container').style.display = 'block';
            }
        }

        function showLoading(message) {
            const overlay = document.getElementById('loadingOverlay');
            const text = document.getElementById('loadingText');
            if (overlay) {
                overlay.style.display = 'flex';
                if (text && message) text.textContent = message;
            }
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';
        }

        function showError(message) {
            console.error('Error:', message);
            alert('Error: ' + message);
        }

        function startTimer() {
            function updateTimer() {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById('remainingTime').textContent = formattedTime;
            }

            updateTimer();
            timerInterval = setInterval(() => {
                remainingTime--;
                updateTimer();

                if (remainingTime <= 0) endCall();
                if (remainingTime === 90 || remainingTime === 30) {
                    $('#insufficientTopUpModal').modal('show');
                }
            }, 1000);
        }

        async function endCall() {
            if (callEnded) return;
            callEnded = true;
            clearInterval(timerInterval);

            if (agoraClient) {
                try {
                    if (localAudioTrack) localAudioTrack.close();
                    if (localVideoTrack) localVideoTrack.close();
                    await agoraClient.leave();
                } catch (e) { console.error('Agora cleanup error:', e); }
            }

            if (zegoUIKit && zegoJoined) {
                try {
                    zegoUIKit.leaveRoom();
                    zegoJoined = false;
                } catch (e) { console.error('Zego cleanup error:', e); }
            }

            var totalSeconds = callDuration - remainingTime;
            $("#totalMin").val(totalSeconds);

            try {
                const response = await fetch("{{ route('api.endCall', ['token' => $token]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        callId: "{{ $callId }}",
                        totalMin: totalSeconds
                    })
                });
                if (response.ok) console.log('Call ended successfully on server');
            } catch (err) {
                console.error('Error ending call on server:', err);
            }

            toastr.success('Call ended successfully');
            setTimeout(() => window.location.href = "{{ route('front.home') }}", 2000);
        }

        window.addEventListener('beforeunload', function(e) {
            if (!callEnded) endCall();
        });

        function toggleMic() {
        if (localAudioTrack) {
            localAudioTrack.setEnabled(!localAudioTrack.enabled);
            const micBtn = document.getElementById('mic-icon');
            if (localAudioTrack.enabled) {
                micBtn.classList.remove('muted');
                micBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            } else {
                micBtn.classList.add('muted');
                micBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            }
        }
    }

        function toggleVideo() {
        if (localVideoTrack) {
            localVideoTrack.setEnabled(!localVideoTrack.enabled);
            const videoBtn = document.getElementById('video-icon');
            if (localVideoTrack.enabled) {
                videoBtn.classList.remove('off');
                videoBtn.innerHTML = '<i class="fas fa-video"></i>';
                document.getElementById('local-avatar').style.display = 'none';
            } else {
                videoBtn.classList.add('off');
                videoBtn.innerHTML = '<i class="fas fa-video-slash"></i>';
                document.getElementById('local-avatar').style.display = 'flex';
            }
        }
    }
 </script>
@endif


<script>
    $(document).ready(function() {
        $('#intakeBtn').click(function(e) {
            e.preventDefault();

            // Show loader and hide button
            $('#intakeBtn').hide();
            $('#loaderintakeBtn').show();

            setTimeout(function() {
                $('#intakeBtn').show();
                $('#loaderintakeBtn').hide();
            }, 3000);

            // Variables from PHP
            var astrocharge = "{{ $getAstrologer['recordList'][0]['charge'] }}";
            var wallet_amount = "{{ $walletAmount ?? 0}}";
            var callId = "{{ $callId }}";
            var token = "{{ session('token') }}";
            var astrologerId = "{{ $getAstrologer['recordList'][0]['id'] }}";
            var userId = "{{ authcheck() ? authcheck()['id'] : 'null' }}";

            // AJAX to get current chat duration
            $.ajax({
                url: "{{ route('api.getcurrentCallDuration', ['callId' => $callId]) }}",
                type: 'POST',
                success: function(response) {
                    if (response.callDuration) {
                        // Calculate the remaining wallet amount
                        let callDurationMinutes = response.callDuration / 60;
                        let remainingWalletAmount = wallet_amount - (callDurationMinutes * astrocharge);
                        remainingWalletAmount = remainingWalletAmount.toFixed(2);

                        // Form data
                        var formData = $('#intakeForm').serialize();
                        var urlParams = new URLSearchParams(formData);
                        var call_duration = $('input[name="call_duration"]:checked').val();
                        var call_duration_minutes = Math.ceil(call_duration / 60);
                        var total_charge = astrocharge * call_duration_minutes;
                        console.log('Call duration:'+call_duration);
                        console.log('call_duration_minutes:'+call_duration_minutes);
                        console.log('total_charge:'+total_charge);
                        console.log('remainingWalletAmount:'+remainingWalletAmount);
                        
                        if (total_charge <= remainingWalletAmount) {
                            // Continue call
                            $.ajax({
                                url: "{{ route('api.updatecallMinute') }}",
                                type: 'POST',
                                data: {
                                    call_duration: call_duration,
                                    callId: callId,
                                },
                                success: function() {
                                    toastr.success('Call Continued');
                                    $('#intake').modal('hide');
                                    $('.modal-backdrop').remove(); // Ensure backdrop removal
                                    $('body').removeClass('modal-open'); // Re-enable scrolling

                                },
                                error: function(xhr) {
                                    toastr.error(xhr.responseText);
                                },
                            });
                        } else {
                            // Redirect to payment
                            $.ajax({
                                url: "{{ route('user.addpayment', ['token' => $token]) }}",
                                type: 'POST',
                                data: {
                                    amount: total_charge,
                                    payment_for: "topupcall",
                                    durationcall: call_duration,
                                    callId: callId,
                                },
                                success: function(response) {
                                    $('#intake').modal('hide');
                                    $('.modal-backdrop').remove(); // Ensure backdrop removal
                                    $('body').removeClass('modal-open'); // Re-enable scrolling
                                    window.open(response.url, '_blank', 'width=800,height=600,resizable=yes,scrollbars=yes');
                                },
                                error: function(xhr) {
                                    toastr.error(xhr.responseText);
                                },
                            });
                        }
                    } else {
                        toastr.error('Invalid call duration.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    toastr.error(errorMessage || 'An error occurred while fetching the call duration.');
                },
            });
        });
    });
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
</script>

<script>
    var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
    var callDuration = "{{ $callrequest->call_duration }}";
    let currentTime = remainingTime = elapsedTime = '';
    var timerInterval = '';

    $.get("{{ route('front.getDateTime') }}", function(response) {
        currentTime = new Date(response).getTime();
        let elapsedTime = Math.floor((currentTime - updateTime) / 1000);
        remainingTime = callDuration - elapsedTime;

        if (remainingTime < 0) {
            remainingTime = 0;
        }

        startTimer();
        setupFirebaseListener();
    }).fail(function() {
        console.error("Error fetching server time");
        // Fallback to local time if server time fails
        currentTime = new Date().getTime();
        let elapsedTime = Math.floor((currentTime - updateTime) / 1000);
        remainingTime = callDuration - elapsedTime;

        if (remainingTime < 0) {
            remainingTime = 0;
        }

        startTimer();
        setupFirebaseListener();
    });

    $("#local-player-name").text("{{ authcheck()['name'] }}");
    $("#remote-player-name").text("{{ $getAstrologer['recordList'][0]['name'] }}");

    function updateTimer() {
        var minutes = Math.floor(remainingTime / 60);
        var seconds = remainingTime % 60;
        var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        document.getElementById('remainingTime').innerHTML = formattedTime;
    }

    function startTimer() {
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

            if (remainingTime == 90 || remainingTime == 30) {
                $('#insufficientTopUpModal').modal('show');
            }

            var totalSeconds = callDuration - remainingTime;
            $("#leave").prop("disabled", true);
            if (totalSeconds >= 60) {
                $("#leave").prop("disabled", false);
            }
        }, 1000);
    }

    function setupFirebaseListener() {
        const callId = '{{ $callId }}';
        const db = firebase.firestore();

        db.collection('updatecall').doc(callId)
            .onSnapshot((doc) => {
                if (doc.exists) {
                    const firebaseData = doc.data();
                    const newDuration = firebaseData.duration;
                    const previousDuration = callDuration;

                    callDuration = newDuration;

                    // Recalculate remaining time based on current server time and new total duration
                    $.get("{{ route('front.getDateTime') }}", function(response) {
                        const currentTime = new Date(response).getTime();
                        const updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
                        const elapsedTime = Math.floor((currentTime - updateTime) / 1000);

                        remainingTime = callDuration - elapsedTime;

                        if (remainingTime < 0) {
                            remainingTime = 0;
                        }

                        console.log("Firebase update - New duration:", callDuration, "Remaining time:", remainingTime);
                        updateTimer();
                    }).fail(function() {
                        console.error("Error fetching server time for Firebase update");
                    });
                }
            }, (error) => {
                console.error("Firebase listener error:", error);
            });
    }

    function refreshTimer() {
        $.get("{{ route('front.getDateTime') }}", function(response) {
            const currentTime = new Date(response).getTime();
            const updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
            const elapsedTime = Math.floor((currentTime - updateTime) / 1000);

            // Get the latest call duration from backend
            $.ajax({
                url: "{{ route('api.getcurrentCallDuration', ['callId' => $callId]) }}",
                type: 'POST',
                success: function(response) {
                    if (response.callDuration) {
                        callDuration = response.callDuration;
                        remainingTime = callDuration - elapsedTime;

                        if (remainingTime < 0) {
                            remainingTime = 0;
                        }

                        updateTimer();
                        console.log("Timer refreshed - Duration:", callDuration, "Remaining:", remainingTime);
                    }
                },
                error: function(xhr) {
                    console.error("Error refreshing timer:", xhr);
                }
            });
        }).fail(function() {
            console.error("Error fetching server time for timer refresh");
        });
    }
</script>
@endsection
