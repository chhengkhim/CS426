<!DOCTYPE html>
<html lang="en">
    <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Conversations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
<body>
  <div class="container py-4">
    <div class="card">
        <div class="card-header d-flex align-items-center bg-primary text-white">
            <a href="{{ route('allSellerMessages') }}" class="btn btn-light btn-sm me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <img src="{{ asset($admin->profile_img ?? 'placeholder.jpg') }}" 
                 class="rounded-circle me-3" width="40" height="40" onerror="this.src='placeholder.jpg'">
            <h5 class="mb-0">Conversation with Admin {{ $admin->name }}</h5>
        </div>

        <div class="card-body chat-container" style="height: 500px; overflow-y: auto;" id="chatContainer">
            @foreach($messages as $message)
                <div class="mb-3 d-flex {{ $message->sender_type === 'seller' ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="message-bubble {{ $message->sender_type === 'seller' ? 'bg-primary text-white' : 'bg-white border' }}">
                        <p class="mb-1">{{ $message->messages }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="time-text {{ $message->sender_type === 'seller' ? 'text-white-50' : 'text-muted' }}">
                                {{ \Carbon\Carbon::parse($message->created_at)->format('h:i A | M d') }}
                            </small>
                            @if($message->sender_type === 'seller')
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
            <form action="{{ route('processSendMessageToAdmin', $admin->admin_id) }}" method="POST">
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
</body>
</html>