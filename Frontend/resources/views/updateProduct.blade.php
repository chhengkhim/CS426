<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Product</title>
  <link rel="stylesheet" href="/asset/styles.css"> {{-- For main site elements like general buttons, footer --}}
  <link rel="stylesheet" href="/asset/update_product.css"> {{-- Page-specific styles --}}
  
</head>
<body>
  <header class="up-header">
    <div class="up-header-content">
      <a href="{{ url('seller_Home') }}" class="up-back-btn">Back to Home</a>
      <h1 class="up-page-title">Update Product</h1>
    </div>
  </header>

  <main class="up-main-content">
    <div class="up-form-container">
      <form action="{{ url('process_updateProduct') }}" method="POST" enctype="multipart/form-data" class="up-form">
        @csrf

        @foreach ($product as $prod)
          <input type="hidden" name="product_id" value="{{ $prod->product_id }}">

          <div class="up-form-group up-image-group">
            <label class="up-label">Current Image:</label>
            @if(count($images) > 0 && isset($images[0]->img_url))
              <img src="{{ asset($images[0]->img_url) }}" alt="Product Image" class="up-current-img">
            @else
              <div class="up-no-img">No image available.</div>
            @endif
          </div>

          <div class="up-form-group">
            <label for="image" class="up-label">New Image (Optional):</label>
            <input type="file" name="image" id="image" class="up-file-input">
          </div>

          <div class="up-form-group">
            <label for="product_name" class="up-label">Product Name:</label>
            <input type="text" name="product_name" id="product_name" value="{{ $prod->product_name }}" class="up-input" required>
          </div>

          <div class="up-form-group">
            <label for="category_select" class="up-label">Product Category:</label>
            <select name="category_name" id="category_select" onchange="toggleCategoryInput()" class="up-select" required>
              <option value="">-- Select Category --</option>
              @foreach ($category as $cat)
                <option value="{{ $cat->category_name }}" {{ $cat->category_id == $prod->category_id ? 'selected' : '' }}>
                  {{ $cat->category_name }}
                </option>
              @endforeach
              <option value="__other__">Other</option>
            </select>
          </div>

          <div id="other_category_div" class="up-form-group" style="display: none;">
            <label for="other_category" class="up-label">New Category Name:</label>
            <input type="text" name="other_category" id="other_category" class="up-input">
          </div>

          <div class="up-form-group">
            <label for="product_description" class="up-label">Product Description:</label>
            <textarea name="product_description" id="product_description" class="up-textarea" rows="4" required>{{ $prod->product_description }}</textarea>
          </div>

          <div class="up-form-group">
            <label for="product_price" class="up-label">Product Price:</label>
            <input type="number" name="product_price" id="product_price" value="{{ $prod->product_price }}" class="up-input" step="0.01" min="0" required>
          </div>

          <div class="up-form-group">
            <label for="stock_quantity" class="up-label">Stock Quantity:</label>
            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ $prod->stock_quantity }}" class="up-input" min="0" required>
          </div>
        @endforeach

        <button type="submit" class="up-submit-btn">Update Product</button>
      </form>

      @if ($errors->any())
        <div class="up-alert up-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
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

  <script>
    function toggleCategoryInput() {
      const select = document.getElementById('category_select');
      const otherInputDiv = document.getElementById('other_category_div');

      if (select.value === '__other__') {
        otherInputDiv.style.display = 'block';
        document.getElementById('other_category').setAttribute('required', 'required');
      } else {
        otherInputDiv.style.display = 'none';
        document.getElementById('other_category').removeAttribute('required');
      }
    }
  </script>
</body>
</html>
