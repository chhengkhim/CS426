<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hidden Craft</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="/asset/styles.css">
</head>
<body>
<header class="hc-navbar">
  <div class="hc-navbar-content">
    <a href="/" class="hc-navbar-logo">Hidden Craft</a>
    <nav class="hc-navbar-links">
      <a href="/customer_Home" class="hc-navbar-link">Home</a>
      <div class="hc-navbar-dropdown-parent">
        <a href="#shop" class="hc-navbar-link hc-navbar-shop-link">Shop</a>
        <div class="hc-navbar-categories-dropdown">
          @foreach ($category as $catItem)
            <a href="{{ url('customer_viewSpecificProduct_category?category_id=' . $catItem->category_id) }}" class="hc-navbar-category-link">
              {{ $catItem->category_name }}
            </a>
          @endforeach
        </div>
      </div>
      <a href="customer_viewOrder" class="hc-navbar-link">Orders</a>
      <a href="allcustomer_messages" class="hc-navbar-link">Messages</a>
      <a href="viewCart" class="hc-navbar-link">
        <span class="hc-cart-icon">🛒</span>
        {{-- <span class="hc-cart-badge">2</span> --}}
      </a>
    </nav>
    @auth('customer')
    <div class="hc-navbar-user">
      @if (Auth::guard('customer')->user()->customer_profile_images)
        <img src="{{ asset(Auth::guard('customer')->user()->customer_profile_images) }}" alt="Customer Image" class="hc-navbar-avatar" onclick="document.getElementById('hc-user-dropdown').classList.toggle('show')">
      @else
        <div class="hc-navbar-avatar hc-navbar-avatar-placeholder" onclick="document.getElementById('hc-user-dropdown').classList.toggle('show')"><i class="fa-regular fa-user"></i></div>
      @endif
      <div class="hc-navbar-dropdown" id="hc-user-dropdown">
        <div class="hc-navbar-userinfo">
          <div><strong>ID:</strong> {{ Auth::guard('customer')->user()->customer_id }}</div>
          <div><strong>Name:</strong> {{ Auth::guard('customer')->user()->full_name }}</div>
          <div><strong>Email:</strong> {{ Auth::guard('customer')->user()->customers_email }}</div>
        </div>
        <form action="customer_profile" method="get">
          @csrf
          <button type="submit" class="hc-navbar-dropdown-link">Profile</button>
        </form>
        <form action="logout" method="post">
          @csrf
          <button type="submit" class="hc-navbar-dropdown-link hc-navbar-logout">Logout</button>
        </form>
      </div>
    </div>
    @endauth
  </div>
  <script>
  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    var dropdown = document.getElementById('hc-user-dropdown');
    if (!dropdown) return;
    if (!dropdown.classList.contains('show')) return;
    if (!event.target.closest('.hc-navbar-user')) {
      dropdown.classList.remove('show');
    }
  });
  </script>
</header>

  <!-- Body -->
  <main class="hc-main">
    <section class="hc-hero" style="display: flex; justify-content: center; align-items: center; min-height: 400px; background: none;">
      <div class="hc-hero-img" style="width: 90vw; max-width: 1400px; background: none;">
        <img src="asset/img/group 7.png" alt="Hero background" style="width: 100%; height: auto; border-radius: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.12); display: block; background: none;" />
      </div>
    </section>
    <section class="hc-profile-section" style="display: flex; justify-content: center; align-items: center; min-height: 350px; background: transparent;">
      <img src="asset/img/group 8.png" alt="Artisan weaving" style="max-width: 100%; height: auto; border-radius: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.12); background: transparent;" />
    </section>
    <section class="hc-products-section">
      <h2 class="hc-section-title" style="font-family: 'Georgia', serif; font-size: 2.5rem; text-align: center; margin-bottom: 2rem; letter-spacing: 1px; border-bottom: none;">SHOP NOW</h2>
      <div class="hc-shop-categories" style="background: #ffe9d1; border-radius: 2rem; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
        <!-- Example category group, repeat for each category as needed -->
        <div class="hc-category-group" style="margin-bottom: 2.5rem;">
          <div class="hc-category-title" style="font-size: 2rem; font-family: 'Georgia', serif; color: #c86b3c; font-weight: bold; margin-bottom: 1rem;"></div>
          <div class="hc-products-grid" style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
            @foreach ($product as $p)
              @php
                $image = collect($images)->firstWhere('product_id', $p->product_id);
                $cat = collect($category)->firstWhere('category_id', $p->category_id);
              @endphp
              <div class="hc-product-card" style="background: #fff6ee; border-radius: 1.2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); width: 220px; padding: 1rem; display: flex; flex-direction: column; align-items: center;">
                <div class="hc-product-img-wrap" style="width: 180px; height: 180px; overflow: hidden; border-radius: 1rem; margin-bottom: 0.7rem; background: #fff; display: flex; align-items: center; justify-content: center;">
                  @if ($image)
                    <img src="{{ asset($image->img_url) }}" alt="Product Image" class="hc-product-img" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1rem;" />
                  @else
                    <div class="hc-no-img" style="color: #c86b3c;">No image</div>
                  @endif
                </div>
                <div class="hc-product-info" style="width: 100%; text-align: left;">
                  <div class="hc-product-category" style="font-size: 1rem; color: #c86b3c; font-weight: bold;">{{ $cat ? $cat->category_name : 'Unknown' }}</div>
                  <div class="hc-product-name" style="font-size: 1.1rem; font-weight: 600; color: #222;">{{ $p->product_name }}</div>
                  <div class="hc-product-price" style="font-size: 1rem; color: #222; float: right; font-weight: 500;">${{ number_format($p->product_price, 2) }}</div>
                  <form action="{{ url('/customer_product_detail/' . $p->product_id) }}" method="get" style="width:100%; margin-top: 0.7rem;">
                    @csrf
                    <button type="submit" class="hc-btn hc-btn-details" style="width: 100%; background: #c86b3c; color: #fff; border-radius: 0.7rem; padding: 0.5rem 0; font-size: 1rem; font-weight: bold; border: none;">Buy now</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        <!-- End example category group -->
      </div>
    </section>
  </main>
  <footer class="hc-footer">
    <div class="hc-footer-content">
      <span>© 2024 Hidden Craft. All rights reserved.</span>
      <span class="hc-footer-socials">
        <a href="#">🌐</a>
        <a href="#">🐦</a>
        <a href="#">📸</a>
      </span>
    </div>
  </footer>
</body>
</html>