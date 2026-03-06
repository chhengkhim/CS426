<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Conversations</title>
  <link rel="stylesheet" href="/asset/styles.css"> <!-- Main site styles -->
  <link rel="stylesheet" href="/asset/seller_home.css"> <!-- For modern card/button styles -->
  <style>
    body {
      background: #f7e7d7;
    }
    .conv-main-card {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      padding: 36px 36px 28px 36px;
    }
    .conv-header {
      display: flex;
      align-items: center;
      margin-bottom: 32px;
    }
    .conv-header .back-btn {
      background: #b85c38;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 20px;
      margin-right: 18px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.18s, color 0.18s;
      display: flex;
      align-items: center;
    }
    .conv-header .back-btn:hover {
      background: #a14d2a;
      color: #fff;
    }
    .conv-header h2 {
      font-size: 2rem;
      font-weight: 700;
      color: #b85c38;
      margin: 0;
    }
    .conv-list {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    .conv-card {
      background: #fdfaf7;
      border: 1px solid #f0d6c2;
      border-radius: 14px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      padding: 18px 24px;
      display: flex;
      align-items: center;
      text-decoration: none;
      color: inherit;
      transition: box-shadow 0.15s, background 0.15s;
    }
    .conv-card:hover {
      box-shadow: 0 6px 20px rgba(184,92,56,0.10);
      background: #fff6ee;
    }
    .conv-avatar {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 18px;
      background: #f0e0d6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      color: #b85c38;
    }
    .conv-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .conv-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #b85c38;
      margin-bottom: 2px;
    }
    .conv-last {
      color: #6d4c2b;
      font-size: 0.98rem;
    }
    .conv-unread {
      background: #d9534f;
      color: #fff;
      font-size: 0.95rem;
      padding: 6px 14px;
      border-radius: 12px;
      font-weight: 600;
      margin-left: 18px;
    }
    .conv-empty {
      background: #f0f0f0;
      color: #b85c38;
      border-radius: 8px;
      padding: 1.2rem 1.5rem;
      margin-top: 1rem;
      font-size: 1.1rem;
      text-align: center;
    }
    @media (max-width: 900px) {
      .conv-main-card { max-width: 100%; padding: 18px 6vw; }
    }
    @media (max-width: 600px) {
      .conv-main-card { padding: 8px 2vw; }
      .conv-header h2 { font-size: 1.3rem; }
      .conv-card { flex-direction: column; align-items: flex-start; padding: 14px; }
      .conv-avatar { margin-bottom: 10px; margin-right: 0; }
    }
  </style>
</head>
<body>
  <div class="conv-main-card">
    <div class="conv-header">
    <a href="{{ url('/customer_Home') }}" class="back-btn">&larr; Back</a>
      <h2>Your Conversations</h2>
    </div>
    @if(session('success'))
      <div class="sh-alert sh-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="sh-alert sh-alert-danger">{{ session('error') }}</div>
    @endif
    @if($conversations->isEmpty())
      <div class="conv-empty">You don't have any conversations yet.</div>
    @else
      <div class="conv-list">
        @foreach($conversations as $conversation)
          <a href="{{ url('/customerMessageSeller', $conversation->seller_id) }}" class="conv-card">
            @if($conversation->seller_profile_img)
              <img src="{{ asset($conversation->seller_profile_img) }}" class="conv-avatar" alt="Avatar">
            @else
              <div class="conv-avatar">{{ substr($conversation->seller_name, 0, 1) }}</div>
            @endif
            <div class="conv-info">
              <div class="conv-title">{{ $conversation->seller_name }}</div>
              <span class="conv-last">Last message: {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans() }}</span>
            </div>
            @if($conversation->unread_count > 0)
              <span class="conv-unread">{{ $conversation->unread_count }}</span>
            @endif
          </a>
        @endforeach
      </div>
    @endif
  </div>
</body>
</html>