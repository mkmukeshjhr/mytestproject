@extends('frontend.layout.master')
@section('content')

<div class="max-w-7xl mx-auto pb-5">
  <div class="container my-5">

    <!-- Section Title -->
    <div class="text-center mb-4">
      <h2 class="display-6 fw-bold text-decoration-underline">Products</h2>
      <p class="mt-3">
        See new products and how {{ ucfirst($appname) }} helped them find their path to happiness!
      </p>
    </div>

    <!-- Category Filter -->
    <div class="col-ms-12 col-md-3 d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2 ml-auto" id="filterproductCategory">
      <select name="productCategoryId" onchange="onFilterProductCategoryList()" class="form-control font13 rounded shadow-sm border-0" id="psychicCategories">
        <option value="0" {{ $productCategoryId == '0' ? 'selected' : '' }}>Select Category</option>
        @foreach ($getproductCategory['recordList'] as $category)
          <option value="{{ $category['id'] }}" {{ $productCategoryId == $category['id'] ? 'selected' : '' }}>
            {{ $category['name'] }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Products Grid -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-4">
      @if (count($productlist) > 0)
        @php
          $colors = ['fuchsia', 'slate', 'purple', 'lime', 'rose', 'green', 'sky'];
        @endphp
        @foreach ($productlist as $key => $products)
          @php $color = $colors[$key % count($colors)]; @endphp
          <div class="col" data-aos="fade-up">
            <div class="product-card d-flex flex-column shadow-sm border-0 rounded-4 overflow-hidden bg-white transition-all">
              <a href="{{ route('front.getproductDetails', ['slug' => $products->slug]) }}" class="text-decoration-none">
                <div class="product-image-wrapper position-relative overflow-hidden">
                  <img
                    class="product-image w-100 h-100"
                    src="{{ Str::startsWith($products->productImage, ['http://','https://']) ? $products->productImage : '/' . $products->productImage }}"
                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                    alt="{{ $products->name }}"
                  />
                </div>
              </a>
                <div class="d-flex flex-column justify-content-between flex-grow-1 p-3">
                  <!-- Product Name -->
                  <h5 class="text-dark fw-semibold mb-3">{{ $products->name }}</h5>
                
                  <!-- Price and Buy Button Row -->
                  <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="fw-bold text-dark fs-6 mb-0">
                     @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                    {{ $products->amount }}
                    </span>
                    <a href="{{ route('front.getproductDetails', ['slug' => $products->slug]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                      Buy Now
                    </a>
                  </div>
                </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</div>

<style>
  .product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }
  .product-image-wrapper {
    width: 100%;
    height: 240px; /* uniform height for all cards */
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
  }
  .product-image {
    object-fit: cover; /* ensures image fills and crops perfectly */
    width: 100%;
    height: 100%;
  }
  .product-card {
    border-radius: 15px;
    margin: 20px -9px;
    padding: 2px !important;
    color: #333;
    text-align: center;
    height: 96%;
    display: flex;
    flex-direction: column;
}
.product-card h5 {
    font-weight: bold;
    margin-top: 10px;
    padding: 5px;
}
  @media (max-width: 576px) {
    .product-image-wrapper {
      height: 200px;
    }
  }
</style>


    <!-- Pagination Controls -->
<div class="mt-8 d-flex justify-content-center pt-5 pb-5">
    {{ $productlist->appends(request()->query())->links() }}
</div>
</div>
@endsection

@section('scripts')
    <script>
    function onFilterProductCategoryList() {
        var productCategoryId = $('#psychicCategories').val();
        var url = new URL(window.location.href);
        url.searchParams.set('productCategoryId', productCategoryId);
        window.location.href = url.toString();
    }
    </script>

    @endsection
