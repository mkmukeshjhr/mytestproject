<style>
    .text-gray{
        color: #D1D5DB !important;
    }
    .text-gray-dark{
        color: #9CA3AF !important;
    }
</style>
<?php
use App\Models\AstrologerModel\AstrologerCategory;
$getAstrologerCategory = AstrologerCategory::where('isActive',1)->orderBy('id', 'DESC')->get();
$facebook = DB::table('systemflag')->where('name', 'Facebook')->select('value')->first();
$apple = DB::table('systemflag')->where('name', 'Apple')->select('value')->first();
$website = DB::table('systemflag')->where('name', 'Website')->select('value')->first();
$youtube = DB::table('systemflag')->where('name', 'Youtube')->select('value')->first();
$linkedIn = DB::table('systemflag')->where('name', 'LinkedIn')->select('value')->first();
$pintrest = DB::table('systemflag')->where('name', 'Pintrest')->select('value')->first();
$instagram = DB::table('systemflag')->where('name', 'Instagram')->select('value')->first();
$whatsapp = DB::table('systemflag')->where('name', 'Whatsapp')->select('value')->first();
$telegram = DB::table('systemflag')->where('name', 'Telegram')->select('value')->first();
$twitter = DB::table('systemflag')->where('name', 'Twitter')->select('value')->first();
$playstore = DB::table('systemflag')->where('name', 'PlayStore')->select('value')->first();
$appstore = DB::table('systemflag')->where('name', 'AppStore')->select('value')->first();
$aiAstrologer = DB::table('systemflag')->where('name', 'AiAstrologer')->select('value')->first();

?>
<!-- FOOTER START -->
<div id="footer" style="background: linear-gradient(180deg, #3a3a3a 0%, #202020 100%);">
    <section class="pt-5 pb-4">
        <div class="container">
            <div class="row  text-md-left g-4">
                <!-- MENU Column -->
                <div class="col-md-3 col-6 mb-4">
                    <h5 class="text-white p-2 font-16 border-bottom border-secondary ">MENU</h5>
                    <ul class="list-unstyled" style="font-size: 14px">
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.getkundali')); ?>">Kundli</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.kundaliMatch')); ?>">Kundli Matching</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.getproducts')); ?>">Products</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.horoScope')); ?>">Horoscope</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.getPanchang')); ?>">Today's Panchang</a></li>
                    </ul>
                </div>

                <!-- LINKS Column -->
                <div class="col-md-3 col-6 mb-4">
                    <h5 class="text-white p-2 font-16 border-bottom border-secondary ">LINKS</h5>
                    <ul class="list-unstyled" style="font-size: 14px">
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.getBlog')); ?>">Go to Blog</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.contact')); ?>">Contact Us</a></li>
                    </ul>

                    <?php if(!authcheck()): ?>
                    <h5 class="text-white p-2 font-16 mt-4 border-bottom border-secondary "><?php echo e(ucfirst($professionTitle)); ?> Section</h5>
                    <ul class="list-unstyled mt-1" style="font-size: 14px">
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.astrologerlogin')); ?>"><?php echo e(ucfirst($professionTitle)); ?> Login</a></li>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.astrologerregister')); ?>"><?php echo e(ucfirst($professionTitle)); ?> Registration</a></li>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- FEATURES Column -->
                <div class="col-md-3 col-6 mb-4">
                    <h5 class="text-white p-2 font-16 border-bottom border-secondary ">GET ADVICE ON</h5>
                    <ul class="list-unstyled" style="font-size: 14px">
                        <?php $__currentLoopData = $getAstrologerCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="p-1"><a class="footer-link" href="<?php echo e(route('front.chatList',['astrologerCategoryId'=>$category->id])); ?>"><?php echo e($category->name); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                <!-- ABOUT Column -->
                <div class="col-md-3 col-6 mb-4  text-md-left">
                    <h5 class="text-white p-2 font-16 border-bottom border-secondary ">Download Our Apps</h5>
                    <a href="<?php echo e($playstore->value); ?>" class="d-block mt-3">
                        <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/google-play.png')); ?>" alt="google-play" class="img-fluid" width="183" height="54" loading="lazy">
                    </a>
                    <a href="<?php echo e($appstore->value); ?>" class="d-block mt-3">
                        <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/app-store.png')); ?>" alt="app-store" class="img-fluid" width="183" height="54" loading="lazy">
                    </a>

                    <div class=" mt-3 f-icon justify-content-md-start">
                        <?php if(!empty($facebook->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($facebook->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/fb.svg')); ?>" alt="facebook" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($twitter->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($twitter->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/twitter.svg')); ?>" alt="twitter" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($linkedIn->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($linkedIn->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/linkedin.svg')); ?>" alt="linkedin" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($instagram->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($instagram->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/insta.svg')); ?>" alt="instagram" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($youtube->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($youtube->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/youtube.svg')); ?>" alt="youtube" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($pintrest->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($pintrest->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/pinterest.svg')); ?>" alt="pinterest" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($whatsapp->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($whatsapp->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/whatsapp.svg')); ?>" alt="whatsapp" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($telegram->value)): ?>
                        <a class="social-icon" target="_blank" href="<?php echo e($telegram->value); ?>" rel="nofollow">
                            <img src="<?php echo e(asset('frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/telegram.svg')); ?>" alt="telegram" width="30" height="30" loading="lazy">
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    $isProfileComplete=false;
    if(authcheck()){
        $user = authcheck()['name'];
        $dob = authcheck()['birthDate'];
        $place_of_birth= authcheck()['birthPlace'];
        $isProfileComplete = $user && $dob && $place_of_birth;
    }
    ?>

    <?php if(!empty($aiAstrologer->value)): ?>
    <div id="sf_chat_button1" role="button" class="sf_chat_button1">
        <button data-bs-toggle="tooltip" title="Chat with master Astrologer" data-bs-placement="top" class="rounded-circle border-0 bg-transparent">
            <a class="shadow-md d-inline checkBalance" id="checkBalance">
                <img src="https://cdn-icons-png.flaticon.com/128/6819/6819661.png" width="50" height="53" alt="">
            </a>
        </button>
    </div>
    <?php endif; ?>

    <div class="text-center py-3" style="background-color: #2b2b2b;">
    <small class="text-gray-dark">
        Copyright © 2020-<?php echo e(date('Y')); ?>

        <?php echo e(ucfirst($appname)); ?>. All Rights Reserved |
    </small>

    <ul class="footer-links list-unstyled d-inline-block m-0 p-0">
       <ul>
<?php $__currentLoopData = $footerPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li class="footer-item d-inline-block mx-1">
        <a class="text-gray-dark footer-link" href="<?php echo e($page->type ? url($page->type) : '#'); ?>">
            <?php echo e($page->title); ?>

        </a>
        <?php if(!$loop->last): ?>
            <span class="d-none d-md-inline">|</span>
        <?php endif; ?>
    </li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>

    </ul>
</div>

<style>
.footer-links {
    display: inline-flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 5px;
}
.f-icon{
    display: flex;
}
.footer-item {
    display: inline-block;
}

.footer-link {
    color: #bfbfbf;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-link:hover {
    color: #fff;
}

/* Mobile View: show 3 columns */
@media (max-width: 767px) {
    .footer-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        text-align: center;
        gap: 8px;
        padding-top: 5px;
    }

    .footer-item {
        display: block;
    }

    .footer-links span {
        display: none !important;
    }
}
</style>

</div>

<!-- FOOTER STYLE -->
<style>
    .footer-link {
        font-size: 14px;
    }
        .d-md-inline {
        display: inline !important;
        color: cadetblue;
    }
.footer-link {
    color: #cfcfcf;
    text-decoration: none;
    transition: color 0.3s ease, transform 0.3s ease;
}
.footer-link:hover {
    color: #f7b731;
    text-decoration: underline;
    transform: translateX(3px);
}
.social-icon {
    margin: 5px;
    transition: transform 0.3s ease, opacity 0.3s ease;
}
.social-icon:hover {
    transform: scale(1.15);
    opacity: 0.85;
}
#sf_chat_button1 {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}
#footer h5 {
    font-size: 16px;
    letter-spacing: 0.5px;
}
@media (max-width: 768px) {
    #footer {
        /*text-align: center;*/
    }
    .col-6 {
        padding: 10px;
    }
    #footer h5 {
        font-size: 15px;
        /*margin-bottom: 10px;*/
    }
    .footer-link {
        font-size: 13px;
    }
    .social-icon img {
        width: 26px;
        height: 26px;
    }
}
</style>
<!-- FOOTER END -->

<script>
    $('.checkBalance').on('click', function(e) {
        e.preventDefault();

        var isProfileComplete = <?php echo json_encode($isProfileComplete, 15, 512) ?>;

        if (!isProfileComplete) {
            // Profile incomplete, show SweetAlert
            Swal.fire({
                title: 'Profile Incomplete',
                text: 'Your profile is incomplete. Please provide your Date of Birth and Place of Birth.',
                icon: 'warning',
                confirmButtonText: 'Update Profile',
                showCancelButton: true,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to profile update page
                    window.location.href = "<?php echo e(route('front.getMyAccount')); ?>"; // Adjust to your profile update route
                }
            });

        }else{

        $.ajax({
            url: '<?php echo e(route("check.user.balance")); ?>',
            method: 'GET',
            success: function(response) {

                localStorage.removeItem('masterSubmitting');
                localStorage.removeItem('refreshRedirectMaster');
                localStorage.removeItem('timer');
                localStorage.removeItem('balance');
                localStorage.removeItem('reloadAftSubmit');

                if (response.status === 'success') {

                    console.log(response.balance)
                    if(response.balance !== null){
                        Swal.fire({
                            icon: 'question',
                            title: 'Confirm Action',
                            text: response.message,
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Hold on!',
                                    text: 'Please do not refresh the page.',
                                    showCancelButton: true,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "<?php echo e(route('master.chat.page')); ?>";
                                    }
                                });
                            }
                        });
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hold on!',
                            text: 'Please do not refresh the page.',
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "<?php echo e(route('master.chat.page')); ?>";
                            }
                        });
                    }
                } else if (response.status === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: response.message
                    });
                } else if (response.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: response.message,
                        confirmButtonText: 'Log In',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loginSignUp').modal('show');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: 'Please try again later.'
                });
            }
        });
        }
    });

</script>
<script>
    window.onload = function() {
    // if (localStorage.getItem('removeRemainingTime')) {
        localStorage.removeItem('remainingTime'); // Remove the item
        localStorage.removeItem('removeRemainingTime'); // Clear the flag
        localStorage.removeItem('refreshRedirect'); // Clear the flag
    // Other initialization code...
};

</script>
<?php /**PATH G:\xampp\htdocs\astroway\Website\resources\views/frontend/layout/footer.blade.php ENDPATH**/ ?>