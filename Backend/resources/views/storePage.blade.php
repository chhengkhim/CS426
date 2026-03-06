<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Details - Handcraft Admin</title>
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
            overflow-x: hidden;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            position: fixed;
            width: 280px;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s;
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

        .account-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .account-inactive {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
                            <a class="nav-link" href="/customerManagement">
                                <i class="fas fa-users"></i> Customer Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/sellerManagement">
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
                    
                    <!-- Logout Button at Bottom -->
                    <div class="mt-4 mb-3" style="position: absolute; bottom: 20px; width: calc(100% - 24px);">
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
                    <h2 class="section-title">Seller Details</h2>
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
                    <a href="/sellerManagement" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Sellers
                    </a>
                </div>

                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-card-header d-flex justify-content-between align-items-center">
                      
                        <div>
                            <i class="fas fa-store me-2"></i> Seller Profile
                        </div>
                        
                        <div>
                          

                            @if($seller->account_status === 'Activate')
                      <form method="POST" action="{{ url('deactivateSellerStorePage/'. $seller->seller_id) }}">
                          @csrf
                          <button type="submit" class="btn btn-danger"
                                  onclick="return confirm('Deactivate this seller?\n\nThis seller will no longer be able to login.')">
                                  <i class="fas fa-ban"></i>
                              Deactivate seller
                          </button>
                      </form>
                  @else
                      <form method="POST" action="{{ url('activateSellerStorePage/'. $seller->seller_id) }}">
                          @csrf
                          <button type="submit" class="btn btn-success"
                                  onclick="return confirm('Activate this seller?\n\nThis seller will now be able to login.')">
                                  <i class="fas fa-check"></i> 
                              Activate seller
                          </button>
                      </form>
                  @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                @if($seller->seller_profile_img)
                                    <img src="{{ asset($seller->seller_profile_img) }}" class="profile-img mb-3">
                                @else
                                    <div class="profile-img mb-3 bg-secondary d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user-tie text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Shop Name</p>
                                        <p class="info-value">{{ $seller->store_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Seller Name</p>
                                        <p class="info-value">{{ $seller->full_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Email</p>
                                        <p class="info-value">{{ $seller->seller_email }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Phone Number</p>
                                        <p class="info-value">{{ $seller->phone_number }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Account Status</p>
                                        <span class="badge rounded-pill {{ $seller->account_status == 'Activate' ? 'account-active' : 'account-inactive' }}">
                                            {{ $seller->account_status }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="info-label">Registration Date</p>
                                        <p class="info-value">{{ $seller->created_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-boxes me-2"></i> Products
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Product Photo</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product as $p)
                                        <tr>
                                            <td>
                                                @php
                                                    $image = collect($images)->firstWhere('product_id', $p->product_id);
                                                @endphp
                                                @if ($image)
                                                    <img src="{{ asset($image->img_url) }}" alt="Product Image" class="product-img">
                                                @else
                                                    <div class="product-img bg-secondary d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $p->product_name }}</td>
                                            <td>
                                                @php
                                                    $cat = collect($category)->firstWhere('category_id', $p->category_id);
                                                @endphp
                                                {{ $cat ? $cat->category_name : 'Unknown' }}
                                            </td>
                                            <td>${{ number_format($p->product_price, 2) }}</td>
                                            <td>
                                                <span class="badge rounded-pill {{ $p->product_status == 'available' ? 'account-active' : 'account-inactive' }}">
                                                    {{ ucfirst($p->product_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ url('/viewProductDetail/' . $p->product_id) }}" method="get">
                                                        @csrf
                                                        <button type="submit" class="btn btn-info btn-sm btn-action">
                                                            <i class="fas fa-eye"> view product</i>
                                                        </button>
                                                    </form>
                                                    @if($p->product_status === 'Activate')
                                                    <form action="{{ url('/deactivateProduct/' . $p->product_id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-{{ $p->product_status == 'Activate' ? 'warning' : 'success' }} btn-sm btn-action">
                                                            <i class="fas fa-{{ $p->product_status == 'Activate' ? 'ban' : 'check' }}"></i> Deactivate
                                                        </button>
                                                    </form>
                                                    @else
                                                    <form action="{{ url('/activateProduct/' . $p->product_id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-{{ $p->product_status == 'Activate' ? 'warning' : 'success' }} btn-sm btn-action">
                                                            <i class="fas fa-{{ $p->product_status == 'Activate' ? 'ban' : 'check' }}"></i> Activate
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Seller Statistics -->
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-chart-line me-2"></i> Seller Statistics
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-primary bg-opacity-10 mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Products</h5>
                                        <p class="display-6">{{ count($product) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success bg-opacity-10 mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Active Products</h5>
                                        <p class="display-6">{{ $product->where('product_status', 'Activate')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info bg-opacity-10 mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Sales</h5>
                                        <p class="display-6">${{ number_format($totalEarnings, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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