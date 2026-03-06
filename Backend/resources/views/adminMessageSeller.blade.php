<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Message seller</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
      background-color:var(--dark-bg);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    
    /* Sidebar Styles */
    .sidebar {
      background-color: var(--primary-color);
      min-height: 100vh;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      position: fixed;
      width: 280px;
      z-index: 1000;
      transition: transform 0.3s ease;
    }
    
    .main-content {
      margin-left: 280px;
      padding: 20px;
      transition: margin-left 0.3s ease;
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
    
    /* Chat Styles */
    .chat-container {
      background-color:var(--dark-bg);
      padding: 20px;
      border-radius: 0 0 8px 8px;
    }
    
    .message-bubble {
      max-width: 70%;
      padding: 12px 16px;
      border-radius: 18px;
      position: relative;
      word-wrap: break-word;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .message-bubble.bg-primary {
      border-bottom-right-radius: 4px;
    }
    
    .message-bubble.bg-white {
      border-bottom-left-radius: 4px;
    }
    
    .time-text {
      font-size: 0.75rem;
      opacity: 0.8;
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
      padding: 8px 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    /* Backdrop for mobile sidebar */
    .sidebar-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-280px);
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .sidebar.active + .sidebar-backdrop {
        display: block;
      }
      
      .main-content {
        margin-left: 0;
        width: 100%;
      }
      
      .menu-toggle {
        display: block;
      }
    }
    
    @media (max-width: 768px) {
      .message-bubble {
        max-width: 85%;
      }
      
      .chat-container {
        padding: 15px;
        height: 400px !important;
      }
      
      .card-header h5 {
        font-size: 1.1rem;
      }
    }
    
    @media (max-width: 576px) {
      .message-bubble {
        max-width: 90%;
        padding: 10px 12px;
      }
      
      .chat-container {
        padding: 10px;
        height: 350px !important;
      }
      
      .card-header {
        padding: 12px 15px;
      }
      
      .card-header img {
        width: 32px;
        height: 32px;
      }
      
      .card-header h5 {
        font-size: 1rem;
      }
      
      .form-control {
        padding: 8px 12px;
      }
      
      .btn {
        padding: 8px 12px;
      }
    }
    
    /* Animation for new messages */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .message-bubble {
      animation: fadeIn 0.3s ease-out;
    }
    
    /* Custom scrollbar for chat */
    .chat-container::-webkit-scrollbar {
      width: 8px;
    }
    
    .chat-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .chat-container::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 10px;
    }
    
    .chat-container::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
    
    /* Auto-scroll to bottom */
    .chat-container {
      scroll-behavior: smooth;
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
            <a class="nav-link active" href="/allAdminMessages">
              <i class="fa-solid fa-envelope"></i> Message Seller
            </a>
          </li>
        </ul>
        
        <!-- Logout Button -->
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
    
    <!-- Sidebar Backdrop (mobile only) -->
    <div class="sidebar-backdrop"></div>

    <main class="col-md-9 col-lg-10 main-content">
      <div class="container py-4">
        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center text-white" style="background-color:var(--primary-color);">
            <a href="{{ route('allAdminMessages') }}" class="btn btn-light btn-sm me-2">
              <i class="fas fa-arrow-left"></i>
            </a>
            <img src="{{ asset($seller->seller_profile_img ?? 'placeholder.jpg') }}" 
                 class="rounded-circle me-3" width="40" height="40" onerror="this.src='placeholder.jpg'">
            <h5 class="mb-0">Conversation with {{ $seller->full_name }}</h5>
          </div>

          <div class="card-body chat-container" style="height: 500px; overflow-y: auto;" id="chatContainer">
            @foreach($messages as $message)
              <div class="mb-3 d-flex {{ $message->sender_type === 'admin' ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="message-bubble {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-white border' }}">
                  <p class="mb-1">{{ $message->messages }}</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="time-text {{ $message->sender_type === 'admin' ? 'text-white-50' : 'text-muted' }}">
                      {{ \Carbon\Carbon::parse($message->created_at)->format('h:i A | M d') }}
                    </small>
                    @if($message->sender_type === 'admin')
                      <span class="ms-2">
                        @if($message->is_read)
                          <i class="fas fa-check-double text-info"></i>
                        @else
                          <i class="fas fa-check"></i>
                        @endif
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="card-footer">
            <form action="{{ route('processAdminMessageToSeller', $seller->seller_id) }}" method="POST">
              @csrf
              <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-paper-plane"></i> Send
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    
    // Toggle sidebar
    menuToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      sidebar.classList.toggle('active');
      backdrop.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
    });
    
    // Close sidebar when clicking backdrop
    backdrop.addEventListener('click', function() {
      sidebar.classList.remove('active');
      backdrop.style.display = 'none';
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth < 992 && 
          !sidebar.contains(e.target) && 
          e.target !== menuToggle && 
          !menuToggle.contains(e.target)) {
        sidebar.classList.remove('active');
        backdrop.style.display = 'none';
      }
    });
    
    // Auto-scroll to bottom of chat
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    // Handle window resize
    function handleResize() {
      if (window.innerWidth >= 992) {
        sidebar.classList.remove('active');
        backdrop.style.display = 'none';
      }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize(); // Initialize
  });
</script>
</body>
</html>