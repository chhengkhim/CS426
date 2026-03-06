<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Store</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/customer_view_store_page.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

  <div class="store-page-main">
    <h1 class="store-main-title">Welcome to {{ $seller->store_name }}'s Store</h1>
    
    <div class="store-content-wrapper">
      <div class="seller-info-column">
        <div class="seller-profile-card">
          <h2 class="card-title">Seller Information</h2>
          <div class="profile-img-container">
            @if ($seller->seller_profile_img)
              <img src="{{ asset($seller->seller_profile_img) }}" alt="Seller Image" class="profile-img">
            @else
              <div class="no-img-placeholder">
                <i class="fas fa-store"></i>
                <p>No profile image available.</p>
              </div>
            @endif
          </div>
          <div class="profile-details">
            <p><strong>Shop Name:</strong> {{ $seller->store_name }}</p>
            <p><strong>Name:</strong> {{ $seller->full_name }}</p>
            <p><strong>Email:</strong> {{ $seller->seller_email }}</p>
            <p><strong>Phone Number:</strong> {{ $seller->phone_number }}</p>


 <p>
      <strong>Address:</strong> {{$seller->seller_address}}
 </p>

    <p>
        <a href="{{ url('customerMessageSeller', $seller->seller_id) }}" class="modern-msg-btn">
            <span style="margin-right:8px;"><i class="fas fa-paper-plane"></i></span> Message Seller
        </a>
    </p>

          </div>


        </div>
      </div>

      <div class="products-column">
        <h2 class="products-section-title">Our Products</h2>
        @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
          </div>
        @endif

        <div class="product-grid-wrapper">
          <div class="nav-arrow left"><i class="fas fa-chevron-left"></i></div>
          <div class="product-grid">
            @foreach ($product as $p)
              <div class="product-card">
                <div class="product-card-image">
                  @php
                    $image = collect($images)->firstWhere('product_id', $p->product_id);
                  @endphp
                  @if ($image)
                    <img src="{{ asset($image->img_url) }}" alt="Product Image" class="product-thumb">
                  @else
                    <div class="no-product-image-placeholder">
                      <span>No image</span>
                    </div>
                  @endif
                  <div class="heart-icon"><i class="far fa-heart"></i></div>
                </div>
                <div class="product-card-details">
                  <h3 class="product-card-name">{{ $p->product_name }}</h3>
                  @php
                    $cat = collect($category)->firstWhere('category_id', $p->category_id);
                  @endphp
                  <p class="product-card-category">Category: {{ $cat ? $cat->category_name : 'Unknown' }}</p>
                  <p class="product-card-price">${{ number_format($p->product_price, 2) }}</p>
                  <div class="product-options">
                    <label for="select-{{ $p->product_id }}" class="sr-only">Select Option</label>
                    <select id="select-{{ $p->product_id }}" class="product-select">
                      <option value="">Please select</option>
                      <option value="option1">Option 1</option>
                      <option value="option2">Option 2</option>
                    </select>
                  </div>
                  <form action="{{ url('/customer_product_detail/' . $p->product_id) }}" method="get">
                      @csrf
                      <button type="submit" class="add-to-bag-btn">ADD TO BAG</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
          <div class="nav-arrow right"><i class="fas fa-chevron-right"></i></div>
        </div>
      </div>
    </div>
  </div>

<style>
  .modern-msg-btn {
    display: inline-flex;
    align-items: center;
    background: #b85c38;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 1.08rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(184,92,56,0.08);
    transition: background 0.18s, box-shadow 0.18s;
    margin-top: 18px;
  }
  .modern-msg-btn:hover {
    background: #a14d2a;
    color: #fff;
    box-shadow: 0 4px 16px rgba(184,92,56,0.16);
    text-decoration: none;
  }
</style>
</body>
</html>