<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Conversations</title>
  <link rel="stylesheet" href="/asset/styles.css"> <!-- Main site styles -->
  <link rel="stylesheet" href="/asset/seller_home.css"> <!-- Seller Home specific styles -->
  <style>
    .conversation-card {
      background: #fdfaf7;
      border: 1px solid #f0d6c2;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.04);
      padding: 18px 24px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      transition: box-shadow 0.15s, transform 0.15s;
      text-decoration: none;
      color: inherit;
    }
    .conversation-card:hover {
      box-shadow: 0 6px 20px rgba(184,92,56,0.10);
      transform: translateY(-3px) scale(1.01);
      background: #fff6ee;
    }
    .conversation-avatar {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #b85c38;
      margin-right: 18px;
      background: #f0e0d6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      color: #b85c38;
    }
    .conversation-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .conversation-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #b85c38;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .conversation-badge {
      background: #b85c38;
      color: #fff;
      font-size: 0.75rem;
      padding: 2px 8px;
      border-radius: 8px;
      margin-left: 6px;
      text-transform: capitalize;
    }
    .conversation-badge.customer {
      background: #0d6efd;
    }
    .conversation-badge.admin {
      background: #6c757d;
    }
    .conversation-last {
      color: #6d4c2b;
      font-size: 0.95rem;
    }
    .conversation-unread {
      background: #d9534f;
      color: #fff;
      font-size: 0.9rem;
      padding: 4px 10px;
      border-radius: 12px;
      font-weight: 600;
      margin-left: 16px;
    }
    @media (max-width: 600px) {
      .conversation-card { flex-direction: column; align-items: flex-start; padding: 14px; }
      .conversation-avatar { margin-bottom: 10px; margin-right: 0; }
    }
  </style>
</head>
<body>
  <div class="sh-main-layout" style="box-shadow:none;background:transparent;max-width:700px;">
    <section class="sh-content" style="width:100%;padding:32px 0 0 0;">
      <a href="/seller_Home" class="sh-btn" style="margin-bottom:24px;display:inline-block;">&larr; Back Home</a>
      <h2 class="sh-section-title" style="margin-bottom:24px;">Your Conversations</h2>
      @if(session('success'))
        <div class="sh-alert sh-alert-success">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="sh-alert sh-alert-danger">
          {{ session('error') }}
        </div>
      @endif
      @if($conversations->isEmpty())
        <div class="sh-alert" style="background:#f0f0f0;color:#b85c38;">You don't have any conversations yet.</div>
      @else
        <div>
          @foreach($conversations as $conversation)
            <a href="{{ $conversation->contact_type === 'admin' 
                  ? route('sellerMessageAdmin', $conversation->contact_id) 
                  : route('sellerMessageCustomer', $conversation->contact_id) }}" 
               class="conversation-card">
              @if($conversation->profile_img)
                <img src="{{ asset($conversation->profile_img ?? 'placeholder.jpg') }}" class="conversation-avatar" alt="Avatar">
              @else
                <div class="conversation-avatar">{{ substr($conversation->contact_name, 0, 1) }}</div>
              @endif
              <div class="conversation-info">
                <div class="conversation-title">
                  {{ $conversation->contact_name }}
                  <span class="conversation-badge {{ $conversation->contact_type }}">
                    {{ $conversation->contact_type }}
                  </span>
                  @if($conversation->unread_count > 0)
                    <span class="conversation-unread">{{ $conversation->unread_count }}</span>
                  @endif
                </div>
                <span class="conversation-last">
                  Last message: {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans() }}
                </span>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </section>
  </div>
</body>
</html>