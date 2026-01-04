@extends('frontend.layout.master')


@section('content')



<main role="main" class="margin-top-header">
    <div class="pt-1 pb-1 bg-red d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-12 d-flex align-items-center">
                    <span style="text-transform: capitalize;">
                        <span class="text-white breadcrumbs">
                            <a href="/" class="text-white text-decoration-none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i>
                            <a href="{{ route('front.pujaList',$puja->category_id) }}" class="text-white text-decoration-none">Puja</a>
                            <i class="fa fa-chevron-right"></i>
                            Puja Details - {{$puja->puja_title}}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="product-detail bg-white py-4">
    <div class="container py-5 px-3">
        <!-- Product Card -->
        <div class="bg-white rounded-3 shadow p-4 d-flex flex-column flex-md-row gap-4">
            
            <!-- Left: Main Image -->
            <div class="col-md-6 d-flex flex-column align-items-center">
                <div class="w-100 border rounded overflow-hidden" style="height: 400px;">
                    @foreach ($puja->puja_images as $index => $image)
                        <img id="mainImage" class="w-100 h-100 object-fit-cover transition-all" 
                             src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" 
                             onerror="this.onerror=null;this.src='/build/assets/images/person.png';" 
                             alt="Customer image" 
                             onclick="changeImage(this)" />
                    @endforeach
                </div>

                <!-- Thumbnails -->
                <div class="d-flex gap-3 mt-3 overflow-auto w-100 justify-content-center">
                    @foreach ($puja->puja_images as $index => $image)
                        <img class="rounded border" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; transition: all 0.2s;" 
                             src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" 
                             onerror="this.onerror=null;this.src='/build/assets/images/person.png';" 
                             alt="Customer image" 
                             onclick="changeImage(this)" 
                             onmouseover="this.style.transform='scale(1.05)'" 
                             onmouseout="this.style.transform='scale(1)'" />
                    @endforeach
                </div>
            </div>

            <!-- Right: Product Details -->
            <div class="col-md-6 d-flex flex-column ">
                <div>
                    <h2 class="h3 fw-bold mb-2">{{ $puja->category->name }}</h2>
                    <h4 class="text-muted mb-1">{{ $puja->puja_title }}</h4>
                    <h5 class="text-muted mb-2">{{ $puja->puja_subtitle }}</h5>
                    <strong class="text-secondary mb-4 d-block">{{ $puja->puja_place }}</strong>

                    <?php
                        $startDatetime = \Carbon\Carbon::parse($puja->puja_start_datetime);
                        $endDatetime = \Carbon\Carbon::parse($puja->puja_end_datetime);

                        $startDateDisplay = $startDatetime->format('j M, D');
                        $endDateDisplay = $endDatetime->format('j M, D');
                        $startTimeDisplay = $startDatetime->format('H:i');
                        $endTimeDisplay = $endDatetime->format('H:i');
                        $sameDate = $startDatetime->isSameDay($endDatetime);

                        $now = \Carbon\Carbon::now();
                        $isFutureEvent = $now->lt($startDatetime);
                    ?>

                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary fs-6 py-2 px-3">
                            @if($sameDate)
                                {{ $startDateDisplay }} {{ $startTimeDisplay }} - {{ $endTimeDisplay }}
                            @else
                                {{ $startDateDisplay }} {{ $startTimeDisplay }} to {{ $endDateDisplay }} {{ $endTimeDisplay }}
                            @endif
                        </span>
                    </div>

                    @if($isFutureEvent)
                        <div class="countdown-timer mt-2" 
                             data-start-datetime="{{ $startDatetime->toIso8601String() }}">
                            <span class="badge bg-warning text-dark">
                                Puja starts in: 
                                <span class="days">0</span>d 
                                <span class="hours">0</span>h 
                                <span class="minutes">0</span>m 
                                <span class="seconds">0</span>s
                            </span>
                        </div>
                    @else
                        <div class="mt-2">
                            <span class="badge bg-success p-2 mb-5">Puja is ongoing</span>
                        </div>
                    @endif
                </div>

                <a href="#packages" class="btn btn-outline-primary mt-5 w-100 mt-4 fw-semibold d-flex justify-content-center align-items-center">
                    Select Puja Package <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function changeImage(el) {
    document.getElementById('mainImage').src = el.src;
}
</script>

<script>
    function changeImage(img) {
        document.getElementById('mainImage').src = img.src;
    }
</script>



  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* ====== NAV TAB STYLING ====== */
    .product-info {
      border-top: 2px solid #eee;
      border-bottom: 2px solid #eee;
    }

    .product-info .nav-link {
      font-weight: 600;
      color: #333;
      font-size: 16px;
      border-radius: 0;
      padding: 10px 25px;
      transition: all 0.3s ease;
    }

    .product-info .nav-link.active {
      color: #fff;
      background-color: #ff6600;
      border-radius: 5px;
    }

    .section {
      display: none;
      animation: fadeIn 0.4s ease-in-out;
    }

    .section.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #ff6600;
      font-weight: 700;
      margin-bottom: 15px;
    }
  </style>

<div class="container py-5">

  <!-- ===== TAB MENU ===== -->
  <div class="py-2 product-info justify-content-center d-flex w-100 mt-3">
    <ul class="nav flex-wrap justify-content-center" id="pujaTabs">
      <li class="nav-item px-3">
        <a class="nav-link active" data-target="about">About Puja</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" data-target="benefits">Benefits</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" data-target="process">Process</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" data-target="packages">Packages</a>
      </li>
      <li class="nav-item px-3">
        <a class="nav-link" data-target="faqs">FAQs</a>
      </li>
    </ul>
  </div>

  <!-- ===== TAB CONTENT ===== -->
  <div class="tab-content mt-4">

    <!-- About -->
    <div id="about" class="section active">
      <h2>About Puja</h2>
      <p class="text-justify">
        {{ $puja->long_description ?? 'Detailed description about the Puja will appear here.' }}
      </p>
    </div>

    <!-- Benefits -->
    <div id="benefits" class="section">
      <h2>Puja Benefits</h2>
      <div class="row mt-4">
        @foreach ($puja->puja_benefits as $benefit)
        <div class="col-md-4 mb-3">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3" style="background-color:#f8f8f8;border-radius:50%;height:50px;width:50px;display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-star text-warning fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold">{{ $benefit['title'] }}</h6>
              <p class="text-muted mb-0">{{ $benefit['description'] }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- Process -->
    <div id="process" class="section">
      <h2>Puja Process</h2>
      <div class="row mt-4">
        <div class="col-md-4 mb-3">
          <h6 class="fw-bold">1️⃣ Select Puja</h6>
          <p class="text-muted">Choose from the puja packages listed below.</p>
        </div>
        <div class="col-md-4 mb-3">
          <h6 class="fw-bold">2️⃣ Add Offerings</h6>
          <p class="text-muted">Enhance your experience with optional offerings like Deep Daan or Anna Daan.</p>
        </div>
        <div class="col-md-4 mb-3">
          <h6 class="fw-bold">3️⃣ Sankalp Details</h6>
          <p class="text-muted">Provide Name and Gotra for Sankalp.</p>
        </div>
      </div>
    </div>

    <!-- Packages -->
    <div id="packages" class="section">
      <h2>Select Puja Package</h2>
      <div class="row mt-4">
        @foreach ($package as $packageDetail)
        <div class="col-md-4 mb-3">
          <div class="card shadow-sm border-warning h-100">
            <div class="card-body text-center">
              <h4 class="text-warning">{{ $packageDetail['title'] }}</h4>
              <p class="mb-1 text-muted">For {{ $packageDetail['person'] }} Person</p>
              <h3 class="fw-bold text-danger">
                @if(systemflag('walletType') == 'Coin')
                                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                    ₹
                                                    @endif
              {{ $packageDetail['package_price'] }}</h3>
              <ul class="text-start mt-3 list-unstyled small">
                @foreach ($packageDetail['description'] as $point)
                <li><i class="fa-solid fa-hand-point-right text-warning"></i> {{ $point }}</li>
                @endforeach
              </ul>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-center pb-3">
              <a @if(!authcheck()) data-toggle="modal" data-target="#loginSignUp" @else href="{{route('front.pujaAstrologerList',['slug'=>$puja->slug,'package_id'=>$packageDetail['id']])}}" @endif class="btn btn-success w-75">Participate</a>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- FAQs -->
    <div id="faqs" class="section">
      <h2>FAQs</h2>
      <div class="accordion" id="faqAccordion">
        @foreach ($FAQ as $index => $faqItem)
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading{{ $index }}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false">
              {{ $faqItem->title }}
            </button>
          </h2>
          <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              {{ $faqItem->description }}
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div><!-- End tab-content -->

</div><!-- End container -->

<!-- ===== JS ===== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Handle tab switching
  const tabs = document.querySelectorAll('#pujaTabs .nav-link');
  const sections = document.querySelectorAll('.section');

  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      // Remove active class from tabs
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      // Hide all sections
      sections.forEach(s => s.classList.remove('active'));

      // Show selected section
      const targetId = this.getAttribute('data-target');
      document.getElementById(targetId).classList.add('active');

      // Scroll to section smoothly
      window.scrollTo({ top: document.querySelector('.product-info').offsetTop - 100, behavior: 'smooth' });
    });
  });
</script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const readMoreElements = document.querySelectorAll('.read-more');

        readMoreElements.forEach(element => {
            element.addEventListener('click', function () {
                const fullDescription = this.nextElementSibling;
                const readLess = this.nextElementSibling.nextElementSibling;
                fullDescription.style.display = 'inline';
                this.style.display = 'none';
                readLess.style.display = 'inline';
            });
        });

        const readLessElements = document.querySelectorAll('.read-less');

        readLessElements.forEach(element => {
            element.addEventListener('click', function () {
                const fullDescription = this.previousElementSibling;
                const readMore = this.previousElementSibling.previousElementSibling;
                fullDescription.style.display = 'none';
                this.style.display = 'none';
                readMore.style.display = 'inline';
            });
        });
    });

    $(document).ready(function () {
        $('.owl-carousel').owlCarousel({
            loop: true,
            margin: 20, // Adds space between items
            nav: false,
            dots: true,
            center: true, // Ensures the nav buttons are centered
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


    document.querySelectorAll('.scrollable-container').forEach(container => {
    container.addEventListener('wheel', (event) => {
        // Check if we're at the top scrolling up or at the bottom scrolling down
        const atTop = container.scrollTop === 0;
        const atBottom = container.scrollHeight - container.scrollTop === container.clientHeight;
        
        // Only prevent default if we need to handle the scroll ourselves
        if ((event.deltaY < 0 && !atTop) || (event.deltaY > 0 && !atBottom)) {
            event.preventDefault();
            container.scrollBy({
                top: event.deltaY,
                behavior: 'auto' // Changed to 'auto' for better performance
            });
        }
        
        // Otherwise, let the native scroll handle it (for edge cases)
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElements = document.querySelectorAll('.countdown-timer');
        
        function updateCountdown() {
            countdownElements.forEach(element => {
                const startDatetime = new Date(element.dataset.startDatetime);
                const now = new Date();
                const diff = startDatetime - now;
                
                if (diff <= 0) {
                    element.innerHTML = '<span class="badge bg-success">Puja has started</span>';
                    return;
                }
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                element.querySelector('.days').textContent = days;
                element.querySelector('.hours').textContent = hours;
                element.querySelector('.minutes').textContent = minutes;
                element.querySelector('.seconds').textContent = seconds;
            });
        }
        
        // Update immediately and then every second
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
    </script>


@endsection