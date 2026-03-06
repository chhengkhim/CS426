<!DOCTYPE html>
<html>
<head>
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="/asset/styles.css">
    <link rel="stylesheet" href="/asset/viewcart.css">
</head>
<body>
    

    <main class="cart-page-main">
        <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
        <div class="cart-container">
            <h1 class="cart-main-title">Your Shopping Cart</h1>
            <p class="cart-breadcrumb">Home / Cart</p>

            <div class="cart-content-wrapper">
                <div class="cart-items-section">
                    <h2 class="cart-section-title">Cart Items</h2>

                    @if (session('status'))
                    <div class="cart-alert cart-alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if($cartItems->isEmpty())
                        <div class="cart-empty-message">Your cart is empty</div>
                    @else
                        <div class="cart-items-list-header">
                            <span class="ci-header-product">Product</span>
                            <span class="ci-header-price">Price</span>
                            <span class="ci-header-quantity">Quantity</span>
                            <span class="ci-header-total">Total</span>
                        </div>
                        <div class="cart-items-list">
                            @foreach($cartItems as $productId => $items)
                                @php
                                    $firstItem = $items->first();
                                    $totalPrice = $firstItem->product_price * $items->sum('quantity');
                                @endphp
                                <div class="ci-card">
                                    <div class="ci-img-wrap">
                                        @if($firstItem->img_url)
                                            <img src="{{ asset($firstItem->img_url) }}" alt="{{ $firstItem->product_name }}" class="ci-img">
                                        @else
                                            <div class="ci-img ci-no-img">No image</div>
                                        @endif
                                    </div>
                                    <div class="ci-details">
                                        <div class="ci-title">{{ $firstItem->product_name }}</div>
                                        <div class="ci-price-old">${{ number_format($firstItem->product_price, 2) }}</div>
                                        <div class="ci-price-current">${{ number_format($firstItem->product_price, 2) }}</div>
                                    </div>
                                    <div class="ci-quantity-control">
                                        <form action="process_updateQuantityCartItem" method="POST" class="ci-qty-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $productId }}">
                                            <button type="button" class="ci-qty-btn" onclick="this.nextElementSibling.stepDown(); this.form.submit();">-</button>
                                            <input type="number" name="quantity" value="{{ $items->sum('quantity') }}" min="1" max="{{ $firstItem->stock_quantity }}" class="ci-qty-input">
                                            <button type="button" class="ci-qty-btn" onclick="this.previousElementSibling.stepUp(); this.form.submit();">+</button>
                                        </form>
                                    </div>
                                    <div class="ci-item-total">${{ number_format($totalPrice, 2) }}</div>
                                    <form action="{{ route('process_removeItemFromCart') }}" method="POST" class="ci-remove-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                        <button type="submit" class="ci-remove-btn">✕</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($errors->any())
                    <div class="cart-alert cart-alert-danger cart-error-list">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <div class="order-summary-section">
                    <h2 class="cart-section-title">Order Summary</h2>
                    <div class="os-line">
                        <span>Subtotal:</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>
                    
                    <div class="os-total">
                        <span>Total:</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>
                 
                    <a href="{{ route('orderFromcart_view') }}" class="os-checkout-btn">Proceed to Checkout</a>
                    <a href="/customer_Home" class="os-continue-shopping">← Continue Shopping</a>
                </div>
            </div>
        </div>
    </main>

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