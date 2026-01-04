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
                             <i class="fa fa-chevron-right"></i> <a href="{{ route('front.checkout',['id' => $getproductdetails->id]) }}"
                                style="color:white;text-decoration:none">Checkout </a>

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


                                                <!--<div class="form-group mb-0">-->
                                                <!--    <span-->
                                                <!--        class="field-validation-valid control-label commonerror float-right color-red"-->
                                                <!--        data-valmsg-for="Name" data-valmsg-replace="false"> </span>-->
                                                <!--    <label for="BoyName" class="">Phone No&nbsp;<span-->
                                                <!--            class="color-red">*</span></label>-->
                                                <!--    <input class="form-control border-pink matchInTxt shadow-none"-->
                                                <!--        id="Name" name="phoneNumber" placeholder="Enter Phone"-->
                                                <!--        value=""  type="tel" pattern="\d{10}" -->
                                                <!--        inputmode="numeric" title="Phone number should contain only numbers." required-->
                                                <!--        oninput="this.value = this.value.replace(/[^0-9]/g, '')">-->
                                                <!--</div>-->
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
                                                    <select class="form-control select2" name="country" required>
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
                                                    <select class="form-control select2" name="state" required>
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="city">City <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="city" required>
                                                        <option value="">Select City</option>
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
                            <h2 class="cat-heading font-24 font-weight-bold">Checkout <span class="color-red">Form</span>
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

                                    <div class="table-responsive  mt-4 mb-4 ">
                                        <table class="table  border-pink font-14 mb-0 text-center">
                                            <tbody>
                                                <tr class="bg-pink color-red">
                                                    <td>#</td>
                                                    <td>Name</td>
                                                    <td>Phone</td>
                                                    <td>Address</td>
                                                </tr>

                                                @foreach ($getOrderAddress['recordList'] as $getOrderAddress)
                                                    <tr>
                                                        <td> <input type="radio" name="orderAddressId"
                                                                value="{{ $getOrderAddress['id'] }}"></td>
                                                        <td>{{ $getOrderAddress['name'] }}</td>
                                                        <td>{{ $getOrderAddress['phoneNumber'] }}</td>
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
                            <div class="shadow-pink p-1">
                                <div class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3">
                                    Product Detail
                                </div>
                                <div class="border-0 mt-2">
                                    <div class="card-body pt-0">
                                        <div class="row justify-content-between mb-3">
                                            <div class="col-auto">
                                                <div class="media">
                                                    <img class="img-fluid  mb-3" src="{{ Str::startsWith($getproductdetails->productImage, ['http://','https://']) ? $getproductdetails->productImage : '/' . $getproductdetails->productImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $getproductdetails->productImage }}')" width="350" height="350"/>
                                                </div>
                                                <div class="media-body">
                                                        <p class="mb-0">
                                                            <b>{{ $getproductdetails->name }}</b></p>
                                                        <small
                                                            class="text-muted">{!! \Illuminate\Support\Str::limit($getproductdetails->features, 200) !!}</small>
                                                    </div>
                                            </div>
                                            <input type="hidden" name="productCategoryId"
                                                value="{{ $getproductdetails->productCategoryId }}">
                                            <input type="hidden" name="productId"
                                                value="{{ $getproductdetails->id }}">

                                        </div>
                                        <div class="row justify-content-between ">
                                            <div class="col-auto">
                                                <p><span>Price:</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                <p><span>
                                                    @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ number_format($getproductdetails->amount, 2) }}</span>
                                                </p><small>(incl of all taxes) </small>
                                                <input type="hidden"
                                                   value="{{ number_format($getproductdetails->amount, 2, '.', '') }}"
                                                   name="payableAmount">

                                            </div>
                                        </div>

                                        {{-- <div class="row justify-content-between">
                                            <div class="col-auto">
                                                <p><span>Gst( {{ $gstvalue['value'] }}%):</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                <p><span>
                                                   @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ number_format($getproductdetails->amount * ($gstvalue['value'] / 100), 2) }}</span>
                                                </p>
                                                <input type="hidden" value="{{ $gstvalue['value'] }}"
                                                    name="gstPercent">
                                            </div>
                                        </div> --}}
                                        <hr>
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <p><b>Total Price:</b></p>
                                            </div>
                                            <div class="col-auto my-auto color-red">
                                                <p><b>
                                                    @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ number_format($getproductdetails->amount, 2) }}</b>
                                                </p>
                                               <input type="hidden"
                                                   value="{{ number_format($getproductdetails->amount, 2, '.', '') }}"
                                                   name="totalPayable" id="totalPayable">

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
                // Check if the form is valid according to HTML5 validation rules
                if (form.checkValidity() === false) {
                    // If the form is invalid, trigger native HTML5 validation
                    form.reportValidity();
                    return;  // Exit if validation fails
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
                e.preventDefault();

                @php
                    $token = $session->get('token');

                    $wallet = DB::table('user_wallets')
                    ->where('userId', '=', authcheck()['id'])
                    ->first();

                    if (!$wallet) {
                        $wallet = (object) ['amount' => 0];
                    }

                @endphp

                var paymentMethod = 'wallet';

                if (!$("input[name='orderAddressId']:checked").val()) {
                    toastr.error('Please select an address.');
                    return;
                }

                var payableAmount=$("#totalPayable").val();
                var walletamount="{{$wallet->amount}}";

                newpayableAmount=(parseFloat(payableAmount.replace(/,/g, '')));

                if(walletamount < newpayableAmount){
                    toastr.error('Insufficient Balance in wallet');
                    $.ajax({
                        url: '{{ route('user.addpayment', ['token' => $token]) }}',
                        type: 'POST',
                        data: {
                            'amount':newpayableAmount,
                            'cashback_amount':0
                        },
                        success: function(response) {
                            window.location.href = response.url;
                            // console.log(response);
                        },

                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseText);
                        }
                     });
                    // window.location.href="{{ route('front.walletRecharge') }}";
                    return false;
                }


                // var formData = $('#orderForm').serialize();
                // formData += '&paymentMethod=' + encodeURIComponent(paymentMethod);
                // console.log(formData);


                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to proceed with the order?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, order now!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User confirmed, proceed with the order
                        var paymentMethod = 'wallet';
                        var formData = $('#orderForm').serialize();
                        formData += '&paymentMethod=' + encodeURIComponent(paymentMethod);

                        $.ajax({
                            url: '{{ route('api.addUserOrder', ['token' => $token]) }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                toastr.success('Product Ordered Successfully');
                                setTimeout(function() {
                                    window.location.href = '{{ route('front.home') }}';
                                }, 2000);
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
