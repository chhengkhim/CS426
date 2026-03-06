<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Reviews</title>
  <link rel="stylesheet" href="/asset/customer_view_reviews.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <main class="cvr-main-content">
    <div class="cvr-container">
      <div class="cvr-header">
        <a href="/customer_viewOrder" class="cvr-back-btn">
          <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        <h2 class="cvr-page-title">My Reviews</h2>
        <div class="cvr-order-info">
          <span>Order #{{ $order->order_id }}</span>
          <span>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</span>
        </div>
      </div>

      @if (session('success'))
        <div class="cvr-alert cvr-alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="cvr-alert cvr-alert-error">
          {{ session('error') }}
        </div>
      @endif

      <div class="cvr-products-grid">
        @foreach($orderItems as $item)
          <div class="cvr-product-card">
            <div class="cvr-product-header">
              @if($item->img_url)
                <img src="{{ asset($item->img_url) }}" alt="{{ $item->product_name }}" class="cvr-product-img">
              @else
                <div class="cvr-product-img cvr-no-img">
                  <i class="fas fa-image"></i>
                </div>
              @endif
              <h3 class="cvr-product-name">{{ $item->product_name }}</h3>
            </div>

            <div class="cvr-review-section">
              @if($item->rating)
                <div class="cvr-review-exists">
                  <div class="cvr-rating">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= $item->rating)
                        <i class="fas fa-star cvr-star-filled"></i>
                      @else
                        <i class="far fa-star cvr-star-empty"></i>
                      @endif
                    @endfor
                    <span class="cvr-review-date">{{ \Carbon\Carbon::parse($item->review_date)->format('M d, Y') }}</span>
                  </div>
                  <p class="cvr-review-comment">{{ $item->comment }}</p>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </main>

  <footer class="cvr-footer">
    <div class="cvr-footer-content">
      <span>© {{ date('Y') }} Hidden Craft. All rights reserved.</span>
    </div>
  </footer>
</body>
</html>