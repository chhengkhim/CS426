<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <link rel="stylesheet" href="/asset/styles.css"> {{-- For main site elements like general buttons, footer --}}
  <link rel="stylesheet" href="/asset/add_product.css"> {{-- Page-specific styles --}}
</head>
<body>
  <header class="ap-header">
    <div class="ap-header-content">
      <a href="seller_Home" class="ap-back-btn">Back to Home</a>
      <h1 class="ap-page-title">Add New Product</h1>
    </div>
  </header>

  <main class="ap-main-content">
    <div class="ap-form-container">
      <form action="process_addProduct" method="POST" enctype="multipart/form-data" class="ap-form">
        @csrf
        
        <div class="ap-form-group">
          <label for="product_image" class="ap-label">Product Image:</label>
          <input type="file" name="image" id="product_image" class="ap-file-input" required>
        </div>

        <div class="ap-form-group">
          <label for="product_name" class="ap-label">Product Name:</label>
          <input type="text" name="product_name" id="product_name" class="ap-input" required>
        </div>

        <div class="ap-form-group">
          <label for="category_select" class="ap-label">Product Category:</label>
          <select name="category_name" id="category_select" onchange="toggleCategoryInput()" class="ap-select" required>
            <option value="">-- Select Category --</option>
            @foreach ($category as $cat)
              <option value="{{ $cat->category_name }}">{{ $cat->category_name }}</option>
            @endforeach
            <option value="__other__">Other</option>
          </select>
        </div>

        <div id="other_category_div" class="ap-form-group" style="display: none;">
          <label for="other_category" class="ap-label">New Category Name:</label>
          <input type="text" name="other_category" id="other_category" class="ap-input">
        </div>

        <div class="ap-form-group">
          <label for="product_description" class="ap-label">Product Description:</label>
          <textarea name="product_description" id="product_description" class="ap-textarea" rows="4" required></textarea>
        </div>

        <div class="ap-form-group">
          <label for="product_price" class="ap-label">Product Price:</label>
          <input type="number" name="product_price" id="product_price" class="ap-input" step="0.01" min="0" required>
        </div>

        <div class="ap-form-group">
          <label for="stock_quantity" class="ap-label">Stock Quantity:</label>
          <input type="number" name="stock_quantity" id="stock_quantity" class="ap-input" min="0" required>
        </div>

        <button type="submit" class="ap-submit-btn">Add Product</button>
      </form>

      @if ($errors->any())
        <div class="ap-alert ap-alert-danger">
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