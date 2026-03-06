<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - Handcraft Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2b2b45;
            --secondary-color: #50508e;
            --accent-color: #ff9191;
            --dark-bg: #1c1c32;
            --light-text: #ffffff;
            --muted-text: rgba(255, 255, 255, 0.7);
        }
        
        body {
            background-color: var(--dark-bg);
            color: var(--light-text);
            font-family: 'Poppins', sans-serif;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            position: fixed;
            width: 280px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .nav-link {
            color: var(--muted-text);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: var(--secondary-color);
            color: var(--light-text);
        }
        
        .logo {
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            color: var(--light-text);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .profile-card {
            background-color: var(--primary-color);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
            border: none;
        }

        .profile-card-header {
            background-color: var(--secondary-color);
            color: var(--light-text);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
        }

        .order-card {
            background-color: var(--primary-color);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            border: none;
        }

        .order-header {
            background-color: var(--secondary-color);
            color: var(--light-text);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid var(--accent-color);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .status-processing {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }

        .status-shipped {
            background-color: rgba(0, 123, 255, 0.2);
            color: #007bff;
        }

        .status-delivered {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .account-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .account-inactive {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .star-rating {
            color: #ffc107;
            font-size: 1.2rem;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--light-text);
        }

        .info-label {
            color: var(--muted-text);
            font-weight: 500;
        }

        .info-value {
            font-weight: 500;
        }

        .table {
            color: var(--light-text);
        }

        .table thead th {
            background-color: rgba(80, 80, 142, 0.5);
            border-bottom: none;
        }

        .table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .pagination .page-link {
            background-color: var(--secondary-color);
            color: var(--light-text);
            border: none;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent-color);
        }

          /* Mobile menu toggle button */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--secondary-color);
            border: none;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-280px);
                position: fixed;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .profile-img {
                width: 100px;
                height: 100px;
            }
            
            .section-title {
                font-size: 20px;
            }
            
            .btn {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
        /* Add to your existing media queries */
          @media (max-width: 992px) {
            .sidebar {
              z-index: 1000; /* Ensure it appears above content */
              transition: transform 0.3s ease; /* Smooth transition */
            }
            
            .main-content {
              transition: margin-left 0.3s ease; /* Match sidebar animation */
            }
          }

        @media (max-width: 768px) {
            .profile-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .profile-card-header > div {
                width: 100%;
            }
            
            .profile-card-header form {
                width: 100%;
            }
            
            .profile-card-header button {
                width: 100%;
            }
            
            .card-statistics .col-md-4 {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 15px;
            }
            
            .profile-img {
                width: 80px;
                height: 80px;
            }
            
            .info-label, .info-value {
                font-size: 0.9rem;
            }
            
            .btn-action {
                margin-bottom: 5px;
                width: 100%;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 5px !important;
            }
        }
        /* Add to your media queries */
@media (max-width: 768px) {
  .stat-card .stat-value {
    font-size: 28px;
  }
  
  .stat-card .stat-label {
    font-size: 14px;
  }
}

@media (max-width: 576px) {
  .stat-card {
    padding: 15px;
  }
  
  .stat-card .stat-value {
    font-size: 24px;
  }
}
    </style>
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
                  <a class="nav-link active" href="/customerManagement">
                      <i class="fas fa-users"></i> Customer Management
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="/sellerManagement">
                      <i class="fas fa-store"></i> Seller Management
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="/productManagement">
                      <i class="fas fa-boxes me-2"></i> Product Management
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="/reviewManagement">
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
      <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="section-title">Customer Details</h2>
          <a href="/customerManagement" class="btn btn-outline-primary">
              <i class="fas fa-arrow-left me-2"></i> Back to Customers
          </a>
      </div>

      <!-- Profile Card -->
      <div class="profile-card">
          <div class="profile-card-header">
              <i class="fas fa-user me-2"></i> Customer Profile
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-md-3 text-center">
                      @if($customer->customer_profile_images)
                          <img src="{{ asset($customer->customer_profile_images) }}" class="profile-img mb-3">
                      @else
                          <div class="profile-img mb-3 bg-secondary d-flex align-items-center justify-content-center">
                              <i class="fas fa-user text-muted"></i>
                          </div>
                      @endif
                  </div>
                  <div class="col-md-9">
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <p class="info-label">Full Name</p>
                              <p class="info-value">{{ $customer->full_name }}</p>
                          </div>
                          <div class="col-md-6 mb-3">
                              <p class="info-label">Email</p>
                              <p class="info-value">{{ $customer->customers_email }}</p>
                          </div>
                          <div class="col-md-6 mb-3">
                              <p class="info-label">Phone Number</p>
                              <p class="info-value">{{ $customer->phone_number }}</p>
                          </div>
                          <div class="col-md-6 mb-3">
                              <p class="info-label">Account Status</p>
                              <span class="badge rounded-pill {{ $customer->account_status == 'Activate' ? 'account-active' : 'account-inactive' }}">
                                  {{ $customer->account_status }}
                              </span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Order History -->
      <div class="profile-card">
          <div class="profile-card-header">
              <i class="fas fa-shopping-bag me-2"></i> Order History
          </div>
          <div class="card-body">
              @if($orders->count() > 0)
                  @foreach($orders as $order)
                  <div class="order-card mb-4">
                      <div class="order-header d-flex justify-content-between align-items-center">
                          <div>
                              <i class="fas fa-receipt me-2"></i>
                              Order #{{ $order->order_id }}
                          </div>
                          <div>
                              <i class="far fa-clock me-1"></i>
                              {{ $order->created_at}}
                          </div>
                      </div>
                      
                      <div class="card-body">
                          <div class="table-responsive">
                              <table class="table">
                                  <thead>
                                      <tr>
                                          <th>Product</th>
                                          <th>Image</th>
                                          <th>Seller</th>
                                          <th>Qty</th>
                                          <th>Unit Price</th>
                                          <th>Status</th>
                                          <th>Total</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @foreach($orderDetails[$order->order_id] ?? [] as $item)
                                      <tr>
                                          <td>{{ $item->product_name }}</td>
                                          <td>
                                              @if($item->product_image)
                                              <img src="{{ asset($item->product_image) }}" class="product-img">
                                              @else
                                              <div class="product-img bg-secondary d-flex align-items-center justify-content-center">
                                                  <i class="fas fa-image"></i>
                                              </div>
                                              @endif
                                          </td>
                                          <td>{{ $item->seller_name }}</td>
                                          <td>{{ $item->quantity }}</td>
                                          <td>${{ number_format($item->price_at_purchase, 2) }}</td>
                                          <td>
                                              @php
                                                  $statusClass = [
                                                      'pending' => 'status-pending',
                                                      'processing' => 'status-processing',
                                                      'shipped' => 'status-shipped',
                                                      'delivered' => 'status-delivered',
                                                      'cancelled' => 'status-cancelled'
                                                  ][$item->item_status] ?? 'bg-secondary text-white';
                                              @endphp
                                              <span class="badge rounded-pill {{ $statusClass }} status-badge">
                                                  {{ ucfirst($item->item_status) }}
                                              </span>
                                          </td>
                                          <td>${{ number_format($item->price_at_purchase * $item->quantity, 2) }}</td>
                                      </tr>
                                      @endforeach
                                      
                                      <tr class="table-active">
                                          <td colspan="6" class="text-end"><strong>Order Total:</strong></td>
                                          <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  @endforeach
                  
                  <div class="d-flex justify-content-center mt-4">
                      {{ $orders->links() }}
                  </div>
              @else
                  <div class="alert alert-info">
                      This customer hasn't placed any orders yet.
                  </div>
              @endif
          </div>
      </div>

      <!-- Product Reviews -->
      <div class="profile-card">
          <div class="profile-card-header">
              <i class="fas fa-star me-2"></i> Product Reviews
          </div>
          <div class="card-body">
              @if($reviews->count() > 0)
                  <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Order #</th>
                                  <th>Product</th>
                                  <th>Image</th>
                                  <th>Seller</th>
                                  <th>Rating</th>
                                  <th>Review</th>
                                  <th>Date</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($reviews as $review)
                              <tr>
                                  <td>{{ $review->order_id }}</td>
                                  <td>{{ $review->product_name }}</td>
                                  <td>
                                      @if($review->product_image)
                                      <img src="{{ asset($review->product_image) }}" class="product-img">
                                      @else
                                      <div class="product-img bg-secondary d-flex align-items-center justify-content-center">
                                          <i class="fas fa-image"></i>
                                      </div>
                                      @endif
                                  </td>
                                  <td>{{ $review->seller_name }}</td>
                                  <td>
                                      <div class="star-rating">
                                          @for($i = 1; $i <= 5; $i++)
                                              @if($i <= $review->rating)
                                                  ★
                                              @else
                                                  ☆
                                              @endif
                                          @endfor
                                      </div>
                                  </td>
                                  <td>{{ $review->review_message }}</td>
                                  <td>{{ $review->review_date }}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
              @else
                  <div class="alert alert-info">
                      This customer hasn't left any reviews yet.
                  </div>
              @endif
          </div>
      </div>
    </main>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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