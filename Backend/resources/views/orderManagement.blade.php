<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a class="nav-link active" href="/viewAllOrders">
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
      <h2 class="section-title">Order Management</h2>
      
      @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          @foreach($errors->all() as $error)
              {{ $error }}
          @endforeach
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      @foreach($orders as $order)
      <div class="card order-card">
          <div class="card-header order-header d-flex justify-content-between align-items-center">
              <div>
                  <i class="fas fa-receipt me-2"></i>
                  Order #{{ $order->order_id }} - {{ $order->customer_name }}
              </div>
              <div>
                  <i class="far fa-clock me-1"></i>
                  {{ $order->created_at }}
              </div>
          </div>
          
          <div class="card-body">
              <div class="table-responsive">
                <div class="table-wrapper">
                  <table class="order-table">
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
                          <tr class="order-item">
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
                          
                          <tr class="order-total-row">
                              <td colspan="6" class="text-end"><strong>Order Total:</strong></td>
                              <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                          </tr>
                      </tbody>
                  </table>
              </div>
              </div>
          </div>
      </div>
      @endforeach
      
      <div class="d-flex justify-content-center mt-4">
          {{ $orders->links() }}
      </div>

      @if($orders->isEmpty())
        <div class="alert alert-info">
            No product orders available.
        </div>
        @endif
    </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  
  // Toggle sidebar with better mobile handling
  menuToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    const isOpening = !sidebar.classList.contains('active');
    sidebar.classList.toggle('active');
    document.body.classList.toggle('no-scroll', isOpening);
    
    // Force reflow to ensure z-index changes take effect
    void sidebar.offsetWidth;
  });
  
  // Close sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth < 992 && 
        sidebar.classList.contains('active') && 
        !sidebar.contains(e.target) && 
        e.target !== menuToggle) {
      sidebar.classList.remove('active');
      document.body.classList.remove('no-scroll');
    }
  });
  
  // Handle window resize
  function handleResize() {
    if (window.innerWidth >= 992) {
      sidebar.classList.remove('active');
      document.body.classList.remove('no-scroll');
    }
  }
  
  window.addEventListener('resize', handleResize);
  handleResize(); // Initialize
});
</script>
</body>
</html>