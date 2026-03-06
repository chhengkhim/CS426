<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Orders</title>
  <link rel="stylesheet" href="/asset/styles.css">
  <link rel="stylesheet" href="/asset/seller_view_order.css">
</head>
<body>
  <main class="svo-main-content">
    <div class="svo-container">
      <div class="svo-header">
        <a href="seller_Home" class="svo-back-btn">Back to Home</a>
        <h2 class="svo-page-title">View Orders</h2>
      </div>

      @if (session('status'))
        <div class="svo-alert svo-alert-success">
          {{ session('status') }}
        </div>
      @endif

      @if($orders->isEmpty())
        <div class="svo-alert svo-alert-info">No orders found.</div>
      @else
        <div class="svo-orders-grid">
          @foreach($orders as $orderId => $orderItems)
            @foreach($orderItems as $item)
              @php
                $product = $products[$item->product_id] ?? null;
                $customer = $customers[$item->customer_id] ?? null;
                $image = $images[$item->product_id]->first() ?? null;
                $category = $categories[$product->category_id] ?? null;
              @endphp
              <div class="svo-order-card">
                <div class="svo-order-card-header">
                  <span class="svo-order-id">Order #{{ $orderId }}</span>
                  <span class="svo-order-date">{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}</span>
                </div>
                
                <div class="svo-product-info">
                  <div class="svo-product-img-wrap">
                    @if ($image)
                      <img src="{{ asset($image->img_url) }}" alt="Product Image" class="svo-product-img">
                    @else
                      <div class="svo-product-img svo-no-img">No image</div>
                    @endif
                  </div>
                  <div class="svo-product-details">
                    <h3 class="svo-product-name">{{ $product->product_name ?? 'N/A' }}</h3>
                    <span class="svo-product-category">Category: {{ $category->category_name ?? 'Unknown' }}</span>
                    <span class="svo-customer-name">Customer: {{ $customer->full_name ?? 'Unknown' }}</span>
                    <span class="svo-quantity">Qty: {{ $item->quantity }}</span>
                    <span class="svo-price">Total: ${{ number_format($item->price_at_purchase * $item->quantity, 2) }}</span>
                  </div>
                </div>

                <div class="svo-shipping-details">
                  <span>Shipping: {{ $item->shipping_address ?? 'N/A' }}</span>
                  <span>Phone: {{ $item->phone_number ?? 'N/A' }}</span>
                </div>

                <div class="svo-status-actions">
                  <span class="svo-status-label">Status: <span class="svo-status svo-status-{{ $item->status }}">{{ ucfirst($item->status) }}</span></span>
                  <form class="svo-status-form" method="POST" action="{{ url('/orders/update-status/'.$orderId) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <select name="status" onchange="this.form.submit()" class="svo-select-status">
                      @if($item->status == 'pending')
                        <option value="pending" selected>Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered" disabled>Delivered</option>
                        <option value="cancelled">Cancel</option>
                      @elseif($item->status == 'shipped')
                        <option value="pending" disabled>Pending</option>
                        <option value="shipped" selected>Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled" disabled>Cancel</option>
                      @elseif($item->status == 'delivered')
                        <option value="pending" disabled>Pending</option>
                        <option value="shipped" disabled>Shipped</option>
                        <option value="delivered" selected>Delivered</option>
                        <option value="cancelled" disabled>Cancel</option>
                      @elseif($item->status == 'cancelled')
                        <option value="pending" disabled>Pending</option>
                        <option value="shipped" disabled>Shipped</option>
                        <option value="delivered" disabled>Delivered</option>
                        <option value="cancelled" selected>Cancel</option>
                      @elseif($item->status == 'received')
                        <option value="pending" disabled>Pending</option>
                        <option value="shipped" disabled>Shipped</option>
                        <option value="delivered" disabled>Delivered</option>
                        <option value="cancelled" disabled>Cancel</option>
                      @endif
                    </select>
                  </form>
                  <a href="{{ url('viewOrderReviews/'. $orderId) }}" class="svo-btn svo-btn-view-reviews">View Reviews</a>
                </div>
              </div>
            @endforeach
          @endforeach
        </div>
      @endif
    </div>
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