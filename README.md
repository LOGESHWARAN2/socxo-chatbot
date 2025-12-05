# Socxo Chatbot Web Application

A modern, AI-powered chatbot application built with Laravel 10+, MySQL, Bootstrap 5, and jQuery. This application features a custom-designed UI, Google Gemini integration, and a robust chat interface.

## Features

### Core Functionality

-   **Authentication**: Secure user registration, login, and password reset.
-   **AI Integration**: Powered by Google Gemini API (`gemini-2.5-flash`) for intelligent responses.
-   **Chat History**: Automatically saves and retrieves conversation threads.
-   **Token Limit**: Enforces a 20,000 token usage limit per user.

### User Interface & Experience

-   **Modern Design**: Custom "Socxo" branded UI with a clean, professional aesthetic.
-   **Collapsible Sidebar**: Responsive sidebar with smooth animation and a collapsed icon-only mode.
-   **Dynamic URLs**:
    -   New chats start at `/chat`.
    -   URL automatically updates to `/chat/{uuid}` after the first message without reloading.
    -   Uses UUIDs for secure and clean conversation links.
-   **Instant Updates**: New chat titles appear instantly in the sidebar upon creation.
-   **Markdown Support**: Bot responses are formatted with Markdown (bold, lists, code blocks, etc.).
-   **Typing Animation**: Visual feedback while the AI is generating a response.
-   **Message Actions**:
    -   **Copy**: Copy message text to clipboard.
    -   **Like/Dislike**: Rate AI responses.
    -   **Retry**: Regenerate the last response.
-   **Empty State**: Welcoming "What can I help with?" screen for new conversations.
-   **Smart Redirects**: Authenticated users are automatically redirected to the chat interface.

## Tech Stack

-   **Backend**: PHP 8.2+, Laravel 12.x
-   **Frontend**: HTML5, CSS3 (Custom + Bootstrap 5), jQuery, Vite
-   **Database**: MySQL
-   **AI Provider**: Google Gemini API
-   **Authentication**: Laravel Sanctum

## Setup Instructions

1.  **Clone the repository**:

    ```bash
    git clone <repository-url>
    cd <repository-directory>
    ```

2.  **Install Dependencies**:

    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration**:

    -   Copy `.env.example` to `.env`:
        ```bash
        cp .env.example .env
        ```
    -   Configure your database settings in `.env`:
        ```ini
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=socxo_chatbot
        DB_USERNAME=root
        DB_PASSWORD=
        ```
    -   Add your Google Gemini API Key:
        ```ini
        GEMINI_API_KEY=your_gemini_api_key_here
        ```

4.  **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

5.  **Run Migrations**:

    ```bash
    php artisan migrate
    ```

6.  **Build Frontend Assets**:

    ```bash
    npm run build
    ```

7.  **Run the Application**:

    ```bash
    php artisan serve
    ```

8.  **Access the App**:
    -   Open `http://127.0.0.1:8000` in your browser.
    -   Register a new account and start chatting!

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
