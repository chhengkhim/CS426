<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css.css">
</head>
<body>

     <!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar p-0" id="sidebar">
                <div class="logo">Handcraft Admin</div>
                <div class="px-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/Home">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/customerManagement">
                                <i class="fas fa-users"></i> Customer Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="/sellerManagement">
                                <i class="fas fa-store"></i> Seller Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/productManagement">
                                <i class="fas fa-boxes me-2"></i> Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/reviewManagement">
                                <i class="fas fa-star"></i> Review Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/viewAllOrders">
                                <i class="fas fa-shopping-cart"></i> Order Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/allAdminMessages">
                                <i class="fa-solid fa-envelope"></i> Message Seller
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Moved Logout Button to Bottom -->
                    <div class="mt-auto" style="position: absolute; bottom: 20px; width: calc(100% - 24px);">
                        <form action="/logout" method="post" class="w-100">
                            @csrf
                            <button type="submit" class="nav-link text-start w-100 bg-transparent border-0">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

      <main class="col-md-9 col-lg-10 main-content">
        <h2 class="section-title">Product Reviews</h2>
        
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            @foreach($errors->all() as $error)
                {{ $error }}
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="review-table-container">
          <div class="table-responsive">
            <table class="review-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Product</th>
                  <th>Customer</th>
                  <th>Seller/Store</th>
                  <th>Rating</th>
                  <th>Review</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($reviews as $review)
                <tr>
                  <td>#{{ $review->order_id }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      @if($review->product_image)
                        <img src="{{ asset($review->product_image) }}" class="product-img me-2">
                      @else
                        <div class="product-img bg-secondary d-flex align-items-center justify-content-center me-2">
                          <i class="fas fa-box-open"></i>
                        </div>
                      @endif
                      <div>
                        <div>{{ $review->product_name }}</div>
                        <span class="id-label">ID: {{ $review->product_id }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      @if($review->customer_image)
                        <img src="{{ asset($review->customer_image) }}" class="customer-avatar me-2">
                      @else
                        <div class="customer-avatar bg-secondary d-flex align-items-center justify-content-center me-2">
                          <i class="fas fa-user"></i>
                        </div>
                      @endif
                      <div>
                        <div>{{ $review->customer_name }}</div>
                        <span class="id-label">ID: {{ $review->customer_id }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div>
                      <div>{{ $review->seller_name }}</div>
                      <div class=" small">{{ $review->store_name }}</div>
                      <span class="id-label">ID: {{ $review->seller_id }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="star-rating">
                      @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                          <i class="fas fa-star"></i>
                        @else
                          <i class="far fa-star"></i>
                        @endif
                      @endfor
                      <span class="id-label">{{ $review->rating }}/5</span>
                    </div>
                  </td>
                  <td class="review-message">
                    {{ $review->review_message ?: 'No review message' }}
                  </td>
                  <td>
                    {{ \Carbon\Carbon::parse($review->review_date)->format('M d, Y') }}
                    <div class="text-muted small">
                      {{ \Carbon\Carbon::parse($review->review_date)->format('h:i A') }}
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @if($reviews->isEmpty())
        <div class="alert alert-info">
            No product reviews available.
        </div>
        @endif

        </div>
      </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  
  // Better touch handling for mobile
  menuToggle.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });
  
  // Close sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth < 992 && sidebar.classList.contains('active') && 
        !sidebar.contains(e.target) && e.target !== menuToggle) {
      sidebar.classList.remove('active');
    }
  });
  
  // Handle window resize
  function handleResize() {
    if (window.innerWidth >= 992) {
      sidebar.classList.remove('active');
    }
  }
  
  window.addEventListener('resize', handleResize);
  handleResize(); // Run once on load
});
    </script>
</body>
</html>