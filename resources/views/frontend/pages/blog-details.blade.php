@extends('frontend.layout.master')

@section('content')

<div class="container py-5 min-vh-100">
    <div class="row gap-4">

        <!-- Main Blog Section -->
        <div class="col-lg-8 col-12">

            <!-- Blog Title -->
            <h1 class="mb-4 fw-bold display-4" style="font-family: 'Poppins', sans-serif; color:#2c2c2c;">
                {{ $blog->title }}
            </h1>

            <!-- Blog Image/Video -->
            <div class="mb-4 shadow-lg rounded overflow-hidden" style="border-radius: 15px;">
                @php
                    $extension = pathinfo($blog->blogImage, PATHINFO_EXTENSION);
                    $videoExtensions = ['mp4', 'webm', 'ogg'];
                @endphp

                @if(in_array($extension, $videoExtensions))
                    <video class="w-100" controls style="border-radius: 15px; max-height: 450px;">
                        <source src="{{ asset($blog->blogImage) }}" type="video/{{ $extension }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ Str::startsWith($blog->blogImage, ['http://','https://']) ? $blog->blogImage : '/' . $blog->blogImage }}" 
                         onerror="this.onerror=null;this.src='/build/assets/images/person.png';" 
                         class="img-fluid w-100 rounded" 
                         style="cursor: pointer; max-height: 450px; object-fit: cover;" 
                         onclick="openImage('{{ $blog->blogImage }}')" alt="{{ $blog->title }}">
                @endif
            </div>

            <!-- Blog Description -->
            <div class="fs-5 text-secondary" style="line-height: 1.8; font-family: 'Roboto', sans-serif;">
                {!! $blog->description !!}
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4 col-12">

            <div class="sticky-top" style="top: 100px;">
                <div class="bg-white shadow-lg rounded p-4 mb-5">
                    <h2 class="mb-4" style="font-weight: 700; font-size: 1.8rem; background: linear-gradient(90deg, #6a1b9a, #00c853); -webkit-background-clip: text; color: transparent;">
                        Explore More
                    </h2>

                    @foreach ($latestBlogs as $index => $latest)
                        @php
                            $extension = pathinfo($latest->blogImage, PATHINFO_EXTENSION);
                            $videoExtensions = ['mp4', 'webm', 'ogg'];
                        @endphp

                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-light">
                            @if(in_array($extension, $videoExtensions))
                                <video class="rounded" width="60" height="60" style="object-fit: cover; border-radius: 10px;" muted>
                                    <source src="{{ asset($latest->blogImage) }}" type="video/{{ $extension }}">
                                </video>
                            @else
                                <img src="{{ Str::startsWith($latest->blogImage, ['http://','https://']) ? $latest->blogImage : '/' . $latest->blogImage }}" 
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';" 
                                     class="rounded" width="60" height="60" 
                                     style="object-fit: cover; border-radius: 10px; cursor:pointer;" 
                                     onclick="openImage('{{ $latest->blogImage }}')" alt="{{ $latest->title }}">
                            @endif

                            <div class="flex-grow-1">
                                <a href="{{ route('front.getBlogDetails', $latest->slug) }}" 
                                   class="fw-bold text-dark d-block mb-1" 
                                   style="transition: all 0.3s; font-size: 1rem;"
                                   onmouseover="this.style.color='#6a1b9a';" onmouseout="this.style.color='black';">
                                   {{ $latest->title }}
                                </a>
                                <small class="text-muted">Read more &rarr;</small>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* Blog main content hover effect */
    .container img:hover {
        transform: scale(1.03);
        transition: transform 0.3s;
    }

    /* Sidebar hover card effect */
    .sticky-top .d-flex:hover {
        background-color: #f5f5f5;
        border-radius: 12px;
        transition: background-color 0.3s;
    }

    /* Smooth scroll if overflow occurs */
    #allText {
        scroll-behavior: smooth;
    }
</style>

@endsection
