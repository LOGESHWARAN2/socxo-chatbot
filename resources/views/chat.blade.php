<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Socxo Chatbot - Chat</title>
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #f8f8f8;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            z-index: 100;
            flex-shrink: 0;
        }

        .sidebar.closed {
            width: 70px;
        }

        .sidebar-header {
            padding: 20px;
            height: 70px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
        }

        .sidebar.closed .sidebar-header {
            /* justify-content: flex-end; Removed to allow smooth transition */
            padding: 20px 10px;
            /* Adjusted padding */
        }

        .logo {
            font-size: 24px;
            font-weight: 900;
            color: #1a3c5e;
            letter-spacing: -1px;
            transition: all 0.3s ease;
            width: auto;
            opacity: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar.closed .logo {
            width: 0;
            opacity: 0;
            margin: 0;
            padding: 0;
        }

        .logo span {
            color: #E85D24;
        }

        .sidebar-toggle {
            background: none;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .sidebar-toggle i {
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .sidebar.closed .sidebar-toggle {
            border: none;
            padding: 5px;
        }

        .sidebar.closed .sidebar-toggle i {
            transform: rotate(180deg);
        }

        .new-chat-btn {
            margin: 15px;
            padding: 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.3s;
        }

        .sidebar.closed .new-chat-btn {
            display: none;
        }

        .new-chat-btn:hover {
            background: #f0f0f0;
        }

        .search-box {
            margin: 0 15px 15px;
            position: relative;
            transition: all 0.3s;
        }

        .sidebar.closed .search-box {
            display: none;
        }

        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .sidebar.closed .search-box input {
            display: none;
        }

        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .sidebar.closed .search-box i {
            position: static;
            transform: none;
            font-size: 18px;
            padding: 10px;
            cursor: pointer;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
            padding: 0 15px;
            overflow-x: hidden;
        }

        .sidebar.closed .chat-list {
            display: none;
        }

        .chat-list-section {
            margin-bottom: 20px;
        }

        .chat-list-title {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .chat-item {
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 5px;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item:hover {
            background: #e8e8e8;
        }

        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
            overflow: hidden;
            margin-top: auto;
        }

        .sidebar.closed .sidebar-footer {
            justify-content: center;
            padding: 15px 0;
            flex-direction: column;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar.closed .user-info span {
            display: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #E85D24;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .sidebar-footer-icons {
            display: flex;
            gap: 10px;
        }

        .sidebar.closed .sidebar-footer-icons {
            display: none;
        }

        .sidebar-footer-icons i {
            cursor: pointer;
            color: #666;
        }

        /* Main Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 80%;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #E85D24;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .message.bot .message-avatar {
            background: #f0f0f0;
            color: #333;
        }

        .message-content {
            flex: 1;
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .message-sender {
            font-weight: 600;
            font-size: 14px;
        }

        .message-time {
            font-size: 12px;
            color: #999;
        }

        .message-text {
            background: #f5f5f5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.user .message-text {
            background: #fff5f0;
        }

        .message-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .message-action-btn {
            background: none;
            border: none;
            color: #aaa;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .message-action-btn:hover {
            color: #E85D24;
            background-color: rgba(232, 93, 36, 0.1);
            transform: scale(1.1);
        }

        .message-action-btn.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
            transform: none;
        }

        /* Input Area */
        .chat-input-container {
            padding: 20px 30px;
            border-top: 1px solid #e0e0e0;
        }

        .chat-input-wrapper {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }

        #chat-form {
            position: relative;
            width: 100%;
            display: flex;
        }

        .chat-input {
            width: 100%;
            padding: 15px 60px 15px 15px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 14px;
            resize: none;
            min-height: 52px;
            max-height: 150px;
            line-height: 1.5;
        }

        .chat-input:focus {
            outline: none;
            border-color: #E85D24;
        }

        .send-btn {
            position: absolute;
            right: 12px;
            bottom: 8px;
            background: #E85D24;
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .send-btn:hover {
            background: #d14d19;
        }

        .send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .chat-footer {
            text-align: center;
            padding: 10px;
            font-size: 11px;
            color: #999;
        }

        .char-count {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }

        /* Typing Animation */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 10px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
        }

        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        /* Markdown Styles */
        .message-text p {
            margin-bottom: 8px;
        }

        .message-text p:last-child {
            margin-bottom: 0;
        }

        .message-text ul,
        .message-text ol {
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .message-text li {
            margin-bottom: 4px;
        }

        .message-text h1,
        .message-text h2,
        .message-text h3,
        .message-text h4 {
            font-size: 1.1em;
            font-weight: 700;
            margin-top: 12px;
            margin-bottom: 8px;
            color: #333;
        }

        .message-text strong {
            font-weight: 700;
        }

        .message-text code {
            background: rgba(0, 0, 0, 0.08);
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9em;
        }

        .message-text pre {
            background: #2d2d2d;
            color: #ccc;
            padding: 12px;
            border-radius: 8px;
            overflow-x: auto;
            margin-bottom: 10px;
        }

        .message-text pre code {
            background: none;
            padding: 0;
            color: inherit;
        }

        .message.user .message-text code {
            background: rgba(255, 255, 255, 0.2);
        }

        .message.user .message-text pre {
            background: rgba(0, 0, 0, 0.2);
            color: inherit;
        }

        .logo-small {
            width: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }

        .sidebar.closed .logo-small {
            width: 40px;
            opacity: 1;
        }

        .empty-chat-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            color: #666;
        }

        .empty-chat-content {
            text-align: center;
        }

        .empty-chat-content h3 {
            font-weight: 600;
            color: #333;
        }

        /* Mobile Responsive Styles */
        .mobile-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
            flex-shrink: 0;
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #333;
            cursor: pointer;
            padding: 0;
        }

        .mobile-close-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                width: 280px;
                transform: translateX(-100%);
                z-index: 1000;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
                width: 280px;
                /* Ensure width is reset if it was collapsed */
            }

            .sidebar.closed {
                width: 280px;
                /* Disable collapsed state on mobile */
            }

            /* Hide desktop toggle on mobile */
            .sidebar-toggle {
                display: none;
            }

            .mobile-close-btn {
                display: block;
            }

            .chat-main {
                width: 100%;
                margin-left: 0;
            }

            .mobile-header {
                display: flex;
            }

            /* Adjust chat padding for mobile */
            .chat-messages {
                padding: 15px;
            }

            .message {
                max-width: 90%;
            }

            .chat-input-container {
                padding: 15px;
            }

            .chat-input {
                padding: 12px 50px 12px 12px;
            }
        }
    </style>
    <!-- Marked.js for Markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    </style>
</head>

<body>
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('img/socxo.png') }}" alt="Socxo" style="height: 40px;">
                </div>
                <div class="logo-small">
                    <img src="{{ asset('img/favicon.png') }}" alt="Socxo" style="height: 32px;">
                </div>
                <button class="sidebar-toggle">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="mobile-close-btn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <a href="{{ route('chat.index') }}" class="new-chat-btn text-decoration-none text-dark">
                <i class="bi bi-plus-lg"></i>
                <span>New Chat</span>
            </a>

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search Chats" id="search-chats">
            </div>

            <div class="chat-list">
                <div class="chat-list-section">
                    {{-- <div class="chat-list-title">Chats</div> --}}
                    @if ($conversations->count() > 0)
                        <div class="chat-list-title" style="margin-top: 15px;">Chats Recent</div>
                        @foreach ($conversations as $conversation)
                            <a href="{{ route('chat.load', $conversation->uuid) }}"
                                class="text-decoration-none text-dark">
                                <div
                                    class="chat-item {{ isset($currentConversation) && $currentConversation->id == $conversation->id ? 'bg-light fw-bold' : '' }}">
                                    {{ Str::limit($conversation->title ?? 'New Chat', 30) }}
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-muted small p-2">No chats yet</div>
                    @endif
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <span style="font-size: 14px;">{{ Auth::user()->name }}</span>
                </div>
                <div class="sidebar-footer-icons">
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-decoration-none text-dark" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay"></div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <button class="mobile-menu-btn">
                    <i class="bi bi-list"></i>
                </button>
                <div class="logo" style="font-size: 20px;">
                    <img src="{{ asset('img/socxo.png') }}" alt="Socxo" style="height: 30px;">
                </div>
                <div style="width: 24px;"></div> <!-- Spacer for centering -->
            </div>

            <div class="chat-messages" id="chat-box">
                @if ($messages->isEmpty())
                    <div class="empty-chat-state">
                        <div class="empty-chat-content">
                            <img src="{{ asset('img/socxo.png') }}" alt="Socxo"
                                style="height: 60px; margin-bottom: 20px; opacity: 0.5;">
                            <h3>What can I help with?</h3>
                        </div>
                    </div>
                @else
                    @foreach ($messages as $message)
                        <div class="message {{ $message->sender }}">
                            <div class="message-avatar">
                                {{ $message->sender == 'user' ? substr(Auth::user()->name, 0, 1) : 'AI' }}
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span
                                        class="message-sender">{{ $message->sender == 'user' ? Auth::user()->name : 'Socxo Chatboot' }}</span>
                                    <span
                                        class="message-time">{{ $message->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="message-text">{{ $message->content }}</div>
                                @if ($message->sender == 'bot')
                                    <div class="message-actions">
                                        @php
                                            $reaction = $message->reaction;
                                            $likeClass = $reaction === 'like' ? 'text-primary' : '';
                                            $dislikeClass = $reaction === 'dislike' ? 'text-danger' : '';
                                            $likeHidden = $reaction === 'dislike' ? 'd-none' : '';
                                            $dislikeHidden = $reaction === 'like' ? 'd-none' : '';
                                        @endphp
                                        <button class="message-action-btn copy-btn" title="Copy"><i
                                                class="bi bi-clipboard"></i></button>
                                        <button class="message-action-btn like-btn {{ $likeHidden }}"
                                            title="Good response"><i
                                                class="bi bi-hand-thumbs-up {{ $likeClass }}"></i></button>
                                        <button class="message-action-btn dislike-btn {{ $dislikeHidden }}"
                                            title="Bad response"><i
                                                class="bi bi-hand-thumbs-down {{ $dislikeClass }}"></i></button>
                                        <button class="message-action-btn" title="Regenerate"><i
                                                class="bi bi-arrow-90deg-left"></i></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <form id="chat-form">
                        <textarea class="chat-input" id="message-input" placeholder="Type your message..." rows="1" required></textarea>
                        <button type="submit" class="send-btn">
                            <i class="bi bi-arrow-up"></i>
                        </button>
                    </form>
                    <div class="char-count">
                        <span id="char-count">0</span>/4000 characters
                    </div>
                </div>
                <div class="chat-footer">
                    Disclaimer: This app uses artificial intelligence, which may make mistakes. | Socxo Confidential
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        $(document).ready(function() {
            const chatBox = $('#chat-box');
            const messageInput = $('#message-input');
            const charCount = $('#char-count');
            let currentConversationId = '{{ $currentConversation->id ?? '' }}';
            const apiToken = '{{ $apiToken ?? '' }}';

            // Global AJAX Setup for Authorization
            if (apiToken) {
                $.ajaxSetup({
                    headers: {
                        'Authorization': 'Bearer ' + apiToken,
                        'Accept': 'application/json'
                    }
                });
            } else {
                console.warn('API Token not found');
            }

            // Configure marked
            marked.setOptions({
                breaks: true, // Enable line breaks
                gfm: true // Enable GitHub Flavored Markdown
            });

            // Parse existing bot messages
            $('.message.bot .message-text').each(function() {
                // Skip if it's the typing indicator
                if ($(this).find('.typing-indicator').length > 0) return;

                const rawContent = $(this).text();
                const htmlContent = marked.parse(rawContent);
                $(this).html(htmlContent);
            });

            // Auto-scroll to bottom
            chatBox.scrollTop(chatBox[0].scrollHeight);

            // Character counter
            messageInput.on('input', function() {
                const length = $(this).val().length;
                charCount.text(length);

                // Auto-resize textarea
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Handle Enter key to send
            messageInput.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $('#chat-form').submit();
                }
            });

            // Function to render messages
            function renderMessage(msg) {
                const isUser = msg.sender === 'user';
                const avatar = isUser ? '{{ substr(Auth::user()->name, 0, 1) }}' : 'AI';
                const name = isUser ? '{{ Auth::user()->name }}' : 'Socxo Chatbot';
                const time = new Date(msg.created_at || new Date()).toLocaleString('en-US', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                let actionsHtml = '';
                if (!isUser) {
                    const likeClass = msg.reaction === 'like' ? 'text-primary' : '';
                    const dislikeClass = msg.reaction === 'dislike' ? 'text-danger' : '';

                    // Logic to HIDE the opposite button if one is selected
                    // Using d-none for visibility control
                    const likeHidden = msg.reaction === 'dislike' ? 'd-none' : '';
                    const dislikeHidden = msg.reaction === 'like' ? 'd-none' : '';

                    actionsHtml = `
                        <div class="message-actions">
                            <button class="message-action-btn copy-btn" title="Copy"><i class="bi bi-clipboard"></i></button>
                            <button class="message-action-btn like-btn ${likeHidden}" title="Good response"><i class="bi bi-hand-thumbs-up ${likeClass}"></i></button>
                            <button class="message-action-btn dislike-btn ${dislikeHidden}" title="Bad response"><i class="bi bi-hand-thumbs-down ${dislikeClass}"></i></button>
                            <button class="message-action-btn" title="Regenerate"><i class="bi bi-arrow-90deg-left"></i></button>
                        </div>
                    `;
                }

                chatBox.append(`
                    <div class="message ${msg.sender}" data-id="${msg.id || ''}">
                        <div class="message-avatar" style="${!isUser ? 'background: #f0f0f0; color: #333;' : ''}">${avatar}</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">${name}</span>
                                <span class="message-time">${time}</span>
                            </div>
                            <div class="message-text">${msg.content}</div>
                            ${actionsHtml}
                        </div>
                    </div>
                `);
            }

            // ... (loadConversations function skipped) ...

            // Feedback Handler
            $(document).on('click', '.like-btn, .dislike-btn', function() {
                const btn = $(this);
                const icon = btn.find('i');
                const isLike = btn.hasClass('like-btn');

                // Check if ALREADY active - if so, DO NOTHING (Permanent choice)
                if (icon.hasClass('text-primary') || icon.hasClass('text-danger')) {
                    return;
                }

                const reaction = isLike ? 'like' : 'dislike';

                // DOM Elements
                const actionsContainer = btn.parent();
                const otherBtn = isLike ? actionsContainer.find('.dislike-btn') : actionsContainer.find(
                    '.like-btn');

                // Find message ID
                const messageDiv = btn.closest('.message');
                const messageId = messageDiv.data('id');

                if (!messageId) {
                    console.error("Message ID not found");
                    return;
                }

                // Apply new selection state
                // 1. Highlight this icon
                icon.addClass(isLike ? 'text-primary' : 'text-danger');

                // 2. Hide the OTHER button
                otherBtn.addClass('d-none');

                // 3. Send to API
                $.ajax({
                    url: `/api/chat/message/${messageId}/feedback`,
                    method: 'POST',
                    data: {
                        reaction: reaction
                    },
                    success: function(res) {
                        console.log('Feedback saved');
                    }
                });
            });

            // Function to load conversations (placeholder, assuming it fetches and updates the sidebar)
            function loadConversations() {
                // This function would typically make an AJAX call to fetch the latest conversations
                // and update the .chat-list-section. For now, we'll simulate a simple update.
                console.log('Loading conversations...');
                $.ajax({
                    url: '/api/chat/conversations', // Adjust this endpoint as needed
                    method: 'GET',
                    success: function(response) {
                        const chatListSection = $('.chat-list-section');
                        chatListSection.empty(); // Clear existing list

                        if (response.conversations && response.conversations.length > 0) {
                            chatListSection.append(
                                '<div class="chat-list-title" style="margin-top: 15px;">Chats Recent</div>'
                            );
                            response.conversations.forEach(conversation => {
                                const newChatHtml = `
                                    <a href="/chat/${conversation.uuid}" class="text-decoration-none text-dark">
                                        <div class="chat-item ${initialUuid === conversation.uuid ? 'bg-light fw-bold' : ''}">
                                            ${conversation.title.length > 30 ? conversation.title.substring(0, 30) + '...' : conversation.title}
                                        </div>
                                    </a>
                                `;
                                chatListSection.append(newChatHtml);
                            });
                        } else {
                            chatListSection.append(
                                '<div class="text-muted small p-2">No chats yet</div>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load conversations:', xhr);
                    }
                });
            }

            // Handle form submission
            $('#chat-form').on('submit', function(e) {
                e.preventDefault();
                let message = messageInput.val().trim();
                if (!message) return;

                // Render User Message Immediately
                renderMessage({
                    sender: 'user',
                    content: message,
                    created_at: new Date() // Optimistic timestamp
                });

                messageInput.val('');
                messageInput.css('height', 'auto');
                charCount.text('0');
                chatBox.scrollTop(chatBox[0].scrollHeight);

                // Show typing indicator
                const typingId = 'typing-' + Date.now();
                chatBox.append(`
                    <div class="message bot" id="${typingId}">
                        <div class="message-avatar">AI</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">Socxo Chatboot</span>
                                <span class="message-time">Typing...</span>
                            </div>
                            <div class="message-text">
                                <div class="typing-indicator">
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                chatBox.scrollTop(chatBox[0].scrollHeight);

                // Send to API
                $.ajax({
                    url: '/api/chat/send',
                    method: 'POST',
                    data: {
                        message: message,
                        conversation_id: currentConversationId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        // Remove typing indicator
                        $('#' + typingId).remove();

                        // Render Bot Message
                        renderMessage(response.bot_message);

                        // Parse markdown for the new message
                        const lastMsg = chatBox.find('.message.bot').last().find(
                            '.message-text');
                        lastMsg.html(marked.parse(lastMsg.text()));

                        chatBox.scrollTop(chatBox[0].scrollHeight);

                        // Update current conversation ID if it was a new chat
                        if (!currentConversationId && response.conversation_id) {
                            currentConversationId = response.conversation_id;
                            // Update URL without reload
                            if (response.conversation_uuid) {
                                const newUrl = '/chat/' + response.conversation_uuid;
                                window.history.pushState({
                                    path: newUrl
                                }, '', newUrl);

                                // Refresh sidebar to show new chat
                                loadConversations();
                                initialUuid = response.conversation_uuid;
                            }
                        }


                    },
                    error: function(xhr) {
                        // Remove typing indicator
                        $('#' + typingId).remove();

                        console.log(xhr);
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        } else {
                            errorMessage = xhr.statusText;
                        }
                        alert('Error: ' + errorMessage);
                    }
                });
            });



            // Search functionality
            $('#search-chats').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.chat-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Sidebar toggle functionality (Desktop)
            $('.sidebar-toggle').on('click', function() {
                $('.sidebar').toggleClass('closed');
            });

            // Mobile Menu Functionality
            const sidebar = $('.sidebar');
            const overlay = $('.sidebar-overlay');

            $('.mobile-menu-btn').on('click', function() {
                sidebar.addClass('mobile-open');
                overlay.fadeIn(200);
            });

            function closeMobileMenu() {
                sidebar.removeClass('mobile-open');
                overlay.fadeOut(200);
            }

            $('.mobile-close-btn').on('click', closeMobileMenu);
            overlay.on('click', closeMobileMenu);

            // Close sidebar when clicking a chat item on mobile
            $(document).on('click', '.chat-item', function() {
                if ($(window).width() <= 768) {
                    closeMobileMenu();
                }
            });

            // Message Actions
            $(document).on('click', '.bi-clipboard', function() {
                const text = $(this).closest('.message-content').find('.message-text').text();
                navigator.clipboard.writeText(text).then(() => {
                    const originalClass = $(this).attr('class');
                    $(this).attr('class', 'bi bi-check-lg');
                    setTimeout(() => {
                        $(this).attr('class', originalClass);
                    }, 2000);
                });
            });

            $(document).on('click', '.bi-hand-thumbs-up', function() {
                $(this).toggleClass('bi-hand-thumbs-up bi-hand-thumbs-up-fill');
                $(this).closest('.message-actions').find('.bi-hand-thumbs-down-fill').toggleClass(
                    'bi-hand-thumbs-down-fill bi-hand-thumbs-down');
            });

            $(document).on('click', '.bi-hand-thumbs-up-fill', function() {
                $(this).toggleClass('bi-hand-thumbs-up-fill bi-hand-thumbs-up');
            });

            $(document).on('click', '.bi-hand-thumbs-down', function() {
                $(this).toggleClass('bi-hand-thumbs-down bi-hand-thumbs-down-fill');
                $(this).closest('.message-actions').find('.bi-hand-thumbs-up-fill').toggleClass(
                    'bi-hand-thumbs-up-fill bi-hand-thumbs-up');
            });

            $(document).on('click', '.bi-hand-thumbs-down-fill', function() {
                $(this).toggleClass('bi-hand-thumbs-down-fill bi-hand-thumbs-down');
            });

            $(document).on('click', '.bi-arrow-90deg-left', function() {
                // Find the last user message
                const lastUserMessage = $(this).closest('.message.bot').prevAll('.message.user').first()
                    .find('.message-text').text();
                if (lastUserMessage) {
                    $('#message-input').val(lastUserMessage);
                    $('#chat-form').submit();
                }
            });
        });
    </script>
</body>

</html>
