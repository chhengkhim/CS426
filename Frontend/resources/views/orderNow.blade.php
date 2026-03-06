<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Place Your Order</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/order_now.css') }}" rel="stylesheet">
</head>
<body>
  

  <div class="order-now-page-main">
    <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
    <h1 class="order-now-main-title">Place Your Order</h1>

    @if (session('status'))
      <div class="order-now-alert order-now-alert-success">
        {{ session('status') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="order-now-alert order-now-alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="order-now-sections-wrapper">
      <div class="product-summary-section order-now-card">
        <h2 class="section-subtitle">Product Details</h2>
        @foreach ($product as $p)
          <div class="product-detail-item">
            <div class="product-image-container">
              @php
                $image = collect($images)->firstWhere('product_id', $p->product_id);
              @endphp
              @if ($image)
                <img src="{{ asset($image->img_url) }}" alt="Product Image" class="product-thumb">
              @else
                <div class="product-thumb-placeholder">
                  <span>No image</span>
                </div>
              @endif
            </div>
            <div class="product-info-summary">
              <h3 class="product-name">{{ $p->product_name }}</h3>
              @php
                $cat = collect($category)->firstWhere('category_id', $p->category_id);
              @endphp
              <p class="product-category">
                Category: {{ $cat ? $cat->category_name : 'Unknown' }}
              </p>
              <p class="product-description">{{ $p->product_description }}</p>
              <p class="product-qty-price">Quantity: {{ $quantity }} | Price per item: ${{ number_format($p->product_price, 2) }}</p>
              <p class="total-price">Total for this item: ${{ number_format($p->product_price * $quantity, 2) }}</p>
            </div>
          </div>
        @endforeach
      </div>

      <div class="shipping-payment-section order-now-card">
        <h2 class="section-subtitle">Shipping & Payment</h2>
        <form id="order-form" action="{{ url('/orderNow_process') }}" method="POST" class="order-now-form">
          @csrf
          <!-- Hidden fields for product and quantity -->
          <input type="hidden" name="product_id" value="{{ $product[0]->product_id }}">
          <input type="hidden" name="quantity" value="{{ $quantity }}">
          
          <div class="form-group">
            <label for="address_line" class="form-label">Shipping Address:</label>
            <input type="text" id="address_line" name="address_line" class="form-control" required>
          </div>
          <div class="form-row">
            <div class="form-group-half">
              <label for="city" class="form-label">City:</label>
              <input type="text" id="city" name="city" class="form-control" required>
            </div>
            <div class="form-group-half">
              <label for="state" class="form-label">State:</label>
              <input type="text" id="state" name="state" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label for="zip" class="form-label">Zip Code:</label>
            <input type="text" id="zip" name="zip" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="number" name="phone_number" id="phone_number" class="form-control" required>
          </div>
          
          <div class="payment-method-group">
            <h3 class="payment-subtitle">Choose your payment method</h3>
            <div class="radio-group">
              <input type="radio" name="payment_method" value="paypal" id="paypal" checked class="radio-input">
              <label for="paypal" class="radio-label">Pay with PayPal</label>
            </div>
            <div class="radio-group">
              <input type="radio" name="payment_method" value="credit_card" id="credit_card" class="radio-input">
              <label for="credit_card" class="radio-label">Pay with Credit Card</label>
            </div>
          </div>

          <div id="credit-card-details" class="credit-card-details" style="display:none;">
            <div class="form-group">
              <label for="card_number" class="form-label">Card Number:</label>
              <input type="text" id="card_number" name="card_number" class="form-control">
            </div>
            <div class="form-row">
              <div class="form-group-half">
                <label for="expiry_date" class="form-label">Expiry Date (MM/YY):</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" class="form-control">
              </div>
              <div class="form-group-half">
                <label for="cvv" class="form-label">CVV:</label>
                <input type="text" id="cvv" name="cvv" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label for="card_name" class="form-label">Name on Card:</label>
              <input type="text" id="card_name" name="card_name" class="form-control">
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="place-order-btn">Place Order</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
      const creditCardDetails = document.getElementById('credit-card-details');

      function toggleCreditCardDetails() {
        creditCardDetails.style.display = 
          (document.getElementById('credit_card').checked) ? 'block' : 'none';
      }

      paymentMethodRadios.forEach(function(elem) {
        elem.addEventListener('change', toggleCreditCardDetails);
      });

      // Initial check on page load
      toggleCreditCardDetails();

      document.getElementById('order-form').addEventListener('submit', function(e) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'credit_card') {
          const cardNumber = document.getElementById('card_number').value;
          const expiryDate = document.getElementById('expiry_date').value;
          const cvv = document.getElementById('cvv').value;
          const cardName = document.getElementById('card_name').value;
          
          if (!cardNumber || !expiryDate || !cvv || !cardName) {
            alert('Please fill in all credit card details');
            e.preventDefault();
            return false;
          }
        }
        return true;
      });
    });
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