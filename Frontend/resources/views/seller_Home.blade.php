<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Home</title>
  <link rel="icon" href="/asset/favicon.ico"> {{-- Favicon --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/asset/styles.css"> {{-- Main site styles --}}
  <link rel="stylesheet" href="/asset/seller_home.css"> {{-- Seller Home specific styles --}}
</head>
<body>
  <main class="sh-main-layout">
    <aside class="sh-sidebar">
      <div class="sh-profile-section">
        @foreach ($sellerImages as $sellerImage)
          @if ($sellerImage && $sellerImage->seller_profile_img)
            <img src="{{ asset($sellerImage->seller_profile_img) }}" alt="Seller Image" class="sh-profile-img">
          @else
            <div class="sh-profile-img sh-no-profile-img"><i class="fa-regular fa-circle-user"></i></div>
          @endif
        @endforeach
        <p class="sh-welcome-text">Welcome to the Handcraft marketplace,</p>
        <span class="sh-profile-detail">ID: {{ Auth::guard('seller')->user()->seller_id}}</span>
        <span class="sh-profile-detail">Name: {{ Auth::guard('seller')->user()->full_name }}</span>
        <span class="sh-profile-detail">Email: {{ Auth::guard('seller')->user()->seller_email}}</span>
        <form action="sellerProfile" method="get">
          @csrf
          <button type="submit" class="btn btn-info">Profile</button>
        </form>

        <form action="logout" method="post">
          @csrf
          <button type="submit" class="sh-btn sh-btn-logout">Logout</button>
        </form>
      </div>
      <nav class="sh-sidebar-nav">
        <a href="#products" class="sh-btn" style="display:flex;align-items:center;gap:8px;font-size:1.05rem;justify-content:center;">
          <span style="font-size:1.2em;">&#128230;</span> Products
        </a>
        <a href="seller_viewOrder" class="sh-btn" style="display:flex;align-items:center;gap:8px;font-size:1.05rem;justify-content:center;">
          <span style="font-size:1.2em;">&#128179;</span> View Orders
        </a>
        <a href="allSellerMessages" class="sh-btn" style="display:flex;align-items:center;gap:8px;font-size:1.05rem;justify-content:center;">
          <span style="font-size:1.2em;">&#9993;</span> View Messages
        </a>
      </nav>
    </aside>
    <section class="sh-content">
      <h2 class="sh-section-title">View All Products</h2>
      @if (session('status'))
        <div class="sh-alert sh-alert-success">
          {{ session('status') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="sh-alert sh-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div class="sh-products-grid">
        @foreach ($product as $p)
          <div class="sh-product-card">
            <div class="sh-product-img-wrap">
              @php
                $image = collect($images)->firstWhere('product_id', $p->product_id);
              @endphp
              @if ($image)
                <img src="{{ asset($image->img_url) }}" alt="Product Image" class="sh-product-img">
              @else
                <div class="sh-product-img sh-no-img">No image</div>
              @endif
            </div>
            <div class="sh-product-info">
              <span class="sh-product-id">ID: {{ $p->product_id }}</span>
              <h3 class="sh-product-name">{{ $p->product_name }}</h3>
              @php
                $cat = collect($category)->firstWhere('category_id', $p->category_id);
              @endphp
              <span class="sh-product-category">Category: {{ $cat ? $cat->category_name : 'Unknown' }}</span>
              <span class="sh-product-description">{{ $p->product_description }}</span>
              <span class="sh-product-price">${{ number_format($p->product_price, 2) }}</span>
              <span class="sh-product-stock">Stock: {{ $p->stock_quantity }}</span>
            </div>
            <div class="sh-product-actions">
              <a href="updateProduct/{{ $p->product_id }}" class="sh-btn sh-btn-update">Update</a>
              <form action="deleteProduct/{{ $p->product_id }}" method="POST">
                @csrf
                <button type="submit" class="sh-btn sh-btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </section>
    <a href="addProduct" class="sh-add-product-btn">Upload products +</a>
  </main>
</body>
</html>