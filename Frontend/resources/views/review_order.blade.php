<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Product</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/review_order.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

  <div class="review-page-main">
    <h1 class="page-title">Review Product</h1>

    @if (session('error'))
      <div class="review-alert review-alert-danger">
        {{ session('error') }}
      </div>
    @endif

    <div class="review-product-card">
      <div class="product-details-section">
        <div class="product-image-container">
          @if($product->img_url)
            <img src="{{ asset($product->img_url) }}" class="product-img" alt="{{ $product->product_name }}">
          @else
            <div class="product-img-placeholder">
              <span>No Image</span>
            </div>
          @endif
        </div>
        <div class="product-info">
          <h2 class="product-name">{{ $product->product_name }}</h2>
        </div>
      </div>

      <div class="review-form-section">
        <form method="POST" action="{{ route('order.submit_review', $order_id) }}" class="review-form">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->product_id }}">
          
          <div class="form-group">
            <label class="form-label">Your Rating</label>
            <div class="star-rating">
              @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star-fill" data-rating="{{ $i }}"></i>
              @endfor
              <input type="hidden" name="rating" id="rating" value="1" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="comment" class="form-label">Your Review</label>
            <textarea class="form-control" id="comment" name="comment" rows="5" 
                      placeholder="Share your experience with this product..."></textarea>
          </div>
          
          <div class="form-actions">
            <button type="submit" class="submit-review-btn">Submit Review</button>
            <a href="{{ url('customer_viewOrder') }}" class="back-to-orders-btn">Back to Orders</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const stars = document.querySelectorAll('.star-rating .bi-star-fill');
      const ratingInput = document.getElementById('rating');

      function updateStars(selectedRating) {
        stars.forEach((star, index) => {
          if (index < selectedRating) {
            star.classList.add('active');
          } else {
            star.classList.remove('active');
          }
        });
      }

      stars.forEach(star => {
        star.addEventListener('click', function() {
          const rating = parseInt(this.getAttribute('data-rating'), 10);
          ratingInput.value = rating;
          updateStars(rating);
        });

        star.addEventListener('mouseover', function() {
          const hoverRating = parseInt(this.getAttribute('data-rating'), 10);
          updateStars(hoverRating);
        });

        star.addEventListener('mouseout', function() {
          const currentRating = parseInt(ratingInput.value, 10);
          updateStars(currentRating);
        });
      });

      // Set initial state based on current rating input value
      updateStars(parseInt(ratingInput.value, 10));
    });
  </script>
</body>
</html>