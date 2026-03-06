<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products by Category</title>
  <link rel="stylesheet" href="/asset/customer_viewSpecificProduct_category.css">
</head>
<body>
  <div class="cvspc-container">
    <div class="cvspc-header">
      <a href="/customer_Home" class="cvspc-btn">Back to Home</a>
      <a href="{{ url('viewCart') }}" class="cvspc-btn">View your Cart</a>
      <a href="{{ url('customer_viewOrder') }}" class="cvspc-btn">View your Orders</a>
    </div>
    <form action="{{ url('/customer_viewSpecificProduct_category') }}" method="GET" class="cvspc-category-form">
      <label for="category_id" class="cvspc-label">View Product by Category:</label>
      <select name="category_id" id="category_id" class="cvspc-select" required>
        <option value="">-- Select Category --</option>
        @foreach ($category as $cat)
          <option value="{{ $cat->category_id }}" {{ $selectedCategory && $cat->category_id == $selectedCategory->category_id ? 'selected' : '' }}>
            {{ $cat->category_name }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="cvspc-btn cvspc-btn-primary">Filter</button>
    </form>
    @if (session('status'))
      <div class="cvspc-alert cvspc-alert-success">
        {{ session('status') }}
      </div>
    @endif
    <h2 class="cvspc-title">
      @if($selectedCategory)
        Products in Category: {{ $selectedCategory->category_name }}
      @else
        All Products
      @endif
    </h2>
    <div class="cvspc-products-table">
      <div class="cvspc-products-header">
        <span>Product Photo</span>
        <span>Product Name</span>
        <span>Category</span>
        <span>Price</span>
        <span>Actions</span>
      </div>
      @forelse ($products as $product)
        @php
          $productImage = $images->firstWhere('product_id', $product->product_id);
          $productCategory = $category->firstWhere('category_id', $product->category_id);
        @endphp
        <div class="cvspc-product-row">
          <div>
            @if ($productImage)
              <img src="{{ asset($productImage->img_url) }}" alt="{{ $product->product_name }}" class="cvspc-product-img">
            @else
              <div class="cvspc-product-img cvspc-no-img">No image</div>
            @endif
          </div>
          <div>{{ $product->product_name }}</div>
          <div>{{ $productCategory ? $productCategory->category_name : 'Unknown' }}</div>
          <div>${{ number_format($product->product_price, 2) }}</div>
          <div>
            <a href="{{ url('/customer_product_detail/' . $product->product_id) }}" class="cvspc-btn cvspc-btn-details">View Details</a>
          </div>
        </div>
      @empty
        <div class="cvspc-no-products">No products found in this category</div>
      @endforelse
    </div>
  </div>
</body>
</html>