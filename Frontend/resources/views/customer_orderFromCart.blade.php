<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Your Order</title>
  <link rel="stylesheet" href="{{ asset('asset/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('asset/customer_order_from_cart.css') }}">
</head>
<body>
  

  <div class="order-page-main">
    <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
    <h1 class="order-main-title">Review Your Order</h1>

    @if(count($outOfStockItems) > 0)
      <div class="order-alert order-alert-warning">
        <h4>Some items unavailable:</h4>
        <ul>
          @foreach($outOfStockItems as $item)
            <li>
              {{ $item->product_name }} - 
              Available: {{ $item->available }}, 
              Requested: {{ $item->cart_quantity }}
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="order-sections-wrapper">
      <div class="order-summary-section order-card">
        <h2 class="order-section-title">Order Summary</h2>
        <div class="order-summary-table">
          <div class="table-header">
            <div class="th-product">Product</div>
            <div>Price</div>
            <div>Quantity</div>
            <div>Subtotal</div>
          </div>
          <div class="table-body">
            @foreach($inStockItems as $item)
              <div class="table-row">
                <div class="td-product">
                  @if($item->img_url)
                    <img src="{{ asset($item->img_url) }}" alt="Product Image" class="product-thumb">
                  @endif
                  <span>{{ $item->product_name }}</span>
                </div>
                <div class="td-price">${{ number_format($item->product_price, 2) }}</div>
                <div class="td-quantity">{{ $item->cart_quantity }}</div>
                <div class="td-subtotal">${{ number_format($item->subtotal, 2) }}</div>
              </div>
            @endforeach
            <div class="table-total">
              <div class="total-label">Total</div>
              <div class="total-value">${{ number_format($cartTotal, 2) }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="shipping-payment-section">
        <div class="shipping-info-section order-card">
          <h2 class="order-section-title">Shipping Information</h2>
          <form id="order-form" action="{{ route('processCartCheckout') }}" method="POST" class="order-form">
            @csrf
            
            <div class="form-group">
              <label for="address_line" class="form-label">Shipping Address</label>
              <input type="text" class="form-control" id="address_line" name="address_line" required>
            </div>
            
            <div class="form-row">
              <div class="form-group-half">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" required>
              </div>
              <div class="form-group-quarter">
                <label for="state" class="form-label">State</label>
                <input type="text" class="form-control" id="state" name="state" required>
              </div>
              <div class="form-group-quarter">
                <label for="zip" class="form-label">Zip Code</label>
                <input type="text" class="form-control" id="zip" name="zip" required>
              </div>
            </div>

            <div class="form-group">
              <label for="phone_number" class="form-label">Phone Number:</label>
              <input type="number" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            
            <div class="payment-method-section">
              <h3 class="section-subtitle">Payment Method</h3>
              <div class="radio-group">
                <input type="radio" name="payment_method" id="paypal" value="paypal" checked class="radio-input">
                <label for="paypal" class="radio-label">Pay with PayPal</label>
              </div>
              <div class="radio-group">
                <input type="radio" name="payment_method" id="credit_card" value="credit_card" class="radio-input">
                <label for="credit_card" class="radio-label">Pay with Credit Card</label>
              </div>
              
              <div id="credit-card-details" class="credit-card-details">
                <div class="form-group">
                  <label for="card_number" class="form-label">Card Number</label>
                  <input type="text" class="form-control" id="card_number" name="card_number">
                </div>
                <div class="form-row">
                  <div class="form-group-half">
                    <label for="expiry_date" class="form-label">Expiry Date (MM/YY)</label>
                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                  </div>
                  <div class="form-group-half">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="cvv" name="cvv">
                  </div>
                </div>
                <div class="form-group">
                  <label for="card_name" class="form-label">Name on Card</label>
                  <input type="text" class="form-control" id="card_name" name="card_name">
                </div>
              </div>
            </div>

            <div class="form-actions">
              @if($canCheckout)
                <button type="submit" class="order-complete-btn">Complete Order</button>
              @else
                <div class="order-alert order-alert-info">
                  Please adjust your cart before checking out
                </div>
                <a href="{{ route('viewCart') }}" class="back-to-cart-btn">Back to Cart</a>
              @endif
            </div>
          </form>
        </div>
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
            e.preventDefault();
            alert('Please fill in all credit card details');
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