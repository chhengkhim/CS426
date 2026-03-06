<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Reviews</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/seller_view_review.css') }}" rel="stylesheet">
</head>
<body>


<div class="review-container">
  <div class="review-header">
    <h2>Reviews for Order #{{ $order->order_id }}</h2>
    <a href="{{ url('seller_viewOrder') }}" class="back-to-orders-btn">Back to Orders</a>
  </div>

  <div class="order-details">
    <p><strong>Customer:</strong> {{ $orderItems->first()->customer_name ?? 'Unknown' }}</p>
    <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</p>
    <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
  </div>

  <h4 style="color: var(--primary-color); margin-bottom: 1.5rem;">Product Reviews</h4>

  @foreach($orderItems as $item)
    <div class="product-review-card">
      @if($item->img_url)
        <img src="{{ asset($item->img_url) }}" class="product-img" alt="{{ $item->product_name }}">
      @else
        <div class="product-img-placeholder"><span>No image</span></div>
      @endif
      <div class="product-info">
        <h5>{{ $item->product_name }}</h5>
        <p>Quantity: {{ $item->quantity }}</p>
        <p>Price: ${{ number_format($item->price_at_purchase, 2) }}</p>
        @if($item->rating)
          <div class="review-section">
            <div class="star-rating">
              @for($i = 1; $i <= 5; $i++)
                <span>@if($i <= $item->rating)&#9733;@else&#9734;@endif</span>
              @endfor
              <span class="review-date">
                {{ \Carbon\Carbon::parse($item->review_date)->format('M d, Y') }}
              </span>
            </div>
            <p>{{ $item->comment ?? 'No comment provided' }}</p>
          </div>
        @else
          <div class="no-review">
            No review submitted for this product
          </div>
        @endif
      </div>
    </div>
  @endforeach
</div>


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