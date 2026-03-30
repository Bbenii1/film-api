# Film API Documentation

Welcome to the Film API documentation. This API allows you to manage films (products), directors, categories, and stars.

## Base URL
`http://localhost:8000` (or your configured local server)

## Response Format
All responses are returned in JSON format with the following structure:
```json
{
  "code": 200,
  "data": { ... }
}
```

---

## Authentication

Authentication is handled via PHP Sessions. To access protected routes, you must first log in.

### Login
*   **URL:** `/users/login`
*   **Method:** `POST`
*   **Body:**
    ```json
    {
      "username": "your_username"
    }
    ```
*   **Response:** Sets a session cookie and returns a success message.

### Logout
*   **URL:** `/users/logout`
*   **Method:** `POST`
*   **Response:** Clears the session and returns a success message.

---

## Products (Films)

The `/products` endpoint provides access to the film database.

### List All Products
*   **URL:** `/products`
*   **Method:** `GET`
*   **Auth Required:** No

### Get Product by ID
*   **URL:** `/products/{id}`
*   **Method:** `GET`
*   **Auth Required:** No

### Create New Product
*   **URL:** `/products`
*   **Method:** `POST`
*   **Auth Required:** **Yes**
*   **Body:**
    ```json
    {
      "title": "Interstellar",
      "subtitle": 1,
      "age_restriction": "PG13",
      "release_year": 2014,
      "description": "...",
      "cover_url": "http://..."
    }
    ```

### Update Product (Partial)
*   **URL:** `/products/{id}`
*   **Method:** `PATCH`
*   **Auth Required:** **Yes**
*   **Body:** Any subset of product fields.

### Delete Product
*   **URL:** `/products/{id}`
*   **Method:** `DELETE`
*   **Auth Required:** **Yes**

---

## Other Resources

All the following resources support `GET` requests for listing or retrieving by ID.

### Directors
*   `GET /directors` - List all directors
*   `GET /directors/{id}` - Get director details

### Categories
*   `GET /categories` - List all categories
*   `GET /categories/{id}` - Get category details

### Stars
*   `GET /stars` - List all stars
*   `GET /stars/{id}` - Get star details

---

## Error Codes
- `200 OK`: Request successful.
- `201 Created`: Resource created successfully.
- `400 Bad Request`: Invalid input or missing parameters.
- `401 Unauthorized`: Authentication required or invalid credentials.
- `404 Not Found`: Resource not found.
``` bash
# Example: Authenticated Request with cURL
curl -X POST http://localhost:8000/users/login -d '{"username": "admin"}' -c cookies.txt
curl -X DELETE http://localhost:8000/products/1 -b cookies.txt
```
