<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Getting Started

### Prerequisites

- PHP \>= 7.3
- Composer
- Node.js & npm
- MySQL or any other supported database

### Installation

1. **Clone the repository**:
    ```sh
    git clone https://github.com/programmerstilesj/nyt-api.git
    cd nyt-api
    ```

2. **Install dependencies**:
    ```sh
    composer install
    ```

3. **Copy the `.env.example` file to `.env`**:
    ```sh
    cp .env.example .env
    ```

4. **Generate an application key**:
    ```sh
    php artisan key:generate
    ```

5. **Set up the NYT API key and base URL in the `.env` file**:
    ```env
    NYT_API_KEY=your_nyt_api_key
    NYT_BASE_URL=https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json
    ```

### Running the Application

1. **Start the development server**:
    ```sh
    php artisan serve
    ```

2. **Access the application**:
   Open your browser and go to `http://localhost:8000/api/v1/nyt/best-sellers`.

### Calling the Endpoint

To call the `api/v1/nyt/best-sellers` endpoint, you can use a tool like `curl` or Postman. 

**Example using `curl`**:
    ```sh
    curl -X GET "http://localhost:8000/api/v1/nyt/best-sellers?author=John%20Doe" -H "Accept: application/json"
    ```


### Running Tests
Run the tests:
```sh
php artisan test
```
