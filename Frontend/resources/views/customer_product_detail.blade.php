<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Details</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/customer_product_detail.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

  <div class="product-detail-page-wrapper">
    <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
    <h1 class="product-page-main-title">Product Details</h1>

    @if (session('status'))
      <div class="product-page-alert product-page-alert-success">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="product-page-alert product-page-alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @foreach ($product as $p)
      <div class="product-main-card">
        <div class="product-image-area">
          @php
            $image = collect($images)->firstWhere('product_id', $p->product_id);
          @endphp
          @if ($image)
            <img src="{{ asset($image->img_url) }}" alt="Product Image" class="product-display-img">
          @else
            <div class="product-image-placeholder">
              <i class="fas fa-box-open"></i>
              <span>No Image Available</span>
            </div>
          @endif
        </div>

        <div class="product-info-area">
          <h2 class="product-title">{{ $p->product_name }}</h2>
          <p class="product-description-brief">{{ $p->product_description }}</p>
          <p class="product-category">
            Category: 
            @php
              $cat = collect($category)->firstWhere('category_id', $p->category_id);
            @endphp
            <span>{{ $cat ? $cat->category_name : 'Unknown' }}</span>
          </p>
          
          <div class="product-price-and-store">
            <span class="product-price">${{ number_format($p->product_price, 2) }}</span>
            <a href="{{ url('/customer_viewStorepage/' . $p->product_id) }}" class="view-store-link"><i class="fas fa-store"></i> View Store</a>
          </div>

          <div class="product-actions-section">
            <div class="product-quantity-control">
              <button type="button" class="qty-adjust-btn" onclick="changeQty('quantity_{{ $p->product_id }}', -1)">-</button>
              <input type="number" id="quantity_{{ $p->product_id }}" value="1" min="1" class="qty-input-field">
              <button type="button" class="qty-adjust-btn" onclick="changeQty('quantity_{{ $p->product_id }}', 1)">+</button>
            </div>
            
            <form method="GET" action="{{ url('/orderNow/'. $p->product_id) }}" class="action-form-btn">
              <input type="hidden" name="quantity" id="order_quantity_{{ $p->product_id }}" value="1">
              <button type="submit" class="action-button order-button"><i class="fas fa-truck"></i> Order Now</button>
            </form>
            
            <form method="POST" action="{{ url('/Process_addToCart/'. $p->product_id) }}" class="action-form-btn">
              @csrf
              <input type="hidden" name="product_id" value="{{ $p->product_id }}">
              <input type="hidden" name="quantity" id="cart_quantity_{{ $p->product_id }}" value="1">
              <button type="submit" class="action-button add-to-cart-button"><i class="fas fa-cart-plus"></i> Add to Cart</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <script>
    function changeQty(inputId, amount) {
      const qtyInput = document.getElementById(inputId);
      let current = parseInt(qtyInput.value, 10) || 1;
      current += amount;
      if (current < 1) current = 1;
      qtyInput.value = current;
      
      const productId = inputId.replace('quantity_', '');
      
      const orderQty = document.getElementById('order_quantity_' + productId);
      const cartQty = document.getElementById('cart_quantity_' + productId);
      
      if (orderQty) orderQty.value = current;
      if (cartQty) cartQty.value = current;
    }
  </script>

  <style>
    .modern-back-btn {
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
      margin-left: 18px;
    }
    .modern-back-btn:hover {
      background: #a14d2a;
      color: #fff;
      box-shadow: 0 4px 16px rgba(184,92,56,0.16);
      text-decoration: none;
    }
  </style>
</body>
</html>