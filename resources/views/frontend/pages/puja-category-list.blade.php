@extends('frontend.layout.master')

@section('content')
<style>
    .container.mt-5.mb-5.pujalist-show {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }


    .dark {
        background: #FFF5E0 !important;
    }

    .darkS {
        background: #8DECB4 !important;
    }

    .scard {
        width: 350px;
        height: 550px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border: 1px solid #d0d0d0;
        border-radius: 10px;
        padding: 15px;
        margin: 10px;
    }

    .imgb {
        width: 100%;
        height: 190px;
        object-fit: cover;
        background-position: bottom;
        background-size: cover;
        border-radius: 10px;
    }

    .descrb {
        height: 100%;
    }

    .icons {
        display: flex;
        padding: 5px 0px;
        display: none;
    }

    .icon {
        display: block !important;
    }

    .icons i {
        margin-right: 15px;
    }

    .puja-footer {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .amt {
        color: #65A9FD;
        font-weight: bold;
    }

    .read {
        border: none;
        background: #ffd700;
        color: #0e0c0c;
        padding: 5px 8px;
        border-radius: 15px;
    }

    .puja-footer i {
        padding: 5px;
        border-radius: 15px;
    }

    @media (min-width: 1199px) {
        .container {
            max-width: 1200px !important;
        }
    }
</style>

@if($pujaCategories->isEmpty())
<!-- Show this section when no pujas are found -->
<div class="container mt-5 mb-5 justify-content-center">
    <div class="text-center">
        <img src="{{ asset('public/frontend/homeimage/360.png') }}" alt="No Puja Found" class="img-fluid" />
        <h3>No Puja Category Found !</h3>
    </div>
</div>

@else

<div class="container mt-5 mb-5 pujalist-show">
    @foreach ($pujaCategories as $category)
    <div class="scard dark1" style="height:auto!important">
        <?php
        $image = $category->image;
        $firstImage = !empty($image) ? $image : 'path/to/default/image.jpg';
        ?>
        <img class="rounded-m" src="{{ Str::startsWith($firstImage, ['http://','https://']) ? $firstImage : '/' . $firstImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $firstImage }}')" />
        <div class="mt-2 text-center" style="border-bottom: 1px solid #aea7a7;">
        </div>
        <div class="descrb">
            <h3 class="mt-3 font-weight-bold">{{$category->name}}</h3>
        </div>
        <div>
            <hr>
        </div>
        <div class="puja-footer ">
            <a href="{{route('front.pujaList',$category->id)}}" class="read w-100">
                <span class="justify-content-center d-flex">Place <i class="fa-solid fa-arrow-right ml-1"></i></span>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="mt-8 text-center pb-5">
    {{ $pujaCategories->links() }}
</div>

@endsection