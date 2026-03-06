<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Orders</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/customer_view_order.css') }}" rel="stylesheet">
</head>
<body>
  
<header class="hc-navbar">
  <div class="hc-navbar-content">
    <a href="/" class="hc-navbar-logo">Hidden Craft</a>
    <nav class="hc-navbar-links">
      <a href="/customer_Home" class="hc-navbar-link">Home</a>
      <a href="customer_viewOrder" class="hc-navbar-link">Orders</a>
      <a href="viewCart" class="hc-navbar-link">
        <span class="hc-cart-icon">🛒</span>
      </a>
    </nav>
    @auth('customer')
    <div class="hc-navbar-user">
      @if (Auth::guard('customer')->user()->customer_profile_images)
        <img src="{{ asset(Auth::guard('customer')->user()->customer_profile_images) }}" alt="Customer Image" class="hc-navbar-avatar" onclick="document.getElementById('hc-user-dropdown').classList.toggle('show')">
      @else
        <div class="hc-navbar-avatar hc-navbar-avatar-placeholder" onclick="document.getElementById('hc-user-dropdown').classList.toggle('show')">?</div>
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
</header>

  <div class="orders-page-main">
    <h1 class="page-title">Your Orders</h1>

    <div class="previous-page">
      <a href="/customer_Home" class="back-to-home-btn">Back to Home</a>
    </div>

    @if (session('success'))
      <div class="order-alert order-alert-success">
        {{ session('success') }}
      </div>
    @endif
    @if (session('error'))
      <div class="order-alert order-alert-danger">
        {{ session('error') }}
      </div>
    @endif

    @php
      // Group orders by order_id since SQL returns one row per item
      $groupedOrders = [];
      foreach ($orders as $order) {
          $groupedOrders[$order->order_id][] = $order;
      }
    @endphp

    @forelse ($groupedOrders as $orderId => $orderItems)
      @php
        $firstItem = $orderItems[0];
        $orderTotal = 0;
        
        // Determine overall order status
        $allStatuses = collect($orderItems)->pluck('item_status')->unique();
        if ($allStatuses->count() === 1) {
            $orderStatus = $allStatuses->first();
        } else {
            // Show the "most incomplete" status
            if ($allStatuses->contains('pending')) {
                $orderStatus = 'pending';
            } elseif ($allStatuses->contains('shipped')) {
                $orderStatus = 'shipped';
            } elseif ($allStatuses->contains('delivered')) {
                $orderStatus = 'delivered';
            } else {
                $orderStatus = $allStatuses->last();
            }
        }
      @endphp
      
      <div class="order-card">
        <div class="order-header">
          <div>
            <h3 class="order-id">Order #{{ $orderId }}</h3>
            <span class="order-date">Placed on {{ date('M d, Y h:i A', strtotime($firstItem->created_at)) }}</span>
          </div>
          <span class="status-badge status-{{ strtolower($orderStatus) }}">
            {{ ucfirst($orderStatus) }}
          </span>
        </div>

        <div class="order-items-list">
    @foreach ($orderItems as $item)
        @php
            $itemTotal = $item->price_at_purchase * $item->quantity;
            $orderTotal += $itemTotal;
            $hasReview = isset($reviews[$orderId]) && 
                         $reviews[$orderId]->where('product_id', $item->product_id)->count() > 0;
        @endphp
        <div class="order-item">
            <div class="item-details">
                @if($item->img_url)
                    <img src="{{ asset($item->img_url) }}" 
                         class="product-img" 
                         alt="{{ $item->product_name }}">
                @else
                    <div class="product-img-placeholder">
                        <span>No image</span>
                    </div>
                @endif
                <div class="product-info">
                    <h4 class="product-name">{{ $item->product_name }}</h4>
                    <p class="product-category">
                        Category: {{ $item->category_name ?? 'Uncategorized' }}
                    </p>
                    <p class="item-qty-price">Qty: {{ $item->quantity }} | Price: ${{ number_format($item->price_at_purchase, 2) }}</p>
                </div>
            </div>
            <div class="item-summary">
                <span class="item-status status-badge status-{{ strtolower($item->item_status) }}">
                    {{ ucfirst($item->item_status) }}
                </span>
                <span class="item-total">${{ number_format($itemTotal, 2) }}</span>
            </div>
            <div class="item-actions">
                <a href="{{ route('customer_product_detail', $item->product_id) }}" 
                   class="action-btn view-product-btn">
                    View Product
                </a>
                @if($item->item_status === 'pending')
                    <form method="POST" action="{{ url('cancelOrReceivedOrder/'. $orderId) }}" onsubmit="return confirm('Are you sure you want to cancel this item?')">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                        <button type="submit" class="action-btn cancel-item-btn">
                            Cancel Item
                        </button>
                    </form>
                @elseif($item->item_status === 'delivered')
                    <form method="POST" action="{{ url('cancelOrReceivedOrder/'. $orderId) }}" onsubmit="return confirm('Have you received this item?')">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                        <button type="submit" class="action-btn mark-received-btn">
                            Mark Received
                        </button>
                    </form>
                @elseif($item->item_status === 'received')
                    @if($hasReview)
                        <a href="{{ url('customer_viewReviews/'. $orderId) }}" class="action-btn write-review-btn">
                            View Review
                        </a>
                    @else
                        <a href="{{ url('showReviewForm/'. $orderId.'/'.$item->product_id) }}" class="action-btn write-review-btn">
                            Write Review
                        </a>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
</div>

        <div class="order-footer">
          <div class="shipping-details">
            <p class="shipping-address">Shipping to: {{ $firstItem->shipping_address}}</p>
            <p class="phone-number">Phone number: {{$firstItem->phone_number }}</p>
          </div>
          <h4 class="order-total">Order Total: ${{ number_format($orderTotal, 2) }}</h4>
        </div>
      </div>
    @empty
      <div class="no-orders-message">
        <p>You haven't placed any orders yet.</p>
        <a href="/customer_Home" class="back-to-home-btn">Start Shopping</a>
      </div>
    @endforelse
  </div>

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

  <script>
    document.addEventListener('click', function(event) {
      var dropdown = document.getElementById('hc-user-dropdown');
      if (!dropdown) return;
      if (!dropdown.classList.contains('show')) return;
      if (!event.target.closest('.hc-navbar-user')) {
        dropdown.classList.remove('show');
      }
    });
  </script>
</body>
</html>