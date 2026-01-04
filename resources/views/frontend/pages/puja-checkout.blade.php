@extends('frontend.layout.master')
@section('content')
<style>
    select[name="country"].select2 + .select2-container,
    select[name="state"].select2 + .select2-container,
    select[name="city"].select2 + .select2-container {
        border: 1px solid #ced4da;
        border-radius: 5px;
    }
</style>
@php
        $countries = DB::table('countries2')
    ->get();

    $countries2 = DB::table('countries')
    ->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")
    ->get();
@endphp
    <div class="pt-1 pb-1 bg-red d-none d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{route('front.pujacheckout',['slug'=>$astrologer->id,'id'=>$PujaDetails->id,'package_id'=>$PujaDetails->packages->id ?? 0])}}"
                                style="color:white;text-decoration:none">Puja Checkout </a>

                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade rounded mt-2 mt-md-5 login-offer" id="checkout" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title font-weight-bold">
                        SHIPPING DETAILS
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body pt-0 pb-0">

                    <div class="bg-white body">
                        <div class="row ">

                            <div class="col-lg-12 col-12 ">
                                <div class="mb-3 ">

                                    <form class="px-3 font-14" method="post" id="orderAddress" autocomplete="off">

                                        <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                        <div class="row">
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Name&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="name" placeholder="Enter Name"
                                                        type="text" value="" pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long." required
                                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                     <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName">Phone No<span class="color-red font-weight-bold">*</span></label>
                                                    <div class="input-group">
                                                        <div class="d-flex inputform country-dropdown-container" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                                            <select class="form-control select2" id="countryCode" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 20%;">
                                                                @foreach ($countries2 as $country)
                                                                <option data-country="in" value="{{$country->phonecode}}" data-ucname="India">
                                                                    +{{ $country->phonecode }} {{ $country->iso }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            <!-- Mobile Number Input -->
                                                            <input class="form-control mobilenumber text-box single-line" id="contact" maxlength="12" name="phoneNumber"  type="number"  style="border: none; border-radius: 0; width: 130%;" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Flat No&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="flatNo" placeholder="Enter Flat"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Locality&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="locality" placeholder="Enter Locality"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Landmark&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="landmark" placeholder="Enter Landmark"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="country">Country <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="country" id="country" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="state">State <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="state" id="state" required>
                                                        <option value="">Select State</option>
                                                    <!-- States will be populated here based on the selected country -->
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="city">City <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="city" id="city" required>
                                                        <option value="">Select City</option>
                                                        <!-- Cities will be populated here based on the selected state -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Pincode&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="pincode" placeholder="Enter Pincode"
                                                        type="text" value="" pattern="\d{6}" 
                                                        inputmode="numeric" title="Pincode should be a 6 digit number." required
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12 py-3">
                                            <div class="row">

                                                <div class="col-12 pt-md-3 text-center mt-2">
                                                    <button type="submit"
                                                        class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                        id="addressBtn">Add Address</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="ds-head-populararticle bg-white cat-pages">
        <div class="container">
            <div class="row py-3">
                <div class="col-sm-12 mt-4">
                    <div class="row">
                        <div class="col-12 mb-5">
                            <h2 class="cat-heading font-24 font-weight-bold">Puja Checkout <span class="color-red">Form</span>
                            </h2>

                        </div>
                        <div class="col-lg-8 col-12 ">
                            <div class="mb-3 shadow-pink">
                                <div class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3">
                                    SELECT ADDRESS
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-6 ml-auto">
                                        <a role="button" data-toggle="modal" data-target="#checkout"
                                            class="mt-3 btn view-more color-red font-weight-normal mb-2">
                                            Add Address
                                        </a>
                                    </div>

                                </div>


                                <form class="px-3 font-14" method="post" id="orderForm" autocomplete="off">
                                    <input type="hidden" name="astrologer_id" value="{{ $astrologer->id }}">
                                    <div class="table-responsive  mt-4 mb-4 ">
                                        <table class="table  border-pink font-14 mb-0 text-center">
                                            <tbody>
                                                <tr class="bg-pink color-red">
                                                    <td>#</td>
                                                    <td>Name</td>
                                                    <td>Phone</td>
                                                    <td>Address</td>
                                                </tr>

                                                @foreach ($getOrderAddressed as $getOrderAddress)
                                                    <tr>
                                                        <td> <input type="radio" name="orderAddressId" id="orderAddressId"
                                                                value="{{ $getOrderAddress['id'] }}"></td>
                                                        <td>{{ $getOrderAddress['name'] }}</td>
                                                        <td>{{ $getOrderAddress['countryCode'] }} {{ $getOrderAddress['phoneNumber'] }}</td>
                                                        <td>{{ $getOrderAddress['flatNo'] }},{{ $getOrderAddress['locality'] }},{{ $getOrderAddress['landmark'] }},{{ $getOrderAddress['city'] }},{{ $getOrderAddress['state'] }},{{ $getOrderAddress['country'] }},{{ $getOrderAddress['pincode'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="shadow-pink p-3">
                                <div class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3">
                                    Puja Detail
                                </div>
                                <div class="card border-0 mt-2">
                                    <div class="card-body pt-0">
                                        <div class="row justify-content-between mb-3">
                                            <div class="col-auto">
                                                <div class="media" style="display:block !important;border-radius: 10px;">
                                                    @if (!empty($PujaDetails->puja_images) && is_array($PujaDetails->puja_images))
                                                        <div id="pujaImageSlider" class="carousel slide" data-ride="carousel" style="width: 220px; height: 160px; overflow: hidden; position:relative;">
                                                            <div class="carousel-inner" style="width: 100%; height: 100%;">
                                                                @foreach($PujaDetails->puja_images as $key => $image)
                                                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" style="width: 100%; height: 100%;">
                                                                        <img class="rounded-m" src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $image }}')" />
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            @if(count($PujaDetails->puja_images) > 1)
                                                                <a class="carousel-control-prev" href="#pujaImageSlider" role="button" data-slide="prev" style="width: 20px; height: 20px; top: 50%; left: 0; transform: translateY(-50%);">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="sr-only">Previous</span>
                                                                </a>
                                                                <a class="carousel-control-next" href="#pujaImageSlider" role="button" data-slide="next" style="width: 20px; height: 20px; top: 50%; right: 0; transform: translateY(-50%);">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="sr-only">Next</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <img class="img-fluid"
                                                            src="{{ asset('public/frontend/homeimage/360.png') }}"
                                                            width="62" height="62">
                                                    @endif
                                                    <div class="media-body mt-2" style="text-align: center;">
                                                        <p class="mb-0"><b>{{ $PujaDetails->puja_title}}</b></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="pujaId"
                                                value="{{ $PujaDetails->id }}">
                                            
                                            @if(isset($PujaDetails->packages->id))
                                                <input type="hidden" name="packageId"
                                                value="{{ $PujaDetails->packages->id }}">
                                                @endif

                                        </div>
                                        <div class="row justify-content-between ">
                                            @if(isset($PujaDetails->packages->id))
                                            <div class="col-auto">
                                                <p class="mb-0"><span class="font-weight-semi-bold text-secondary">Package : <p><span>{{ $PujaDetails->packages->title }} ({{$PujaDetails->packages->person}} Person)</span></p></span></p>
                                            </div>
                                            @endif

                                        </div>

                                        <div class="row justify-content-between ">
                                            <div class="col-auto">
                                                <p class="mb-1"><span>Price:</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                @if(isset($PujaDetails->packages->id))
                                                <p><span>{{ $currency->value }}{{ number_format($PujaDetails->packages->package_price, 2) }}</span>
                                                </p><small>(incl of all taxes) </small>
                                                <input type="hidden"
                                                    value="{{ number_format($PujaDetails->packages->package_price, 2) }}"
                                                    name="payableAmount">
                                                    @else

                                                    <p><span>{{ $currency->value }}{{ number_format($PujaDetails->puja_price, 2) }}</span>
                                                    </p><small>(incl of all taxes) </small>
                                                    <input type="hidden"
                                                        value="{{ number_format($PujaDetails->puja_price, 2) }}"
                                                        name="payableAmount">

                                                    @endif
                                            </div>
                                        </div>

                                        {{-- <div class="row justify-content-between">
                                            <div class="col-auto">
                                                <p class="mb-1"><span>Gst({{$gstvalue->value}}%):</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                <p><span>{{ $currency->value }}{{ number_format($PujaDetails->packages->package_price * ($gstvalue->value / 100), 2) }}</span>
                                                </p>
                                                <input type="hidden" value="{{ number_format($PujaDetails->packages->package_price * ($gstvalue->value / 100), 2) }}"
                                                    name="gstPercent">
                                            </div>
                                        </div> --}}
                                        <hr>
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <p><b>Total Price:</b></p>
                                            </div>
                                            <div class="col-auto my-auto color-red">
                                                @if(isset($PujaDetails->packages->id))
                                                <p><b>{{ $currency->value }}{{ number_format($PujaDetails->packages->package_price , 2) }}</b>
                                                </p>
                                                <input type="hidden"
                                                    value="{{ number_format($PujaDetails->packages->package_price , 2) }}"
                                                    name="totalPayable" id="totalPayable">
                                                @else
                                                <p><b>{{ $currency->value }}{{ number_format($PujaDetails->puja_price , 2) }}</b>
                                                </p>
                                                <input type="hidden"
                                                    value="{{ number_format($PujaDetails->puja_price , 2) }}"
                                                    name="totalPayable" id="totalPayable">
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 py-3">
                                <div class="row">

                                    <div class="col-12 pt-md-3 text-center mt-2">
                                        <button type="submit" class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                            id="orderBtn">Buy Now</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%' // Ensure Select2 dropdown takes full width of the parent
        });
    });
$(document).ready(function () {
    // Get references to the dropdowns using their name attributes
    const $countryDropdown = $('select[name="country"]');
    const $stateDropdown = $('select[name="state"]');
    const $cityDropdown = $('select[name="city"]');

    // Add event listener to the country dropdown
    $countryDropdown.on('change', function () {
        const countryId = $(this).val();

        // Clear the state and city dropdowns
        $stateDropdown.html('<option value="">Select State</option>');
        $cityDropdown.html('<option value="">Select City</option>');

        if (countryId) {
            // Fetch states based on the selected country
            $.ajax({
                url: `/get-states/${countryId}`,
                type: 'GET',
                success: function (data) {
                    $stateDropdown.html('<option value="">Select State</option>');
                    $.each(data, function (key, value) {
                        $stateDropdown.append(`<option value="${value.id}">${value.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching states:', error);
                }
            });
        }
    });

    // Add event listener to the state dropdown
    $stateDropdown.on('change', function () {
        const stateId = $(this).val();
 
        // Clear the city dropdown
        $cityDropdown.html('<option value="">Select City</option>');

        if (stateId) {
            // Fetch cities based on the selected state
            $.ajax({
                url: `/get-cities/${stateId}`,
                type: 'GET',
                success: function (data) {
                    $cityDropdown.html('<option value="">Select City</option>');
                    $.each(data, function (key, value) {
                        $cityDropdown.append(`<option value="${value.id}">${value.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching cities:', error);
                }
            });
        }
    });
});
            </script>
    <script>
        $(document).ready(function() {
            $('#addressBtn').click(function(e) {
                e.preventDefault();

                var form = document.getElementById('orderAddress');
                if (form.checkValidity() === false) {
                    form.reportValidity();
                    return;  
                }
                
                @php
                    use Symfony\Component\HttpFoundation\Session\Session;
                    $session = new Session();
                    $token = $session->get('token');

                @endphp

                var formData = $('#orderAddress').serialize();
                // console.log(formData);

                $.ajax({
                    url: '{{ route('api.addOrderAddress', ['token' => $token]) }}',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        toastr.success('Address Added Successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#orderBtn').click(function(e) {

                var radioButton = document.querySelector('input[name="orderAddressId"]:checked'); // Find the checked radio button

                if (!radioButton) {
                    // If no radio button is selected, show the error using Toastr
                    toastr.error('Please select an address.', 'Validation Error', {
                        timeOut: 5000, // Duration of the notification
                        closeButton: true, // Allow closing of toastr
                        progressBar: true // Show progress bar
                    });
                    e.preventDefault(); // Prevent form submission
                    return;
                }
                e.preventDefault();

                // Retrieve the token from the session using PHP, then embed it into the JavaScript variable
                var token = "{{ session('token') }}";

                // Display confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to proceed with this Puja order?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, order now!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user confirms, proceed with the order
                        var paymentMethod = 'wallet';

                        // Serialize form data and append the payment method
                        var formData = $('#orderForm').serialize();
                        formData += '&paymentMethod=' + encodeURIComponent(paymentMethod);

                        // Make the AJAX request
                        $.ajax({
                            url: '{{ route('front.addUserPujaOrder', ['token' => '']) }}' + token,
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                if(response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    toastr.success('Puja Ordered Successfully');
                                    setTimeout(function() {
                                        window.location.href = '{{ route('front.home') }}';
                                    }, 2000);
                                }
                            },
                            error: function(xhr, status, error) {
                                var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                                toastr.error(errorMessage);
                            }
                        });
                    }
                });
            });
        });

    </script>
    
@endsection
