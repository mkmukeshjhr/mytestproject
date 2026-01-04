<?php $__env->startSection('content'); ?>
<?php
$countries = DB::table('countries')
->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")
->get();


use Symfony\Component\HttpFoundation\Session\Session;
$session = new Session();
$token = $session->get('token');

?>

    <style>
        .modal-content {
            background-clip: border-box !important;
            border: none !important;
            border-radius: 0 !important;
        }
        .detailed-link:hover{
            color: #fff !important;
        }

        .read-more
        {
            color :blue;
        }

        h2.heading span[aria-expanded="true"] .fa-chevron-up {
            border: 2px solid #65a9fd;
            border-radius: 50%;
            padding: 5px;
            -webkit-text-stroke: 2px #fff5f6;
            font-size: 24px;
        }



    /* Center items only for web (screens wider than 768px) */
    @media (min-width: 768px) {
            .astrology-video-carousel {
                justify-items: center;
            }
        }

        .video-title{
            height:60px;
        }


        #videoModal .close {
        font-size: 2rem; /* Use relative units (30px equivalent) */
        position: absolute; /* Position absolutely */
        right: 0; /* Adjust position */
        top: -2.5rem; /* Adjust position */
        z-index: 1; /* Ensure it's above the video */
        color: #fff; /* White color for visibility */
        opacity: 1; /* Ensure it's fully visible */
        transition: color 0.3s ease; /* Smooth hover effect */
        }

        #videoModal .close:hover {
        color: #ccc; /* Light gray on hover */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
        #videoModal .close {
            font-size: 1.5rem; /* Smaller font size for mobile */
            top: -2rem; /* Adjust position for mobile */
        }
        }

        @media (max-width: 576px) {
        #videoModal .close {
            font-size: 1.25rem; /* Even smaller font size for very small screens */
            top: -0.5rem; /* Adjust position for very small screens */
        }
        }

      #videoModal .modal-header {
        padding: 0; /* Remove padding */
        border: none; /* Remove border */
      }

      #videoModal .modal-body {
        padding: 0; /* Remove padding */
      }
    </style>


     <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Modal -->
<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Random astrologer select karo
$randomAstrologer = DB::table('astrologers')
->where('callStatus', 'Online')
    ->inRandomOrder()
    ->first();

$token = session('token');
$wallet_amount = authcheck()['totalWalletAmount'] ?? 0;
?>

<?php if(Auth::check()): ?>
<?php if($isFreeAvailable == true): ?>
<div class="modal fade p-4" id="autoModal" tabindex="-1" aria-labelledby="autoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <div class="modal-header">
          <h5 class="modal-title">Would you like to connect with an astrologer now?</h5>
          <!-- <button type="button" class="btn btn-sm btn-danger" id="doNotShowmodule">Hide</button> -->
          <button type="button" class="btn btn-sm btn-primary" id="closeModalBtn">Close</button>
        </div>

            <div class="modal-body p-4">
                <form class="px-3 font-14" method="post" id="callintakeForm">

                    <?php if(authcheck()): ?>
                    <input type="hidden" name="userId" value="<?php echo e(authcheck()['id']); ?>">
                    <?php endif; ?>

                    <input type="hidden" name="call_type" id="call_type" value="10">
                    <input type="hidden" name="astrocharge" id="astrocharge" value="">
                    <input type="hidden" name="astrologerId" id="astroId" value="<?php echo e($randomAstrologer->id ?? ''); ?>">

                    <div class="row">
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-group mb-0">
                                <label for="Name">Name<span class="color-red">*</span></label>
                                <input class="form-control border-pink matchInTxt shadow-none" id="Name" name="name"
                                    placeholder="Enter Name" type="text"
                                    value="<?php echo e($getIntakeForm['recordList'][0]['name'] ?? ''); ?>"
                                    pattern="^[a-zA-Z\s]{2,50}$" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label for="profileImage">Contact No*</label>
                            <div class="d-flex inputform country-dropdown-container"
                                style="border: 1px solid #ddd; border-radius: 4px;">
                                <select class="form-control select2" id="countryCode1" name="countryCode"
                                    style="border: none; border-right: 1px solid #ddd;">
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($country->phonecode); ?>">+<?php echo e($country->phonecode); ?>

                                        <?php echo e($country->iso); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input class="form-control mobilenumber" id="contact" maxlength="12" name="phoneNumber"
                                    type="number"
                                    value="<?php echo e($getIntakeForm['recordList'][0]['phoneNumber'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Gender<span class="color-red">*</span></label>
                            <select class="form-control" id="Gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Birthdate<span class="color-red">*</span></label>
                            <input class="form-control" id="BirthDate" name="birthDate" type="date"
                                value="<?php echo e(isset($getIntakeForm['recordList'][0]['birthDate']) ? date('Y-m-d', strtotime($getIntakeForm['recordList'][0]['birthDate'])) : ''); ?>"
                                required>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Birthtime</label>
                            <input class="form-control" id="BirthTime" name="birthTime" type="time"
                                value="<?php echo e($getIntakeForm['recordList'][0]['birthTime'] ?? ''); ?>">
                        </div>

                        <input type="hidden" id="latitude" name="latitude"
                            value="<?php echo e($getIntakeForm['recordList'][0]['latitude'] ?? ''); ?>">
                        <input type="hidden" id="longitude" name="longitude"
                            value="<?php echo e($getIntakeForm['recordList'][0]['longitude'] ?? ''); ?>">
                        <input type="hidden" id="timezone" name="timezone"
                            value="<?php echo e($getIntakeForm['recordList'][0]['timezone'] ?? '5.5'); ?>">

                        <div class="col-12 col-md-6 py-2">
                            <label>Birthplace<span class="color-red">*</span></label>
                            <input class="form-control" id="BirthPlace" name="birthPlace" type="text"
                                value="<?php echo e($getIntakeForm['recordList'][0]['birthPlace'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Marital Status<span class="color-red">*</span></label>
                            <select class="form-control" id="MaritalStatus" name="maritalStatus" required>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Occupation</label>
                            <input class="form-control" id="Occupation" name="occupation" type="text"
                                value="<?php echo e($getIntakeForm['recordList'][0]['occupation'] ?? ''); ?>">
                        </div>

                        <div class="col-12 col-md-6 py-2">
                            <label>Topic Of Concern</label>
                            <input class="form-control" id="TopicOfConcern" name="topicOfConcern" type="text"
                                value="<?php echo e($getIntakeForm['recordList'][0]['topicOfConcern'] ?? ''); ?>">
                        </div>
                        <?php if(authcheck()): ?>
                         <?php if($isFreeAvailable == false): ?>
                         <input type="hidden" name="isFreeSession" value="0">
                         <?php else: ?>
                         <input type="hidden" name="isFreeSession" value="1">
                         <?php endif; ?>
                         <?php endif; ?>
                    </div>

                    <div class="col-12 py-3">
                        <label class="mr-3">
                            <input type="radio" name="call_option" value="10" class="callOptionRadio" checked> Audio Call
                        </label>
                        <label class="mr-3">
                            <input type="radio" name="call_option" value="11" class="callOptionRadio"> Video Call
                        </label>
                        <!-- <label>
                            <input type="radio" name="call_option" value="12" class="callOptionRadio" > Chat
                        </label> -->
                    </div>

                    <div class="col-12 col-md-12 py-3">
                        <div class="row">
                            <div class="col-12 text-center mt-2" style="display: flex;">
                                <button class="font-weight-bold ml-0 w-100 btn btn-chat" id="callloaderintakeBtn"
                                    type="button" style="display:none;" disabled>
                                    <span class="spinner-border spinner-border-sm"></span> Loading...
                                </button>
                                 <button type="submit" class="btn btn-block btn-chat" id="callintakeBtn">
                                    Start Audio Call
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('input[name="call_option"]').change(function() {
        let selectedVal = $(this).val();
        let callText = "Chat";

        if (selectedVal == "10") callText = "Audio Call";
        else if (selectedVal == "11") callText = "Video Call";
        else callText = "Chat";

        $('#call_type').val(selectedVal);
        $('#callintakeBtn').text(`Start ${callText}`);
    });

    $('#callintakeForm').submit(function(e) {
        e.preventDefault();
        let callType = $('#call_type').val();
        console.log("Selected Call Type:", callType);
    });
});
</script>

<script>
    $(document).ready(function() {
        let nextPageUrl = "<?php echo e($getAstrologer->nextPageUrl()); ?>";
        $('#load-more').click(function() {
            let $btn = $(this);
            if (!nextPageUrl) {
                console.log("No more pages to load!");
                return;
            }
            $btn.prop('disabled', true).html('<span class="loader"></span> Loading...');
            authcheck = "<?php echo e(authcheck()); ?>";
            // Get current filters
            let sortBy = $('select[name="sortBy"]').val(); // Sorting dropdown
            let astrologerCategoryId = $('input[name="astrologerCategoryId"]').val(); // Hidden input or category filter
            let searchTerm = $('input[name="s"]').val(); // Search box

            // Add filters to the nextPageUrl if not already there
            let url = new URL(nextPageUrl, window.location.origin);
            if (sortBy) url.searchParams.set('sortBy', sortBy);
            if (astrologerCategoryId) url.searchParams.set('astrologerCategoryId', astrologerCategoryId);
            if (searchTerm) url.searchParams.set('s', searchTerm);
            $.ajax({
                url: url.toString(),
                type: "GET",
                success: function(response) {

                    if (response.getAstrologer && response.getAstrologer.data.length > 0) {
                        var html = '';
                        response.getAstrologer.data.forEach(function(astrologer) {
                            html += `
                            <div id="ATAAIOfferTile" class="psychic-card overflow-hidden expertOnline ask-guruji" data-astrologer-id="${astrologer.id}">
                                <a href="${astrologer.slug ? '/astrologer-details/' + astrologer.slug : '#'}" class="text-decoration-none">
                                    ${astrologer.is_boosted == 1 ? `
                                        <span class="must-try-badge font-10 position-absolute font-weight-semi text-center align-items-center justify-content-center text-white">Sponsored</span>
                                    ` : ''}
                                    <ul class="list-unstyled d-flex mb-0">
                                        <li class="mr-3 position-relative psychic-presence status-online" data-status="online">
                                            <div class="psyich-img position-relative">
                                                ${astrologer.profileImage ? `
                                                    <img src="/${astrologer.profileImage}" width="85" height="85" style="border-radius:50%;" loading="lazy">
                                                ` : `
                                                    <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png')); ?>" width="85" height="85" style="border-radius:50%;">
                                                `}
                                            </div>
                                               ${astrologer.callStatus === 'Busy' ? `
                                                <div class="status-badge specific-Clr-Busy" title="Online"></div>
                                                <div class="status-badge-txt text-center specific-Clr-Busy">
                                                    <span class="status-badge-txt specific-Clr-Busy tooltipex">${astrologer.callStatus}</span>
                                                </div>
                                            ` : (astrologer.callStatus === 'Offline' && astrologer.emergencyCallStatus) ?`
                                              <div class="status-badge specific-Clr-Busy" title="Online"></div>
                                                <div class="status-badge-txt text-center specific-Clr-Busy">
                                                    <span class="status-badge-txt specific-Clr-Busy tooltipex">Emergency</span>
                                                </div>
                                            ` : (astrologer.callStatus === 'Offline' || !astrologer.callStatus) ? `
                                                <div class="status-badge specific-Clr-Offline" title="Offline"></div>
                                                <div class="status-badge-txt text-center specific-Clr-Offline">
                                                    <span class="status-badge-txt specific-Clr-Offline tooltipex">${astrologer.callStatus || 'Offline'}</span>
                                                </div>

                                            ` : `
                                                <div class="status-badge specific-Clr-Online" title="Online"></div>
                                                <div class="status-badge-txt text-center specific-Clr-Online">
                                                    <span class="status-badge-txt specific-Clr-Online tooltipex">${astrologer.callStatus}</span>
                                                </div>
                                            `}
                                        </li>
                                        <li class="w-100 colorblack">
                                            <span class="colorblack font-weight-bold font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block" data-toggle="tooltip" title="" style="font-weight: bold;color: #495057 !important;">
                                                ${astrologer.name}
                                                <svg id="Layer_1" fill="#495057" height="16" width="16" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 106.11 122.88">
                                                    <path class="cls-1" d="M56.36,2.44A104.34,104.34,0,0,0,79.77,13.9a48.25,48.25,0,0,0,19.08,2.57l6.71-.61.33,6.74c1.23,24.79-2.77,46.33-11.16,63.32C86,103.6,72.58,116.37,55.35,122.85l-4.48,0c-16.84-6.15-30.16-18.57-39-36.47C3.62,69.58-.61,47.88.07,22l.18-6.65,6.61.34A64.65,64.65,0,0,0,28.23,13.5,60.59,60.59,0,0,0,48.92,2.79L52.51,0l3.85,2.44ZM52.93,19.3C66.46,27.88,78.68,31.94,89.17,31,91,68,77.32,96.28,53.07,105.41c-23.43-8.55-37.28-35.85-36.25-75,12.31.65,24.4-2,36.11-11.11ZM45.51,61.61a28.89,28.89,0,0,1,2.64,2.56,104.48,104.48,0,0,1,8.27-11.51c8.24-9.95,5.78-9.3,17.21-9.3L72,45.12a135.91,135.91,0,0,0-11.8,15.3,163.85,163.85,0,0,0-10.76,17.9l-1,1.91-.91-1.94a47.17,47.17,0,0,0-6.09-9.87,33.4,33.4,0,0,0-7.75-7.12c1.49-4.89,8.59-2.38,11.77.31Zm7.38-53.7c17.38,11,33.07,16.22,46.55,15,2.35,47.59-15.23,82.17-46.37,93.9C23,105.82,5.21,72.45,6.53,22.18,22.34,23,37.86,19.59,52.89,7.91Z"/>
                                                </svg>
                                            </span>
                                            <span class="font-13 d-block color-red">
                                                <img src="<?php echo e(asset('frontend/homeimage/horoscope2.svg')); ?>" height="16" width="16" alt="">&nbsp;
                                                ${astrologer.primarySkill ? astrologer.primarySkill.split(',').slice(0, 3).join(' | ') : ''}
                                            </span>
                                            <span class="font-13 d-block exp-language">
                                                <img src="<?php echo e(asset('frontend/homeimage/language-icon.svg')); ?>" height="16" width="16" alt="">&nbsp;
                                                ${astrologer.languageKnown ? astrologer.languageKnown.split(',').slice(0, 3).join(' • ') : ''}
                                            </span>
                                            <span class="font-13 d-block">
                                                <img src="<?php echo e(asset('frontend/homeimage/experience-expert-icon.svg')); ?>" height="16" width="16" alt="">&nbsp; Experience : ${astrologer.experienceInYears} Years
                                            </span>
                                            ${astrologer.emergencyCallStatus ? `
                                              <span class="font-13 font-weight-semi-bold d-flex">
                                                    <img src="<?php echo e(asset('frontend/homeimage/rupee-coin-outline-icon.svg')); ?>" height="16" width="16" alt="">&nbsp; &nbsp;
                                                    <span class="exprt-price mr-2">
                                                        <i class="fa-solid fa-phone mr-1"></i>${astrologer.emergency_audio_charge}
                                                    </span>
                                                    <i class="fa-solid fa-video mt-1 mr-1"></i>${astrologer.emergency_video_charge}
                                                </span>
                                            `: (astrologer.isFreeAvailable) ? `
                                                <span class="font-13 font-weight-semi-bold d-flex">
                                                    <span class="exprt-price">
                                                        <img src="<?php echo e(asset('frontend/homeimage/rupee-coin-outline-icon.svg')); ?>" height="16" width="16" alt="">&nbsp; <del> ${astrologer.charge}</del>/Min
                                                    </span>
                                                    <span class="free-badge text-uppercase color-red ml-2">Free</span>
                                                </span>
                                            ` : `
                                                <span class="font-13 font-weight-semi-bold d-flex">
                                                    <img src="<?php echo e(asset('frontend/homeimage/rupee-coin-outline-icon.svg')); ?>" height="16" width="16" alt="">&nbsp; &nbsp;
                                                    <span class="exprt-price mr-2">
                                                        <i class="fa-solid fa-phone mr-1"></i>${astrologer.charge}
                                                    </span>
                                                    <i class="fa-solid fa-video mt-1 mr-1"></i>${astrologer.videoCallRate}
                                                </span>
                                            `}
                                        </li>
                                    </ul>
                                    <div class="d-flex align-items-end position-relative">
                                        <div class="d-block">
                                            <div class="row">
                                                <div class="psy-review-section col-12">
                                                    <div>
                                                        <span class="colorblack font-12 m-0 p-0 d-block">
                                                            <span style="color: #495057;font-size: 14px;font-weight: bold;">${astrologer.rating}</span>
                                                             <span>
                                                                ${Array.from({ length: 5 }, (_, i) => `
                                                                    ${i < astrologer.rating ? `
                                                                        <i class="fas fa-star filled-star" style="font-size:10px"></i>
                                                                    ` : `
                                                                        <i class="far fa-star empty-star" style="font-size:10px"></i>
                                                                    `}
                                                                `).join('')}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="color: gray;font-size: 12px">${astrologer.totalOrder || 0} Sessions</span>
                                                    </div>
                                                </div>
                                                <div class="col-3 responsiveCallBtn mt-1">
                                                      ${
                                                      astrologer.callStatus === 'Offline' && astrologer.emergencyCallStatus ? `
                                                          <a class="btn-block btn btn-call btn-audio-call align-items-center" role="button" data-toggle="modal" ${!authcheck ? 'data-target="#loginSignUp"' : 'data-target="#callintake"'}>
                                                              <i class="fa-solid fa-phone"></i>&nbsp;Call
                                                          </a>
                                                      ` : (
                                                          astrologer.callStatus === 'Busy' || astrologer.callStatus === 'Offline' || !astrologer.callStatus ? `
                                                              <a class="btn-block btn btn-call align-items-center" style="font-size: 14px !important;">
                                                                  ${astrologer.callStatus || 'Offline'}
                                                              </a>
                                                          ` : `
                                                              <a class="btn-block btn btn-call btn-audio-call align-items-center" role="button" data-toggle="modal" ${!authcheck ? 'data-target="#loginSignUp"' : 'data-target="#callintake"'}>
                                                                  <i class="fa-solid fa-phone"></i>&nbsp;Call
                                                              </a>
                                                          `
                                                      )
                                                    }
                                                </div>
                                                <div class="col-3 responsiveVideoBtn mt-1">
                                                    ${
                                                    astrologer.callStatus === 'Offline' && astrologer.emergencyCallStatus ? `
                                                        <a class="btn-block btn btn-call btn-video-call align-items-center" role="button" data-toggle="modal" ${!authcheck ? 'data-target="#loginSignUp"' : 'data-target="#callintake"'}>
                                                            <i class="fa-solid fa-video"></i>&nbsp;Call
                                                        </a>
                                                    ` : (
                                                        astrologer.callStatus === 'Busy' || astrologer.callStatus === 'Offline' || !astrologer.callStatus ? `
                                                            <a class="btn-block btn btn-call align-items-center" style="font-size: 14px !important;">
                                                                ${astrologer.callStatus || 'Offline'}
                                                            </a>
                                                        ` : `
                                                            <a class="btn-block btn btn-call btn-video-call align-items-center" role="button" data-toggle="modal" ${!authcheck ? 'data-target="#loginSignUp"' : 'data-target="#callintake"'}>
                                                                <i class="fa-solid fa-video"></i>&nbsp;Call
                                                            </a>
                                                        `
                                                    )
                                                   }
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        `;
                        });
                        $('#expert-list').append(html);

                        // Update the nextPageUrl for the next request
                        nextPageUrl = response.getAstrologer.next_page_url;

                        // If there's no next page, remove the button
                        if (!response.getAstrologer.next_page_url) {
                            $btn.remove();
                        } else {
                            $btn.prop('disabled', false).html('Load More');
                        }
                    } else {
                        $btn.remove();
                    }
                },
                error: function(xhr) {
                    console.log("Error:", xhr.responseText);
                }
            });
        });
    });

    function initializeAutocomplete(inputId) {
        var input = document.getElementById(inputId);
        var autocomplete = new google.maps.places.Autocomplete(input);
        var originLatitude = document.getElementById('latitude');
        var originLongitude = document.getElementById('longitude');

        autocomplete.addListener('place_changed', function(event) {
            var place = autocomplete.getPlace();
            if (place.hasOwnProperty('place_id')) {
                if (!place.geometry) {
                    return;
                }
                latitude.value = place.geometry.location.lat();
                longitude.value = place.geometry.location.lng();
            } else {
                var service = new google.maps.places.PlacesService(document.createElement('div'));
                service.textSearch({
                    query: place.name
                }, function(results, status) {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        latitude.value = results[0].geometry.location.lat();
                        longitude.value = results[0].geometry.location.lng();
                    }
                });
            }
        });
    }
    // Initialize when the page loads
    initializeAutocomplete('BirthPlace');
</script>


<script>
    $(document).ready(function() {


        <?php if($getAstrologer): ?>
        $(document).on('click', '.btn-audio-call', function() {

            var astrologerCard = $(this).closest('.psychic-card');
            var astrologerId = astrologerCard.data('astrologer-id');

            $('#astroId').val(astrologerId);
            var astrologerId = $('#astroId').val();

            $("#call_type").val(10);
            var astroChargeText = astrologerCard.find('.exprt-price').text().trim();

            // Extract numerical value from the charge text
            var astroCharge = parseFloat(astroChargeText.match(/[\d.]+/));

            $('#astroCharge').val(astroCharge);

        });


        $(document).on('click', '.btn-video-call', function() {
            var astrologerCard = $(this).closest('.psychic-card');
            var astrologerId = astrologerCard.data('astrologer-id');
            $('#astroId').val(astrologerId);
            var astrologerId = $('#astroId').val();

            $("#call_type").val(11);
            var astroChargeText = astrologerCard.find('.exprt-price').text().trim();

            // Extract numerical value from the charge text
            var astroCharge = parseFloat(astroChargeText.match(/[\d.]+/));

            $('#astroCharge').val(astroCharge);

        });

        $(document).on('click', '.btn-chat', function() {
            var astrologerCard = $(this).closest('.psychic-card');
            var astrologerId = astrologerCard.data('astrologer-id');
            $('#astroId').val(astrologerId);
            var astrologerId = $('#astroId').val();
            $("#call_type").val(12);
            var astroChargeText = astrologerCard.find('.exprt-price').text().trim();
            var astroCharge = parseFloat(astroChargeText.match(/[\d.]+/));

            $('#astroCharge').val(astroCharge);
        });
        <?php endif; ?>


        $('#callintakeBtn').click(function(e) {
            e.preventDefault();

            var form = document.getElementById('callintakeForm');
            if (form.checkValidity() === false) {
                form.reportValidity();
                return;
            }

            <?php

            $session = new Session();
            $token = $session->get('token');
            ?>



            $('#callintakeBtn').hide();
            $('#callloaderintakeBtn').show();
            setTimeout(function() {
                $('#callintakeBtn').show();
                $('#callloaderintakeBtn').hide();
            }, 3000);

            astrocharge = $("#astrocharge").val();



            <?php
            $wallet_amount = '';
            if (authcheck()) {
                $wallet_amount = authcheck()['totalWalletAmount'];
            }
            ?>

            var formData = $('#callintakeForm').serialize();

            // Parse form data as URL parameters
            var urlParams = new URLSearchParams(formData);
            var call_duration = parseInt(urlParams.get('call_duration'));

            var call_duration_minutes = Math.ceil(call_duration / 60);

            var total_charge = astrocharge * call_duration_minutes;

            <?php if($getAstrologer): ?>
            var isFreeAvailable = "<?php echo e($isFreeAvailable); ?>";

            var wallet_amount = "<?php echo e($wallet_amount); ?>";
            <?php endif; ?>

            $.ajax({
                url: "<?php echo e(route('api.checkCallSessionTaken', ['token' => $token])); ?>",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (!response.recordList)
                        callRequestWallet();
                    else
                        toastr.error('Your request is already there');

                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });

            function callRequestWallet() {
                // Check if free chat is available and wallet has sufficient balance
                if (isFreeAvailable != true) {
                    if (total_charge <= wallet_amount) {
                        AddCallRequestFunc(formData)
                    } else {
                        toastr.error('Insufficient balance. Please recharge your wallet.');
                        window.location.href = "<?php echo e(route('front.walletRecharge')); ?>";
                    }
                } else {
                    AddCallRequestFunc(formData)
                }
            }


            function AddCallRequestFunc(formData) {
                $.ajax({
                    url: "<?php echo e(route('api.addCallRequest', ['token' => $token])); ?>",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $.ajax({
                            url: "<?php echo e(route('api.intakeForm', ['token' => $token])); ?>",
                            type: 'POST',
                            data: formData,
                            success: function(response) {

                                setTimeout(function() {
                                    toastr.success(
                                        'Call Request Sent ! you will be notified if <?php echo e(strtolower($professionTitle)); ?> accept your request.'
                                    );
                                    $('#callintakeForm').modal('hide');
                                }, 2000);
                            },
                            error: function(xhr, status, error) {
                                toastr.error(xhr.responseText);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                            toastr.error(xhr.responseJSON.recordList.message);
                        } else {
                            toastr.error(xhr.responseText);
                        }
                    }
                });

            }
        });
    });

    document.getElementById('clearButton').addEventListener('click', function() {
        window.location.href = "<?php echo e(route('front.talkList')); ?>";
    });
</script>



  <style>
    :root{
      --banner-height: 60vh; /* adjust for hero area */
      --overlay-color: rgba(0,0,0,0.35);
      --accent: #ffb400;
    }

    *{box-sizing:border-box}
    body{margin:0;font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial}

    /* Banner wrapper */
    .banner{
      position:relative;
      width:100%;
      height:var(--banner-height);
      min-height:550px;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
    }

    /* Background image layer with Ken Burns style animation (infinite loop) */
    .banner__bg{
      position:absolute;
      inset:0; /* top:0; right:0; bottom:0; left:0 */
      background-image: url('https://s1.dmcdn.net/v/WxOZW1cqnKTAoF-Td/x1080');
      background-size:cover;
      background-position:center center;
      transform-origin:center center;
      will-change:transform, filter;
      z-index:0;
      filter:brightness(0.9) contrast(1.02);
      animation: kb 10s linear infinite;
    }

    /* subtle slow pan + zoom; alternate to keep motion interesting */
    @keyframes kb{
      0%{transform:scale(1) translateY(0px) translateX(0px)}
      25%{transform:scale(1.06) translateY(-8px) translateX(-6px)}
      50%{transform:scale(1.12) translateY(6px) translateX(6px)}
      75%{transform:scale(1.08) translateY(-4px) translateX(4px)}
      100%{transform:scale(1) translateY(0px) translateX(0px)}
    }

    /* overlay for text contrast */
    .banner__overlay{
      position:absolute;inset:0;z-index:1;
      background:linear-gradient(180deg, rgba(0,0,0,0.25) 0%, rgba(0,0,0,0.45) 70%);
      pointer-events:none;
    }

    /* content container */
    .banner__content{
      position:relative;z-index:2;padding:2rem;max-width:1200px;width:100%;display:flex;align-items:center;gap:2rem;
      flex-wrap:wrap;justify-content:flex-start;
    }

    .banner__text{
      flex:1 1 420px;
      min-width:220px;
    }

    h1{margin:0 0 0.5rem;font-size:clamp(1.4rem, 3.8vw, 2.8rem);line-height:1.05;letter-spacing: -0.02em}
    p.lead{margin:0 0 1.2rem;font-size:clamp(0.95rem, 1.5vw, 1.1rem);opacity:0.95}

    /* animated entrance for heading and paragraph */
    .anim-slide-up{animation:slideUp 1s cubic-bezier(.2,.9,.3,1) both 0.15s}
    .anim-fade-in{animation:fadeIn 1s cubic-bezier(.2,.9,.3,1) both 0.4s}

    @keyframes slideUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}

    /* CTA button */
    .cta{
      display:inline-flex;align-items:center;gap:.65rem;padding:.72rem 1.05rem;border-radius:10px;background:var(--accent);color:#000;text-decoration:none;font-weight:600;box-shadow:0 6px 20px rgba(0,0,0,0.24);
      transform-origin:center;animation:ctaPulse 3s ease-in-out infinite;
    }
    @keyframes ctaPulse{0%{transform:scale(1)}50%{transform:scale(1.03)}100%{transform:scale(1)}}

    /* small visual card on right optional */
    .banner__card{
      flex:0 0 300px;min-width:220px;background:rgba(255,255,255,0.06);backdrop-filter:blur(6px);padding:1rem;border-radius:12px;border:1px solid rgba(255,255,255,0.06);
      box-shadow:0 8px 30px rgba(0,0,0,0.35);align-self:center
    }
    .banner__card h3{margin:0 0 .4rem;font-size:1.05rem}
    .banner__card small{opacity:.9}

    /* responsive tweaks */
    @media (max-width:880px){
      :root{--banner-height:48vh}
      .banner{align-items:flex-end}
      .banner__card{width:100%;order:3}
    }
    @media (max-width:560px){
      :root{--banner-height:40vh}
      .banner__content{padding:1.2rem}
      .banner__text{flex-basis:100%}
    }

    /* Respect user's reduced motion preference */
    @media (prefers-reduced-motion: reduce){
      .banner__bg, .cta{animation:none}
    }
  </style>


  <div class="container my-5 mt-5">
            <div class="row g-4">
                <!-- Kundli Analysis Section -->
                <div class="col-md-6">
                    <div class="kundli-card shadow d-none d-md-block" data-aos="fade-right" data-aos-delay="200">
                        <a href="<?php echo e(route('front.getkundali')); ?>"><img src="<?php echo e(asset('frontend/homeimage/home-analyze.png')); ?>" alt="Sage Icon"></a>
                    </div>
                </div>
            
                <!-- Panchang Section -->
                <div class="col-md-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="panchang-card">
                        <h3 class="font-30" style="color: #fed8aa">Today's Panchang</h3>
                        <p><strong class="font-22"><?php echo e($Tpanchangs['response']['date'] ?? date('d-m-Y h:i a')); ?></strong></p>

                        <div class="row g-2">
                            <div class="col-6 p-1">
                                <div class="time-card">
                                    🌅 Sunrise : <?php echo e($Tpanchangs['response']['advanced_details']['sun_rise'] ?? '7:00:50 AM'); ?>

                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="time-card">
                                    🌇 Sunset : <?php echo e($Tpanchangs['response']['advanced_details']['sun_set'] ?? '6:44:15 PM'); ?>

                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="time-card">
                                    🌙 Moonrise : <?php echo e($Tpanchangs['response']['advanced_details']['moon_rise'] ?? '7:45:26 AM'); ?>

                                </div>
                            </div>
                            <div class="col-6 p-1">
                                <div class="time-card">
                                    🌓 Moonset : <?php echo e($Tpanchangs['response']['advanced_details']['moon_set'] ?? '8:09:49 PM'); ?>

                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php
                            $todaysDate = date('D M d Y');
                            ?>

                            <p><strong>Month :</strong> Amanta :
                                <?php echo e($Tpanchangs['response']['advanced_details']['masa']['amanta_name'] ?? 'Phalguna'); ?></p>
                            <p><strong>Samvat :</strong> Vikram
                                <?php echo e($Tpanchangs['response']['advanced_details']['years']['vikram_samvaat'] ?? '2081'); ?> -
                                <?php echo e($Tpanchangs['response']['advanced_details']['years']['vikram_samvaat_name'] ?? 'Pingala'); ?></p>
                            <p><strong>Tithi :</strong> <?php echo e($Tpanchangs['response']['tithi']['name'] ?? 'Dwitiya'); ?> till
                                <?php echo e($Tpanchangs['response']['tithi']['end'] ?? "$todaysDate 1:18:41 PM"); ?></p>
                            <p><strong>Nakshatra :</strong> <?php echo e($Tpanchangs['response']['nakshatra']['name'] ?? 'PurvaBhadra'); ?> till
                                <?php echo e($Tpanchangs['response']['nakshatra']['end'] ?? "$todaysDate 11:44:39 PM"); ?></p>
                            <p><strong>Karan :</strong> <?php echo e($Tpanchangs['response']['karana']['name'] ?? 'Balava'); ?> till
                                <?php echo e($Tpanchangs['response']['karana']['end'] ?? "$todaysDate 10:57:29 AM"); ?></p>
                            <p><strong>Yog :</strong> <?php echo e($Tpanchangs['response']['yoga']['name'] ?? 'Sadhya'); ?> till
                                <?php echo e($Tpanchangs['response']['yoga']['end'] ?? "$todaysDate 3:58:31 PM"); ?></p>
                            <div>
                            <a href="<?php echo e(route('front.getPanchang')); ?>" class="detailed-link p-2 ">Detailed Panchang →</a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>



    <?php if(isset($liveAstrologer ) && count($liveAstrologer)>0): ?>
    <div class="astroway-live-astrologers slider-bullets py-2 my-md-5 pt-md-5">
        <div class="container">
            <div class="row pb-2">
                <div class="col-sm-12">
                    <h2 class="text-center text-black py-3 font-28">LIVE SESSIONS</h2>
                    <p class="text-md-center mb-1">Connect with top-rated <?php echo e(ucfirst($professionTitle)); ?>s through live sessions for
                        instant solutions</p>
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-sm-12">
                    <div class="owl-carousel owl-theme owl-blur owl-mobile">
                        <?php $__currentLoopData = $liveAstrologer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $live): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item gif-animation-enable mb-3"
                                style="background:url('<?php echo e($live->profileImage ? '/' . $live->profileImage : asset('frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png')); ?>')">
                                <a <?php if(authcheck()): ?> href="<?php echo e(route('front.LiveAstroDetails', ['astrologerId' => $live->astrologerId])); ?>" <?php else: ?> data-toggle="modal" data-target="#loginSignUp" <?php endif; ?>"
                                    class="text-white">
                                    <div class="position-relative live-expert">
                                        <div class="position-absolute top-part">
                                            <span
                                                class="bg-red px-2 text-white d-inline-flex align-items-center rounded font-12"><i
                                                    class="fa fa-circle font-11 mr-1"></i>Live</span>
                                        </div>
                                        <div class="position-absolute bottom-part w-100 p-2">
                                            <div class="d-flex h-100 align-items-center">
                                                <div
                                                    class="position-relative profile-pic bg-white d-none d-md-flex align-items-center justify-content-center">
                                                    <?php if($live->profileImage): ?>
                                                    <img src="<?php echo e(Str::startsWith($live->profileImage, ['http://','https://']) ? $live->profileImage : '/' . $live->profileImage); ?>" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('<?php echo e($live->profileImage); ?>')" width="38"
                                                            height="38" loading="lazy"/>
                                                    <?php else: ?>
                                                        <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png')); ?>"
                                                            width="38" height="38" loading="lazy" />
                                                    <?php endif; ?>
                                                </div>
                                                <div class=" ml-2">
                                                    <p class="mb-0 pb-0 text-white font-16 text-capitalize">
                                                        <?php echo e($live->name); ?>

                                                    </p>
                                                    <p class="mb-0 pb-0 text-yellow  font-12 text-capitalize"><?php echo e(explode(',', $live->skill_names)[0]); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="text-center pt-2">
                        <a href="<?php echo e(route('front.getLiveAstro')); ?>"
                            class="btn view-more colorblack font-weight-semi-bold">
                            View More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<!-- ================= HOROSCOPE SECTION START ================= -->
<?php if(isset($horosign) && count($horosign) > 0): ?>
<section class="as_padderTop80 as_padderBottom30" id="redirecthoroscope">
    <div class="container">
        <div class="col-lg-12 col-md-12 text-center">
            <h2 class="as_heading" data-aos="fade-up">Horoscope Prediction</h2>
            <p class="size_number" data-aos="fade-up">It is exciting to explore the possibilities of the future. With
                horoscope predictions, you can gain daily, weekly, or even yearly insights into how celestial movements
                impact your life.</p>
        </div>
        <div class="row">
            <?php $__currentLoopData = $horosign; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-3 col-lg-2 mb-3" data-aos="fade-up">
                    <div class="daily_horoscope_box text-center" data-horoscope="1" data-rasi="daily-<?php echo e($sign->slug); ?>">
                        <a href="<?php echo e(route('front.dailyHoroscope', ['slug' => $sign->slug])); ?>" class="text-decoration-none text-dark">
                            <img
                                class="rasiImage img-fluid mb-2"
                                src="<?php echo e(Str::startsWith($sign->image, ['http://','https://']) ? $sign->image : '/' . $sign->image); ?>"
                                onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                alt="<?php echo e($sign->name); ?>"
                                onclick="openImage('<?php echo e($sign->image); ?>')"
                            />
                            <h5 class="fw-semibold"><?php echo e($sign->name); ?></h5>
                            
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<style>
    /*Horoscope style start */
    .as_padderBottom30 {
        padding-bottom: 30px;
    }

    .as_padderTop80 {
        padding-top: 80px;
    }

    .daily_horoscope_box {
        text-align: center;
        padding-bottom: 10px;
        display: block;
        box-shadow: 0 0 12px #9289894f;
        margin-top: 65px;
        border-radius: 5px 30px 5px 30px;
        cursor: pointer;
        background: azure;
    }

    .daily_horoscope_box img {
        height: 90px;
        margin-top: -45px;
        filter: drop-shadow(0.35px 0.35px 4.4px rgba(0, 0, 0, 0.3));
        border-radius: 100px;
        border: 1px solid rosybrown;
        padding: 5px;
    }

    /*Horoscope style end*/
</style>
<!-- ================= HOROSCOPE SECTION END ================= -->

    <?php if(isset($astrologer) && count($astrologer )>0): ?>
    <div class="astroway-astrologers py-5 bg-astrologer-pink-light">
        <div class="container">
            <div class="row pb-2">
                <div class="col-sm-12">
                    <h2 class="text-center text-black py-3 font-28 heading" data-aos="fade-left">OUR <?php echo e(ucfirst($professionTitle)); ?>S</h2>
                    <p class="text-md-center mb-1">Get in touch with the best Online <?php echo e(ucfirst($professionTitle)); ?>s, anytime &amp;
                        anywhere!</p>
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-sm-12 "  data-aos="fade-up">
                    <div class="owl-carousel owl-theme owl-blur owl-mobile">
                        <?php $__currentLoopData = $astrologer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $astrologer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item p-3 mb-3 expertOnline bg-white psychic-card overflow-hidden "
                                data-psychic-id="<?php echo e($astrologer->id); ?>">
                                 <?php if($astrologer->is_boosted == 1): ?>
                                <span class=" must-try-badge font-11 position-absolute font-weight-semi text-center align-items-center justify-content-center text-white ">Sponsored</span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('front.astrologerDetails',  ['slug' => $astrologer['slug']])); ?>">
                                    <div class="astro-profile">
                                        <div>
                                            <?php if($astrologer->profileImage): ?>
                                            <img class="img-fluid" src="<?php echo e(Str::startsWith($astrologer->profileImage, ['http://','https://']) ? $astrologer->profileImage : '/' . $astrologer->profileImage); ?>" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('<?php echo e($astrologer->profileImage); ?>')" />
                                            <?php else: ?>
                                                <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png')); ?>"
                                                    class="img-fluid">
                                            <?php endif; ?>
                                        </div>
                                        <p class="astro-name text-center colorblack text-capitalize"
                                            data-toggle="tooltip" title="<?php echo e($astrologer->name); ?>"
                                            style="white-space: nowrap;text-overflow: ellipsis;display: block;overflow:hidden">
                                            <?php echo e($astrologer->name); ?></p>
                                    </div>
                                    <div>
                                        <p class="mb-0 colorblack text-center">Reviews: <span
                                                class="color-red"><?php echo e($astrologer->reviews); ?></span></p>
                                        <p class="mb-0 text-center">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <?php if($i <= $astrologer->rating): ?>
                                                    <i class="fas fa-star filled-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star empty-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($stories) && count($stories)>0): ?>
        <div class="container mt-5 mb-5<?php echo e(empty($liveAstrologer) ? 'mb-5' : ''); ?>">

            <h2 class="text-center text-black py-3 heading font-28">Stories</h2>
            <p class="text-center mb-4">See Stories of top-rated <?php echo e(ucfirst($professionTitle)); ?>s</p>
            <div class="stories-container">
                <?php $__currentLoopData = $stories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $story): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="story <?php echo e($story->allStoriesViewed > 0 ? 'viewed' : ''); ?>" data-astrologer-id="<?php echo e($story->astrologerId); ?>" data-astrologer-name="<?php echo e($story->name); ?>" data-astrologer-profile="<?php echo e($story->profileImage); ?>">
                    <?php if($story->profileImage): ?>
                    <img  src="<?php echo e(Str::startsWith($story->profileImage, ['http://','https://']) ? $story->profileImage : '/' . $story->profileImage); ?>" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('<?php echo e($story->profileImage); ?>')" />
                    <?php else: ?>
                    <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png')); ?>" alt="<?php echo e($story->name); ?>">
                    <?php endif; ?>
                    <p><?php echo e($story->name); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Story Modal -->
    <div class="modal fade" id="storyModal" tabindex="-1" aria-labelledby="storyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img id="astrologerProfileImage" src="" alt="Astrologer Profile Image" class="rounded-circle" style="height: 40px;width:40px">
                    <span class="modal-title mt-2 ml-2" id="astrologerName"></span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators" id="carouselIndicators"></ol>
                        <div class="carousel-inner" id="carouselInner"></div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev" style="margin-top:82px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next"  style="margin-top:82px;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php if(!empty($astrologyVideo) && count($astrologyVideo)): ?>
        <section class="py-5 bg-white" id="calculator" style="background:url('<?php echo e(asset('frontend/homeimage/videobackground.jpeg')); ?>');">

            <div class="container-fluid">
                <h2 class="text-center text-black py-3 font-28">
                    Astrology Videos
                </h2>

                <div class="marquee-wrapper overflow-hidden position-relative">
                    <div class="marquee d-flex">

                        <?php $__currentLoopData = collect($astrologyVideo)->concat($astrologyVideo); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="javascript:void(0);"
                            class="video-link mx-2"
                            data-video="<?php echo e($video->youtubeLink); ?>"
                            data-description="<?php echo e(\Illuminate\Support\Str::words($video->description, 30, '...')); ?>"
                            data-toggle="modal"
                            data-target="#videoModal">

                                <div class="video-card position-relative">
                                    <img
                                        class="video-thumbnail img-fluid"
                                        style="height:160px"
                                        src="<?php echo e($video->cover_image_url); ?>"
                                        alt="<?php echo e($video->videoTitle); ?>"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='<?php echo e(asset('build/assets/images/person.png')); ?>';"
                                    />

                                    <img
                                        class="position-absolute youtube-icon"
                                        style="cursor:pointer"
                                        src="<?php echo e(asset('frontend/homeimage/youtube.svg')); ?>"
                                        alt="YouTube"
                                    />

                                    <div class="video-title text-center mt-2">
                                        <?php echo e($video->videoTitle); ?>

                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal fade mt-5" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 30px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" id="videoIframe" src="" allowfullscreen></iframe>
                    </div>
                    <h3 class="p-3 bg-success text-white">Video Description</h3>
                    <div class="video-description mt-2 p-3" id="videoDescription"></div>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($Productlist) && count($Productlist) > 0): ?>
        <div class="container pt-5 pb-5" data-aos="fade-up">

            <div class="d-flex justify-content-between mt-4 mb-4 align-items-center flex-column flex-md-row">
                <h2 class="text-left text-black py-2 font-28 text-center text-md-left" style="max-width: 25rem;">
                    Shop Genuine and Energised Products by <?php echo e(ucfirst($appname)); ?>

                </h2>
                <a href="<?php echo e(route('front.getproducts')); ?>" class="button-blog py-2 mt-2 mt-md-0">See All Products</a>
            </div>

            <div class="row">
                <?php
                    $colors = [
                        0 => '#81ecec',
                        1 => '#fed7aa',
                        2 => '#fab1a0',
                    ];
                ?>

                <?php $__currentLoopData = $Productlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $color = $colors[$key] ?? '#ffffff';
                    ?>

                    <div class="col-md-4 mb-4">
                        <div class="vedic-card"
                            style="background-color: <?php echo e($color); ?>; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">

                            <h2 class="font-weight-semi text-start font-22 mb-2">
                                <?php echo e($product->name); ?>

                            </h2>

                            <p class="vedic-description text-start font-16 mb-3">
                                <?php echo \Illuminate\Support\Str::words($product->features, 15); ?>

                            </p>

                            <span class="vedic-product-img"
                                style="mask-image: linear-gradient(white, white, transparent);">

                                <img
                                    class="vedic-image mb-3"
                                    style="max-width:100%; height:200px; object-fit:contain;"
                                    src="<?php echo e($product->profile_image_url); ?>"
                                    alt="<?php echo e($product->name); ?>"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='<?php echo e(asset('build/assets/images/person.png')); ?>';"
                                    onclick="openImage('<?php echo e($product->profile_image_url); ?>')"
                                />
                            </span>

                            <div class="vedic-product-footer">
                                <p class="vedic-price mb-3">
                                    Selling at

                                    <?php if($walletType === 'coin'): ?>
                                        <img src="<?php echo e(asset($coinIcon)); ?>" alt="Coin" width="15">
                                    <?php else: ?>
                                        <?php echo e($currency['value']); ?>

                                    <?php endif; ?>

                                    <?php echo e($product->amount); ?>

                                </p>

                                <a href="<?php echo e(route('front.getproductDetails', $product->slug)); ?>"
                                class="vedic-button btn btn-dark w-100">
                                    Buy Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        </div>
    <?php endif; ?>


    <?php if(!empty($astrotalkInNews) && count($astrotalkInNews)): ?>
        <section class="backimage mt-4 mb-4">
            <h3 class="text-center pt-3 font-28">
                As Seen On
            </h3>

            <div class="owl-carousel owl-theme news-sections m-auto container p-3">
                <?php $__currentLoopData = $astrotalkInNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="item">
                        <div class="video-card">

                            <img
                                class="video-thumbnail"
                                style="height:160px;"
                                src="<?php echo e($news->banner_image_url); ?>"
                                alt="<?php echo e($news->channel); ?>"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='<?php echo e(asset('build/assets/images/person.png')); ?>';"
                            />

                            <div class="video-title">
                                <?php echo e($news->channel); ?>

                            </div>

                            <a href="<?php echo e(route('news.show', $news->id)); ?>"
                            class="btn btn-primary btn-sm d-block">
                                View More
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>


<style>
    .decoration:hover{
         text-decoration: none!important;
      color: black!important;

    }
</style>
    <?php if(isset($blog) && count($blog) > 0): ?>
    <section class="py-5 bg-white">
        <div class="container ">
            <div class="d-flex justify-content-between">
                <h2 class="text-left text-black py-2 font-28">Latest Blogs</h2>
                <a href="<?php echo e(route('front.getBlog')); ?>" class="button-blog text-right">See All Articles</a>
            </div>
            <div class="row justify-content-strat">

                    <?php $__currentLoopData = $blog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bloglist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mt-4">
                            <a href="<?php echo e(route('front.getBlogDetails', $bloglist->slug)); ?>"
                            class="text-decoration-none">

                                <div class="product-card parad-shivling shadow-sm overflow-hidden p-0">
                                    <div class="position-relative" style="height:250px;">
                                        <img
                                            class="product-image position-absolute"
                                            style="top:0; left:0; width:100%; height:100%; object-fit:cover;"
                                            src="<?php echo e($bloglist->blog_image_url); ?>"
                                            alt="<?php echo e($bloglist->title); ?>"
                                            loading="lazy"
                                            onerror="this.onerror=null;this.src='<?php echo e(asset('build/assets/images/person.png')); ?>';"
                                            onclick="openImage('<?php echo e($bloglist->blog_image_url); ?>')"
                                        />
                                    </div>

                                    <div class="p-3 text-left">
                                        <h3 class="font-weight-700">
                                            <?php echo e($bloglist->title); ?>

                                        </h3>

                                        <p class="text-dark">
                                            <?php echo \Illuminate\Support\Str::words($bloglist->description, 15); ?>

                                        </p>

                                        <span class="mt-1 text-blue-500 text-sm read-more">
                                            Read More →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            </div>
        </div>
    </section>
    <?php endif; ?>
    <div class="py-5 bg-pink-light">
        <div class="container">
            <div class="row ">
                <div class="col-sm-12">
                    <h2 class="heading text-center">What Is Astrology?<span onclick="toggleIcon(this)" class="ml-3" data-toggle="collapse"
                            href="#collapse-faq" role="button" aria-expanded="false"
                            aria-controls="collapse-faq"><i class="fa fa-chevron-down color-red"></i></span></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="collapse py-4 font-14" id="collapse-faq">
                        <h3 class="font-weight-bold">Astrology Is The Language Of The Universe</h3>
                        <p>Astrology predictions are based on the position and movements of planets and
                            celestial bodies in the Universe that impact our life quality. This can be studied
                            by creating an offline or online horoscope of individuals. This affects not only the
                            people but also controls the occurrence of certain events happening in the sublunar
                            world.</p>
                        <p>Some may call it pseudo-science, and others call it predictive science. The science
                            that is Astrology inspires people to know the various aspects of their life and take
                            it in the right direction. From making life predictions on the basis of a detailed
                            Kundali or telling you about the near future through daily, weekly and monthly
                            horoscopes, Astrology is the medium through which you can get a glimpse of what the
                            future will bring for you.</p>
                        <p>There is one aspect of offline and online Astrology prediction where the impacts of
                            planetary transition can be seen. And when it is related to the Zodiacs, it happens
                            as various planets cross the sectors of each zodiac in the sky. It impacts the
                            natives of different zodiacs differently. And one more way is by analyzing the
                            planetary position in various houses of one&#39;s Kundli.</p>
                        <p>Astrology reading is quite extensive. It is all about studying the 9 planets placed
                            in the twelve houses of one&#39;s Kundli and their impact on their life. These
                            planets are the Sun, Moon, Mercury, Venus, Mars, Jupiter, Saturn, Rahu, and Ketu.
                            Some of these planets positively impact human life, and others affect it adversely.
                            It depends on their house placement.</p>
                        <p>For example, it is not always a compulsion that Saturn will bring negative impacts or
                            Jupiter will be a positive one.</p>
                        <p>Every house in the Kundli represents a different aspect of one&#39;s life. Similarly,
                            Sun Signs, Moon Signs, Ascendants, and Descendants have their own significance. So
                            it is not a confined subject, and the best way to know your future through the power
                            of Astrology is to talk to an online <?php echo e(ucfirst($professionTitle)); ?> and get a detailed analysis of your
                            online horoscope covering every aspect of your life.</p>

                        <h3 class="font-weight-bold">Astrology Predictions And Its Benefits</h3>
                        <p>Offline and online Astrology predictions have the power to forecast the future by
                            analyzing the positions of the planets as they move and studying their impact on
                            your life.</p>
                        <p>An online horoscope is essentially a blueprint of your life that can help you gain
                            clarity about the different aspects of your life, your personality and your future.
                            Although there are several benefits of Astrological predictions, the best one
                            remains timely guidance, and remedial suggestions to help avoid any unfavorable
                            events coming your way. Or even if not eliminate them altogether, the offline and
                            online Astro remedies can at least minimize their impacts. It is best if the
                            guidance comes from the best <?php echo e(ucfirst($professionTitle)); ?> in India.</p>
                        <p>You can take advantage of staying a step ahead of time in every aspect of your life,
                            be it love, money, career, marriage, family, or anything else. Online Astrology has
                            the power to show you the right path that will lead you towards a successful and
                            happy life.</p>

                        <h3 class="font-weight-bold">How Online Astrology Services Can Benefit You</h3>
                        <p>You know how well you can take your life in the right direction with right Astro
                            guidance, so why not get it from the comfort of your home.</p>

                        <p>Keeping the convenience, comfort and flexibility in mind, <?php echo e(ucfirst($appname)); ?> has
                            introduced the best online Astrology consultation services. You can choose from
                            online <?php echo e(ucfirst($professionTitle)); ?>s, numerologists, palmists, and <a href="#" target="_blank">tarot
                                reading experts</a> to get answers for your concerns.
                            This has been done while keeping various factors in mind that can benefit you.</p>
                        <ul class="pl-3">
                            <li>It is the most hassle-free way to connect with the best <?php echo e(ucfirst($professionTitle)); ?>s.</li>
                            <li>Online Astrology services are the most time-saving and affordable way to connect
                                with top <?php echo e(ucfirst($professionTitle)); ?>s and get consultations, anytime and anywhere.</li>
                            <li>It makes it convenient for people to talk to an <?php echo e(ucfirst($professionTitle)); ?> openly as your
                                privacy and confidentiality is strictly maintained.</li>
                            <li>You can choose the best <?php echo e(ucfirst($professionTitle)); ?> online among nearly 100+ <?php echo e(ucfirst($professionTitle)); ?>s that you
                                think matches your requirements perfectly.</li>
                        </ul>


                        <h3 class="font-weight-bold">Online Astrology Consultation Services By <?php echo e(ucfirst($appname)); ?>

                        </h3>
                        <p><?php echo e(ucfirst($appname)); ?> has established its footprints in the online Astrology services,
                            helping people get through their life problems. This is done by the best online
                            <?php echo e(ucfirst($professionTitle)); ?>s who are experienced and renowned in this domain. Our <?php echo e(ucfirst($professionTitle)); ?>s are
                            available 24/7 to help people with their Astro advice on the best website for
                            Astrology.</p>
                        <p><?php echo e(ucfirst($appname)); ?> strives to provide the best Astrology consultation services by the best
                            <?php echo e(ucfirst($professionTitle)); ?>s. Our professional <?php echo e(ucfirst($professionTitle)); ?>s are not only limited to providing guidance
                            and insights into various aspects of your life. They are also your friend and
                            partner to get you through difficult situations. Another thing is that they are not
                            only traditional <?php echo e(ucfirst($professionTitle)); ?>s. There are also tarot reading experts and <a href="#"
                                target="_blank">numerologists</a> to give you a range of Astrology services.
                        </p>
                        <p>You know that you need an online Astrology reading session at <?php echo e(ucfirst($appname)); ?>, so you
                            should understand how it works.</p>
                        <p>Here are the steps you can follow to reach the expert <?php echo e(ucfirst($professionTitle)); ?>s on the best
                            <?php echo e(ucfirst($professionTitle)); ?> site.</p>
                        <ul class="pl-3">
                            <li>Download the <?php echo e(ucfirst($appname)); ?> app</li>
                            <li>Sign up with your basic details</li>
                            <li>Enjoy your free session of online Astrology consultation</li>
                            <li>Recharge your wallet</li>
                            <li>Choose the best <?php echo e(ucfirst($professionTitle)); ?> online with whom you want to consult</li>
                            <li>Enjoy your live chat/call session with the best online <?php echo e(ucfirst($professionTitle)); ?>s</li>
                        </ul>
                        <p>So are you now confused about how you can choose the best <?php echo e(ucfirst($professionTitle)); ?> for your
                            session? The one who can make the most accurate online horoscope? Here are the
                            things to consider.</p>
                        <p>First of all, categorize your query based on various issues like love, finance,
                            family, etc. Then look for the expert <?php echo e(ucfirst($professionTitle)); ?>s of that particular aspect and
                            choose them based on the ratings they get from their clients. These ratings are
                            based on the quality of the session. Or you can go a step further and read their
                            descriptions where their experience and expertise are mentioned.</p>
                        <p>That&#39;s how you will get in touch with the expert <?php echo e(ucfirst($professionTitle)); ?> that will provide the
                            guidance you need for all your life problems along with the most effective
                            solutions.</p>


                        <h3 class="font-weight-bold">Online <?php echo e(ucfirst($professionTitle)); ?>s Of <?php echo e(ucfirst($appname)); ?></h3>
                        <p><?php echo e(ucfirst($appname)); ?> connects you with India&#39;s top <?php echo e(ucfirst($professionTitle)); ?>s!</p>
                        <p>We at <?php echo e(ucfirst($appname)); ?> consider it our responsibility to connect you with India&#39;s
                            best online <?php echo e(ucfirst($professionTitle)); ?>s. And to make sure that you get the most satisfactory
                            experience after each session, whether through live chat or call, we are highly
                            particular about choosing our <?php echo e(ucfirst($professionTitle)); ?>s.</p>
                        <p>There are a lot of factors that we consider before an <?php echo e(ucfirst($professionTitle)); ?> comes on board with
                            us.</p>
                        <ul class="pl-3">
                            <li>Educational qualifications</li>
                            <li>Area of expertise</li>
                            <li>Years of experience</li>
                            <li>Method of practice (Astrology, numerology, tarot card reading, etc.)</li>
                        </ul>
                        <p>We make sure that our clients get what they expect. So, we ensure that only the best
                            and the most knowledgeable <?php echo e(ucfirst($professionTitle)); ?>s are associated with us. <?php echo e(ucfirst($professionTitle)); ?>s go
                            through a multi-layer screening process to become a part of our community. And they
                            come from all over the country. All the <?php echo e(ucfirst($professionTitle)); ?>s who are associated with us are
                            certified and verified for their area of expertise. We leave no stone unturned to
                            ensure you get the best guidance by the best <?php echo e(ucfirst($professionTitle)); ?>s.</p>
                        <p>You can get their guidance regarding <a href="#" target="_blank">your online
                                horoscope</a>, Kundli matching, general online
                            predictions, etc.</p>
                        <p>Search for the phrase &#39;the best <?php echo e(ucfirst($professionTitle)); ?> near me,&#39; and you will get the
                            relevant results wherever you are. But with <?php echo e(ucfirst($appname)); ?>, you will still find the
                            best <?php echo e(ucfirst($professionTitle)); ?>s and get their guidance from the comfort of your home.</p>
                        <p>So whenever you consult with an expert <?php echo e(ucfirst($professionTitle)); ?> at <?php echo e(ucfirst($appname)); ?>, you get only the
                            best!</p>


                        <h3 class="font-weight-bold">Online Astrology Predictions Categories</h3>
                        <p>You can discuss anything troubling you with a professional <?php echo e(ucfirst($professionTitle)); ?>. Still, in case
                            you need clarity, here are the buckets of specific categories in which you can put
                            your queries.</p>

                        <ul class="pl-3">
                            <li>
                                <p class="font-weight-bold mb-0">Love and relationships</p>
                                <p>Here, you can <a href="#" target="_blank">ask an <?php echo e(ucfirst($professionTitle)); ?> any question
                                        related to your
                                        relationship</a>, whether past, present, or future. It also answers the
                                    question about your ex's feelings or maybe issues related to cheating, etc.
                                </p>
                            </li>
                            <li>
                                <p class="font-weight-bold mb-0">Marriage and family</p>

                                <p><a href="#" target="_blank">Ask questions related to your married life</a>.
                                    It
                                    taps the issues related to infidelity, general future, or even second
                                    marriage.</p>
                            </li>
                            <li>
                                <p class="font-weight-bold mb-0">Career and job</p>
                                <p>Under this category, all the questions related to your work will be placed.
                                    It can be anything from workplace conflicts to promotions to being confused
                                    between two options.</p>
                            </li>
                            <li>
                                <p class="font-weight-bold mb-0">Money and finance</p>
                                <p>This category will have questions that concern money. It may be related to
                                    your current financial position or the future, or maybe the reasons
                                    affecting it or how you can improve.</p>
                            </li>
                        </ul>
                        <p>These are the four primary and basic categories under which almost every question can
                            be put. Then it will be convenient for you to choose the expert <?php echo e(ucfirst($professionTitle)); ?>s who will
                            answer your question. It will be done through Vedic Astrology predictions, tarot
                            reading, numerology, and palmistry to give you the best insights.</p>
                        <p><?php echo e(ucfirst($appname)); ?> is your ultimate destination for all your online Astrology consultation
                            needs. Here you can get the best guidance from the top <?php echo e(ucfirst($professionTitle)); ?>s who will help you
                            make the best and the most beneficial decisions in life.</p>

                        <h3 class="font-weight-bold pt-4 pb-3">FAQs Related To Astrology &amp; <?php echo e(ucfirst($appname)); ?>

                        </h3>
                        <div itemscope itemtype='https://schema.org/FAQPage'>
                            <ol class="pl-3">
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">What are Astrology
                                            predictions based on?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Astrology predictions are basically the
                                                analysis of the position of planets and stars and how they move
                                                to impact the world and each individual existing there. So the
                                                basis of offline and online Astrology predictions is the
                                                movement and transits of the planets in the Universe.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">What are Astrology
                                            and zodiac?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Zodiac signs are the signs that develop the
                                                internal and external personality of someone, and Astrology
                                                defines the changes in that personality concerning the planetary
                                                movements.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">How do Astrology
                                            predictions help me to deal with my problems?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Astrology predictions can keep you a step ahead
                                                of time where you can know what is waiting for you in the
                                                future. And with proper guidance, you can be better prepared to
                                                deal with the problems and challenges you might face in the
                                                future.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">How can online
                                            Astrology predictions be so accurate? Is there any scientific reason
                                            behind it?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Astrology services are based on
                                                pseudo-scientific practice that provides Astrology predictions
                                                to individuals based on the movements of planets. These offline
                                                and online Astrology predictions can be general and specific
                                                depending on the type of reading.</p>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">How reliable are
                                            the <?php echo e(ucfirst($appname)); ?> <?php echo e(ucfirst($professionTitle)); ?>s?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">The credibility of <?php echo e(ucfirst($appname)); ?>&#39;s
                                                <?php echo e(ucfirst($professionTitle)); ?>s can be seen through the reviews and the ratings they
                                                get from people like you after their session with them. All our
                                                <?php echo e(ucfirst($professionTitle)); ?>s are verified for their experience and expertise.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">Can I ask personal
                                            questions?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text"><?php echo e(ucfirst($appname)); ?>&#39;s <?php echo e(ucfirst($professionTitle)); ?>s have expertise
                                                in every aspect of life. This includes both personal and general
                                                queries. So you can very well ask an <?php echo e(ucfirst($professionTitle)); ?> online any
                                                question related to the issue that is troubling you.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">What type of a
                                            question can I ask an <?php echo e(ucfirst($professionTitle)); ?>?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">A good <?php echo e(ucfirst($professionTitle)); ?> is there to solve all your
                                                queries and concerns regarding life. So you can ask an
                                                <?php echo e(ucfirst($professionTitle)); ?> any question except those that break the sanctity of
                                                this spiritual practice. It includes queries related to black
                                                magic, death, afterlife, etc.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">Can I speak to the
                                            same <?php echo e(ucfirst($professionTitle)); ?> when I call again?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Yes, you can always choose the <?php echo e(ucfirst($professionTitle)); ?> of
                                                your choice. And if you want to talk to the same <?php echo e(ucfirst($professionTitle)); ?>

                                                again, you have to select them again for your session through
                                                the defined process.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">Can I talk to an
                                            <?php echo e(ucfirst($professionTitle)); ?> for free?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">You can connect with the best <?php echo e(ucfirst($professionTitle)); ?>s
                                                without paying anything for your first session. After that, you
                                                need to recharge your wallet with a basic amount to connect with
                                                them. You can either chat with an <?php echo e(ucfirst($professionTitle)); ?> or call them.</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">How much does it
                                            cost to see an <?php echo e(ucfirst($professionTitle)); ?>?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <div itemprop="text">
                                                <p>You can connect with the <?php echo e(ucfirst($professionTitle)); ?>s through live chat or
                                                    call. You need to sign up and register absolutely for free
                                                    to get there. After that, you can also avail your first free
                                                    chat session but moving forward, you need to recharge your
                                                    wallet.</p>
                                                <p>The rates of each <?php echo e(ucfirst($professionTitle)); ?> vary. These are based on their
                                                    expertise, experience, and exposure. So how much you will
                                                    pay will depend on the <?php echo e(ucfirst($professionTitle)); ?> you choose.</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                        <p itemprop="name" class="font-weight-bold mb-0">Who is the best
                                            online <?php echo e(ucfirst($professionTitle)); ?>?</p>
                                        <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <p itemprop="text">Every <?php echo e(ucfirst($professionTitle)); ?> at <?php echo e(ucfirst($appname)); ?> is the best.
                                                Still, the one who can cater to your specific requirements based
                                                on the area of expertise will be the best for you. At Anytime
                                                Astro, you can connect with the best <?php echo e(ucfirst($professionTitle)); ?>s in India.</p>
                                        </div>
                                    </div>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="astroway-about d-none d-md-block py-4 py-md-5">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="text-md-center heading">WHY <?php echo e(ucfirst($appname)); ?>?</h2>
                    <p class="text-md-center">One of the best online Astrology platforms to connect with
                        experienced and verified <?php echo e(ucfirst($professionTitle)); ?>s</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="accordion" id="faq">

                        <?php foreach ($webfaqs as $index => $faqItem): ?>
                        <div class="card">
                            <div class="card-header" id="faqhead<?php echo $index + 1; ?>">
                                <h3 class="panel-title mb-0">
                                    <a href="#" class="btn btn-header-link collapsed font-18" data-toggle="collapse"
                                    data-target="#faq<?php echo $index + 1; ?>" aria-expanded="false"
                                    aria-controls="faq<?php echo $index + 1; ?>">
                                    <?php echo e($faqItem->title); ?>

                                    </a>
                                </h3>
                            </div>

                            <div id="faq<?php echo $index + 1; ?>" class="collapse" aria-labelledby="faqhead<?php echo $index + 1; ?>"
                                data-parent="#faq">
                                <div class="card-body">
                                <?php echo e($faqItem->description); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="astroway-about-mobile d-md-none bg-pink py-4">
        <div class="container">
            <h2 class="heading text-center">WHY <?php echo e(ucfirst($appname)); ?>?</h2>
            <div class="row pt-4 pb-2">
                <div class="col-4 text-center">
                    <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about1.svg')); ?>"
                        class="img-fluid" />
                    <p class="font-weight-semi-bold pt-3 font-14">Verified <?php echo e(ucfirst($professionTitle)); ?>s</p>
                </div>
                <div class="col-4 text-center">
                    <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about2.svg')); ?>"
                        class="img-fluid" />
                    <p class="font-weight-semi-bold pt-3 font-14">Ask An <?php echo e(ucfirst($professionTitle)); ?> Via Multiple Ways</p>
                </div>
                <div class="col-4 text-center">
                    <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about3.svg')); ?>"
                        class="img-fluid" />
                    <p class="font-weight-semi-bold pt-3 font-14">100% Privacy Guaranteed</p>
                </div>
            </div>
        </div>
    </div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<style>
.marquee-wrapper {
    width: 100%;
    overflow: hidden;
}

.marquee {
    display: flex;
    animation: marquee 10s linear infinite;
}

.marquee a {
    flex-shrink: 0;
    text-decoration: none;
}

/* Pause marquee on hover */
.marquee a:hover,
.marquee:hover {
    animation-play-state: paused;
}

@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.youtube-icon {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
}
.video-card {
    position: relative;
}
</style>

<script>
$(document).ready(function() {
    // Open modal with selected video and description
    $('.video-link').on('click', function() {
        var videoUrl = $(this).data('video');
        var description = $(this).data('description');
        $('#videoIframe').attr('src', videoUrl + "?autoplay=1");
        $('#videoDescription').html(description);
    });

    // Stop video when modal is closed
    $('#videoModal').on('hidden.bs.modal', function () {
        $('#videoIframe').attr('src', '');
    });
});
</script>
<script>
    $(document).ready(function() {
        var owl = $('.astroway-astrologers .owl-carousel');

        if ($(window).width() > 767) {
            owl.owlCarousel({
                margin: 0,
                responsive: {
                    0: {
                        items: 2,
                        slideBy: 2
                    },
                    370: {
                        items: 2.3,
                        slideBy: 2
                    },
                    768: {
                        items: 2.4,
                        slideBy: 2,
                        nav: true
                    },
                    992: {
                        nav: true,
                        items: 3
                    },
                    1199: {
                        nav: true,
                        items: 5
                    }
                }
            });
        }
        owl.removeClass('owl-blur');

        $('#main_nav').on('shown.bs.collapse', function() {
            $('#navbarDropdown').dropdown('toggle');
        });

        $(".news-sections").owlCarousel({
            loop: false,
            nav: true,
            dots: true,

            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });

    $(document).ready(function() {
      $(".astrology-video-carousel").owlCarousel({
            loop: false,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            margin: 15,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.story').on('click', function() {
            var astrologerId = $(this).data('astrologer-id');
            var astrologerName = $(this).data('astrologer-name');
            var astrologerProfile = $(this).data('astrologer-profile');
            // console.log(astrologerProfile);

            if (!astrologerProfile) {
                astrologerProfile = 'frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png';
            }
            // Make an AJAX request to get the stories
            $.ajax({
                url: '/astrologer/' + astrologerId + '/stories',
                method: 'GET',
                success: function(response) {
                    openStoryModal(response, astrologerName,astrologerProfile);
                },
                error: function(error) {
                    console.error('Error fetching stories:', error);
                }
            });
        });
    });

    function openStoryModal(stories, name , profileImage) {
        var modal = $('#storyModal');
        var astrologerProfileImage = $('#astrologerProfileImage');
        var astrologerName = $('#astrologerName');
        var carouselIndicators = $('#carouselIndicators');
        var carouselInner = $('#carouselInner');
        var modalTitle=$('#astrologerName');

        // Clear existing slides and indicators
        carouselIndicators.empty();
        carouselInner.empty();

        // Add new slides and indicators
        stories.forEach((story, index) => {
            var indicator = $('<li>')
                .attr('data-target', '#carouselExampleIndicators')
                .attr('data-slide-to', index);
            if (index === 0) {
                indicator.addClass('active');
            }
            carouselIndicators.append(indicator);

            var carouselItem = $('<div>')
                .addClass('carousel-item');
            if (index === 0) {
                carouselItem.addClass('active');
            }

            if (story.mediaType === 'image') {
                var img = $('<img>')
                    .addClass('d-block w-100')
                    .attr('src', story.media);
                carouselItem.append(img);
            } else if (story.mediaType === 'video') {
                var video = $('<video>')
                    .addClass('d-block w-100')
                    .attr('controls', true);
                var source = $('<source>')
                    .attr('src', story.media)
                    .attr('type', 'video/mp4');
                video.append(source);
                carouselItem.append(video);
            } else if (story.mediaType === 'text') {
                var text = $('<div>')
                    .addClass('d-block w-100 text-center')
                    .css({
                        'padding': '20px',
                        'font-size': calculateFontSize(story.media)
                    })
                    .text(story.media);
                carouselItem.append(text);
            }
            <?php if(authcheck()): ?>
            trackStoryView(story.id);
            <?php endif; ?>
            carouselInner.append(carouselItem);
        });

        modalTitle.text(name);
        astrologerProfileImage.attr('src', profileImage);

        modal.modal('show');

        // Stop auto sliding
            $('.carousel').carousel('pause');


        function calculateFontSize(text) {
            var baseFontSize = 30;
            var maxLength = 200;
            var fontSize = baseFontSize;

            if (text.length > maxLength) {
                fontSize = baseFontSize - ((text.length - maxLength) / 10);
            }

            return fontSize + 'px';
        }


        function trackStoryView(storyId) {
            $.ajax({
                url: "<?php echo e(route('front.viewstory')); ?>",
                method: 'POST',
                data: {
                    storyId: storyId
                },
                success: function(response) {
                    console.log(response.message);
                },
                error: function(error) {
                    console.error('Error viewing story:', error);
                }
            });
     }
}


</script>
<script>
$(document).ready(function() {
    $('a[data-video]').click(function(e) {
        e.preventDefault();
        var videoUrl = $(this).data('video');
        var videoId = '';

        // Handle shorts
        if (videoUrl.includes('youtube.com/shorts/')) {
            videoId = videoUrl.split('/shorts/')[1];
            var qmark = videoId.indexOf('?');
            if (qmark !== -1) {
                videoId = videoId.substring(0, qmark);
            }
        }
        // Handle normal YouTube link
        else if (videoUrl.includes('youtube.com/watch')) {
            videoId = videoUrl.split('v=')[1];
            var ampersandPosition = videoId.indexOf('&');
            if (ampersandPosition !== -1) {
                videoId = videoId.substring(0, ampersandPosition);
            }
        }
        // Handle short youtu.be link
        else if (videoUrl.includes('youtu.be')) {
            videoId = videoUrl.split('/').pop();
        }
        else {
            videoId = videoUrl; // assume already video ID
        }

        var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
        $('#videoIframe').attr('src', embedUrl);
        $('#videoModal').modal('show');
    });

    $('#videoModal').on('hidden.bs.modal', function() {
        $('#videoIframe').attr('src', '');
    });
});

</script>
 <?php if(request('error')): ?>
  <script>

            toastr.error("<?php echo e(request('error')); ?>");

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
     <?php endif; ?>
<?php $__env->stopSection(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <?php if(Auth::check()): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (!localStorage.getItem("hidemyModal")) {
        var modalElement = document.getElementById('autoModal');
        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();

        // document.getElementById('doNotShowmodule').addEventListener('click', function() {
        //   localStorage.setItem("hidemyModal", "true");
        //   myModal.hide();
        // });

        document.getElementById('closeModalBtn').addEventListener('click', function() {
          myModal.hide();
        });
      }
    });
  </script>
  <?php endif; ?>

</body>
</html>

<?php echo $__env->make('frontend.layout.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH G:\xampp\htdocs\astroway\Website\resources\views/frontend/pages/index.blade.php ENDPATH**/ ?>