<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Management</title>
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
        <h2 class="section-title">Customer Management</h2>
        
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

        <div class="customer-table-container">
          <div class="table-responsive">
            <div class="table-wrapper">
            <table class="customer-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Profile</th>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Gender</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Actions</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($customer as $customer)
                  <tr>
                    <td>{{$customer->customer_id}}</td>
                    <td>
                      @if ($customer->customer_profile_images)
                          <img src="{{ asset($customer->customer_profile_images) }}" alt="customer profile" class="customer-avatar">
                      @else
                          <div class="customer-avatar bg-secondary d-flex align-items-center justify-content-center">
                            <i class="fas fa-user"></i>
                          </div>
                      @endif
                    </td>
                    <td>{{ $customer->full_name }}</td>
                    <td>{{$customer->age}}</td>
                    <td>{{ $customer->gender }}</td>
                    <td>{{ $customer->customers_email }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>
                      <span class="status-badge {{ $customer->account_status === 'Activate' ? 'status-active' : 'status-inactive' }}">
                        {{ $customer->account_status }}
                      </span>
                    </td>

                    <td>
                      @if($customer->account_status === 'Activate')
                        <form method="POST" action="{{ url('deactivateCustomer/'. $customer->customer_id) }}" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-danger"
                                  onclick="return confirm('Deactivate this customer?\n\nThis customer will no longer be able to login.')">
                            <i class="fas fa-ban"></i> Deactivate
                          </button>
                        </form>
                      @else
                        <form method="POST" action="{{ url('activateCustomer/'. $customer->customer_id) }}" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-success"
                                  onclick="return confirm('Activate this customer?\n\nThis customer will now be able to login.')">
                            <i class="fas fa-check"></i> Activate
                          </button>
                        </form>
                      @endif
                    </td>

                    <td>
                      <form action="{{ url('viewCustomerDetail/' . $customer->customer_id)}}" method="GET" class="d-inline">
                        <button type="submit" class="btn btn-primary">
                          <i class="fas fa-eye"></i> View
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
        </div>
          </div>
      </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            
            if (window.innerWidth < 992 && 
                !sidebar.contains(event.target) && 
                event.target !== menuToggle && 
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>