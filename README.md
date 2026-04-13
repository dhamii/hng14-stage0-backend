# Gender Classification API

A RESTful API built with Laravel that predicts the gender of a person based on their name using the Genderize.io service.

## Overview

This API provides endpoints to:
- Check the health of the API
- Classify names by predicted gender with confidence scoring

The API includes **CORS support** to allow cross-origin requests and returns consistent JSON responses with proper error handling.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm (for asset compilation)

## Installation

1. **Clone or navigate to the project directory:**
   ```bash
   cd hng-stage0-backend
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   ```

4. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

## Running the Server

Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### 1. Health Check
**GET** `/api/hi`

Returns a simple health check response.

**Response:**
```
Okay
```

---

### 2. Classify Name by Gender
**GET** `/api/classify`

Predicts the gender of a person based on their name using the Genderize.io API.

**Query Parameters:**
| Parameter | Type   | Required | Description       |
|-----------|--------|----------|-------------------|
| name      | string | Yes      | The name to classify |

**Example Request:**
```bash
curl "http://localhost:8000/api/classify?name=john"
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "name": "john",
    "gender": "male",
    "probability": 0.98,
    "sample_size": 5234,
    "is_confident": true,
    "processed_at": "2026-04-13T14:30:00Z"
  }
}
```

**Error Response - Missing name (400 Bad Request):**
```json
{
  "status": "error",
  "message": "Bad Request"
}
```

**Error Response - Numeric input (400 Bad Request):**
```json
{
  "status": "error",
  "message": "No prediction available for the provided name"
}
```

**Error Response - No prediction found (400 Bad Request):**
```json
{
  "status": "error",
  "message": "No prediction available for the provided name"
}
```

---

## Response Fields Explanation

| Field | Type | Description |
|-------|------|-------------|
| `status` | string | Either `"success"` or `"error"` |
| `data.name` | string | The name provided in the request |
| `data.gender` | string | `"male"` or `"female"` |
| `data.probability` | float | Confidence probability (0-1) |
| `data.sample_size` | integer | Number of data samples used for prediction |
| `data.is_confident` | boolean | `true` if probability ≥ 0.7 AND sample_size ≥ 100 |
| `data.processed_at` | string | ISO 8601 timestamp of when the request was processed |

---

## Validation Rules

- **Name is required**: Request must include the `name` parameter
- **Name must not be numeric**: Pure numbers will be rejected
- **Name must exist in database**: The Genderize.io API must have data for the name

---

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── ApiController.php    # API logic
├── Models/
│   └── User.php                 # User model
└── Providers/

routes/
├── api.php                       # API routes
├── web.php                       # Web routes
└── console.php                   # Console commands

config/                          # Configuration files
database/                        # Migrations, seeders, factories
resources/                       # Views, CSS, JavaScript
tests/                          # Unit and feature tests
```

---

## Error Handling

All errors return a consistent JSON format with an appropriate HTTP status code:
- **400 Bad Request**: Invalid input or no prediction available
- **500 Internal Server Error**: Server-side issues

---

## Testing

Run the test suite:
```bash
php artisan test
```

---

## External Services

This API integrates with:
- **Genderize.io API** - Provides gender prediction data based on names

---

## CORS Support

The API sets the `Access-Control-Allow-Origin: *` header on all responses to allow cross-origin requests from client applications.

---

## Built With

- **Laravel** - PHP web framework
- **Guzzle HTTP** - HTTP client for external API calls

---

## License

Licensed under the MIT license. See LICENSE file for details.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
