@extends('frontend.layout.master')

@section('content')
<style>
    .container.mt-5.mb-5.pujalist-show { display: flex; flex-wrap: wrap; gap: 10px; }
    .dark { background: #FFF5E0 !important; }
    .darkS { background: #8DECB4 !important; }
    .scard { width: 350px; height: 550px; overflow: hidden; display: flex; flex-direction: column; border: 1px solid #d0d0d0; border-radius: 10px; padding: 15px; margin: 10px; }
    .imgb { width: 100%; height: 190px; object-fit: cover; background-position: bottom; background-size: cover; border-radius: 10px; }
    .descrb { height: 100%; }
    .puja-footer { display: flex; justify-content: center; align-items: center; }
    .read { border: none; background: #ffd700; color: #0e0c0c; padding: 5px 8px; border-radius: 15px; }
    @media (min-width: 1199px) { .container { max-width: 1200px !important; } }
</style>

@if($pujalists->isEmpty())
    <div class="container mt-5 mb-5 justify-content-center">
        <div class="text-center">
            <img src="{{ asset('public/frontend/homeimage/360.png') }}" alt="No Puja Found" class="img-fluid" />
            <h3>No Puja Found !</h3>
        </div>
    </div>
@else
    <div class="container mt-5 mb-5 pujalist-show">
        @foreach ($pujalists as $puja)
            <?php
            $startDatetime = $puja->puja_start_datetime ? \Carbon\Carbon::parse($puja->puja_start_datetime) : null;
            $endDatetime = $puja->puja_end_datetime ? \Carbon\Carbon::parse($puja->puja_end_datetime) : null;

            // Agar start or end same ho jaye to skip
            if($startDatetime && $endDatetime && $startDatetime->eq($endDatetime)) continue;

            $images = $puja->puja_images;
            $firstImage = !empty($images) ? $images[0] : 'path/to/default/image.jpg';

            $startDateDisplay = $startDatetime ? $startDatetime->format('j M, D') : 'Date not available';
            $endDateDisplay = $endDatetime ? $endDatetime->format('j M, D') : '';
            $startTimeDisplay = $startDatetime ? $startDatetime->format('H:i') : '';
            $endTimeDisplay = $endDatetime ? $endDatetime->format('H:i') : '';
            $sameDate = $startDatetime && $endDatetime ? $startDatetime->isSameDay($endDatetime) : true;
            ?>
            <div class="scard dark1">
                <img class="imgb" src="{{ Str::startsWith($firstImage, ['http://','https://']) ? $firstImage : '/' . $firstImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $firstImage }}')" />
                <div class="mt-2 text-center" style="border-bottom: 1px solid #aea7a7;">
                    <small>{{ \Illuminate\Support\Str::limit($puja->category->name, 39, '...') }}</small>
                </div>
                <div class="descrb">
                    <h3 class="mt-3 font-weight-bold">{{ \Illuminate\Support\Str::limit($puja->puja_title, 58, '...') }}</h3>
                    <span>{{ \Illuminate\Support\Str::limit($puja->puja_subtitle, 58, '...') }}</span>
                </div>
                <div class="mt-2 d-flex align-items-start">
                    <i class="fa-solid fa-place-of-worship me-2 mr-2"></i>
                    <div>
                        <span class="d-block" style="font-size: 15px;">{{ \Illuminate\Support\Str::limit($puja->puja_place, 60, '...') }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mt-2">
                    <div class="d-flex justify-content-center align-items-center rounded-circle overflow-hidden"
                        style="width: 30px; height: 30px;">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </div>
                    <div class="text-white">
                        <div class="text-gray-90 mt-1">
                            <span class="text-secondary" style="font-size: 16px; line-height: 21.79px;">
                            {{ $startDatetime && $endDatetime ? ($sameDate ? $startDateDisplay.' '.$startTimeDisplay : $startDateDisplay.' '.$startTimeDisplay.' '.$endTimeDisplay) : 'Date not available' }}</span>
                        </div>
                    </div>
                </div>
                <div><hr></div>
                <div class="puja-footer">
                    <a href="{{ route('front.pujaDetails', $puja->slug) }}" class="read w-100">
                        <span class="justify-content-center d-flex">
                            PARTICIPATE <i class="fa-solid fa-arrow-right ml-1"></i>
                        </span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="mt-8 text-center pb-5">
    {{ $pujalists->links() }}
</div>
@endsection
