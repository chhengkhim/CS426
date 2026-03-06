<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conversation with {{ $customer->full_name }}</title>
  <link rel="stylesheet" href="/asset/styles.css"> <!-- Main site styles -->
  <link rel="stylesheet" href="/asset/seller_home.css"> <!-- Seller Home specific styles -->
  <style>
    .chat-main-card {
      max-width: 1100px;
      min-width: 700px;
      margin: 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .chat-header {
      display: flex;
      align-items: center;
      background: #0d6efd;
      color: #fff;
      padding: 24px 48px;
      border-bottom: 1px solid #e0e0e0;
    }
    .chat-header .back-btn {
      background: #fff;
      color: #0d6efd;
      border: none;
      border-radius: 8px;
      padding: 8px 20px;
      margin-right: 24px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.18s, color 0.18s;
      display: flex;
      align-items: center;
    }
    .chat-header .back-btn:hover {
      background: #e0e0e0;
      color: #0d6efd;
    }
    .chat-header .avatar {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 24px;
      border: 2px solid #fff;
      background: #f0e0d6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      color: #b85c38;
    }
    .chat-header .chat-title {
      font-size: 1.4rem;
      font-weight: 600;
      margin: 0;
    }
    .chat-container {
      background: #f7e7d7;
      padding: 40px 60px;
      height: 600px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    .message-row {
      display: flex;
      margin-bottom: 8px;
    }
    .message-row.seller {
      justify-content: flex-end;
    }
    .message-row.customer {
      justify-content: flex-start;
    }
    .message-bubble {
      max-width: 50%;
      padding: 18px 28px;
      border-radius: 18px;
      word-wrap: break-word;
      font-size: 1.1rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .message-bubble.seller {
      background: #b85c38;
      color: #fff;
      border-bottom-right-radius: 6px;
      align-items: flex-end;
    }
    .message-bubble.customer {
      background: #fff;
      color: #2d1c0b;
      border-bottom-left-radius: 6px;
      border: 1px solid #f0d6c2;
      align-items: flex-start;
    }
    .message-meta {
      font-size: 1rem;
      color: #6d4c2b;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .message-meta.seller { color: #ffe9d1; }
    .message-meta.customer { color: #b85c38; }
    .message-status {
      margin-left: 6px;
      font-size: 1rem;
    }
    .chat-footer {
      background: #fdfaf7;
      padding: 24px 48px;
      border-top: 1px solid #f0d6c2;
    }
    .chat-footer form {
      display: flex;
      gap: 10px;
    }
    .chat-footer input[type="text"] {
      flex: 1;
      border-radius: 8px;
      border: 1px solid #f0d6c2;
      padding: 14px 18px;
      font-size: 1.1rem;
      outline: none;
      background: #fff;
      color: #2d1c0b;
    }
    .chat-footer button {
      background: #b85c38;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 0 32px;
      font-size: 1.2rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.18s;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .chat-footer button:hover {
      background: #a14d2a;
    }
    @media (max-width: 1200px) {
      .chat-main-card { max-width: 100%; min-width: 0; }
      .chat-header, .chat-footer { padding: 18px; }
      .chat-container { padding: 18px; }
    }
    @media (max-width: 700px) {
      .chat-main-card { max-width: 100%; margin: 0; border-radius: 0; }
      .chat-header, .chat-footer { padding: 12px; }
      .chat-container { padding: 12px; }
    }
  </style>
</head>
<body style="background:#f7e7d7;">
  <div class="chat-main-card">
    <div class="chat-header">
      <a href="{{ url('/allSellerMessages') }}" class="back-btn">&larr;</a>
      @if($customer->customer_profile_images)
        <img src="{{ asset($customer->customer_profile_images) }}" class="avatar" alt="Avatar">
      @else
        <div class="avatar">{{ substr($customer->full_name, 0, 1) }}</div>
      @endif
      <span class="chat-title">Conversation with {{ $customer->full_name }}</span>
    </div>
    @if(session('error'))
      <div class="sh-alert sh-alert-danger" style="margin: 0 24px;">
        {{ session('error') }}
      </div>
    @endif
    <div class="chat-container" id="chatContainer">
      @foreach($messages as $message)
        <div class="message-row {{ $message->sender_type === 'seller' ? 'seller' : 'customer' }}">
          <div class="message-bubble {{ $message->sender_type === 'seller' ? 'seller' : 'customer' }}">
            <span>{{ $message->messages }}</span>
            <div class="message-meta {{ $message->sender_type }}">
              <span>{{ \Carbon\Carbon::parse($message->created_at)->format('h:i A | M d') }}</span>
              @if($message->sender_type === 'seller')
                <span class="message-status">
                  @if($message->is_read)
                    &#10003;&#10003;
                  @else
                    &#10003;
                  @endif
                </span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="chat-footer">
      <form action="{{ url('processSendMessageToCustomer', $customer->customer_id) }}" method="POST">
        @csrf
        <input type="text" name="message" placeholder="Type your message..." required>
        <button type="submit">
          &#10148; Send
        </button>
      </form>
    </div>
  </div>
  <script>
    // Auto-scroll to bottom of chat
    const chatContainer = document.getElementById('chatContainer');
    if(chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
  </script>
</body>
</html>