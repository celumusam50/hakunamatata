# Hakuna Matata POS — API Reference v3

Base URL: `http://localhost/hakunamatata_v3/api`

All responses are JSON. Authenticated routes require:
```
Authorization: Bearer <access_token>
```

---

## AUTH  `/api/auth`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/login` | None | Login with email/phone + password |
| POST | `/auth/register` | None | Customer self-registration |
| POST | `/auth/refresh` | None | Refresh access token |
| POST | `/auth/logout` | None | Revoke refresh token |
| POST | `/auth/request-reset` | None | Request password reset OTP |
| POST | `/auth/verify-otp` | None | Verify OTP without resetting |
| POST | `/auth/reset-password` | None | Reset password with OTP |
| GET  | `/auth/me` | Any | Get current user profile |

### Login
```json
POST /auth/login
{ "email": "john@example.com", "password": "Pass@123" }

Response:
{ "access_token": "...", "refresh_token": "...", "expires_in": 900,
  "user": { "id": 1, "full_name": "John Doe", "role": "customer" } }
```

---

## USERS  `/api/users`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/users` | super_admin, manager | List all users (paginated, filterable) |
| GET | `/users/{id}` | self or management | Get user by ID |
| POST | `/users` | super_admin, manager | Create user (any role) |
| PUT | `/users/{id}` | self or management | Update profile |
| PATCH | `/users/{id}/status` | super_admin | Change status (active/suspended) |
| PATCH | `/users/{id}/password` | self | Change own password |
| DELETE | `/users/{id}` | super_admin | Deactivate user |

Query params for GET /users: `role`, `status`, `search`, `page`, `per_page`

---

## CATEGORIES  `/api/categories`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/categories` | Public | List categories |
| GET | `/categories/{id}` | Public | Get category |
| POST | `/categories` | manager, super_admin | Create category |
| PUT | `/categories/{id}` | manager, super_admin | Update category |
| DELETE | `/categories/{id}` | super_admin | Deactivate category |

Query params: `type` (Butchery/Restaurant/Liquor), `status`

---

## PRODUCTS  `/api/products`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/products` | Public | List products (with add-ons) |
| GET | `/products/{id}` | Public | Get product |
| POST | `/products` | manager, super_admin | Create product |
| PUT | `/products/{id}` | manager, super_admin | Update product |
| PATCH | `/products/{id}/stock` | manager, cashier | Adjust stock |
| PATCH | `/products/{id}/toggle` | manager, cashier | Toggle availability |
| DELETE | `/products/{id}` | super_admin | Deactivate product |

Query params: `category_id`, `type`, `search`, `badge`, `available=1`, `low_stock=1`

### Stock adjustment body:
```json
PATCH /products/5/stock
{ "qty_change": 10, "change_type": "restock", "notes": "Weekly delivery" }
```
change_type: `sale | restock | adjustment | damage | return | waste`

---

## CART  `/api/cart`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/cart` | customer | Get cart with totals |
| POST | `/cart` | customer | Add/update item |
| DELETE | `/cart/{cart_id}` | customer | Remove item |
| DELETE | `/cart` | customer | Clear entire cart |

```json
POST /cart
{ "product_id": 3, "quantity": 2, "addon_ids": [1,3], "notes": "Well done please" }
```

---

## ORDERS  `/api/orders`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/orders` | staff: all, customer: own | List orders (paginated) |
| GET | `/orders/{id}` | staff or owner | Get full order details |
| POST | `/orders` | customer, cashier | Place order |
| PATCH | `/orders/{id}/status` | staff | Update order status |
| PATCH | `/orders/{id}/cancel` | customer | Cancel own order |

### Place order body:
```json
POST /orders
{
  "order_type": "delivery",
  "channel": "web",
  "payment_method": "Mobile Money",
  "mobile_number": "+26876000001",
  "reference_no": "MM-REF-123",
  "delivery_type": "Standard",
  "street": "12 Main Road",
  "city": "Manzini",
  "region": "Manzini",
  "discount_code": "WELCOME10",
  "subtotal": 370.00,
  "delivery_fee": 80.00,
  "special_notes": "Call on arrival",
  "items": [
    { "product_id": 1, "name": "Ribeye Steak", "unit_price": 185.00,
      "quantity": 2, "addon_total": 0, "line_total": 370.00 }
  ]
}
```

Order statuses: `pending → confirmed → preparing → ready → dispatched → delivered/completed`

Query params: `status`, `order_type`, `channel`, `date_from`, `date_to`, `ref`, `customer_id`, `page`, `per_page`

---

## PAYMENTS  `/api/payments`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/payments` | manager, cashier | List payments |
| GET | `/payments/{id}` | staff | Get payment |
| PATCH | `/payments/{id}/pay` | cashier, manager | Mark CoD as paid |

---

## DELIVERIES  `/api/deliveries`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/deliveries` | staff | All deliveries (driver sees own) |
| PATCH | `/deliveries/{order_id}/assign` | manager | Assign driver to order |
| PATCH | `/deliveries/{order_id}/status` | driver | Update delivery status |
| PATCH | `/deliveries/{order_id}/confirm` | driver | Confirm delivery with OTP |

Delivery statuses: `pending → assigned → en_route → arrived → confirmed`

```json
PATCH /deliveries/5/confirm
{ "otp": "847291" }
```

---

## DISCOUNTS  `/api/discounts`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/discounts` | manager, super_admin | List all discounts |
| GET | `/discounts/{id}` | manager, super_admin | Get discount |
| POST | `/discounts` | manager, super_admin | Create discount |
| POST | `/discounts/validate` | Public (optional auth) | Validate promo code |
| PUT | `/discounts/{id}` | manager, super_admin | Update discount |
| DELETE | `/discounts/{id}` | super_admin | Deactivate discount |

```json
POST /discounts/validate
{ "code": "WELCOME10" }
```

---

## TABLES  `/api/tables`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/tables` | Public | List tables + status |
| POST | `/tables` | manager | Add table |
| PATCH | `/tables/{id}` | cashier, manager | Update table status |
| DELETE | `/tables/{id}` | super_admin | Remove table |

Table statuses: `available | occupied | reserved | cleaning`

---

## REPORTS  `/api/reports`

All require: `manager` or `super_admin`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports/dashboard` | Today's KPIs, pending orders, stock alerts |
| GET | `/reports/sales` | Daily sales by channel and order type |
| GET | `/reports/products` | Product/menu performance |
| GET | `/reports/drivers` | Driver delivery performance |
| GET | `/reports/cashiers` | Cashier sales performance |
| GET | `/reports/stock-alerts` | Low/out-of-stock products |

Query params: `date_from`, `date_to`, `channel`, `order_type`, `category_type`, `stock_status`

---

## NOTIFICATIONS  `/api/notifications`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/notifications` | Any | Get own notifications |
| PATCH | `/notifications/{id}/read` | Any | Mark one as read |
| PATCH | `/notifications` | Any | Mark all as read |
| DELETE | `/notifications/{id}` | Any | Delete notification |

Query params: `unread=1`, `page`, `per_page`

---

## SETTINGS  `/api/settings`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/settings` | Public (subset) / Management (all) | Get system settings |
| PUT | `/settings` | super_admin | Update settings (key-value body) |

---

## UPLOAD  `/api/upload`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/upload/product` | manager | Upload product image |
| POST | `/upload/avatar` | Any | Upload own avatar |
| POST | `/upload/logo` | super_admin | Upload business logo |

Use `multipart/form-data` with key `file`. Optionally include `product_id` for product uploads.

---

## ERROR RESPONSES

```json
{ "success": false, "error": "Human-readable message", "errors": ["Validation error 1"] }
```

| Code | Meaning |
|------|---------|
| 400 | Bad request / validation error |
| 401 | Not authenticated |
| 403 | Forbidden (wrong role) |
| 404 | Not found |
| 409 | Conflict (duplicate, constraint) |
| 422 | Validation failed |
| 500 | Server error |
| 503 | Database unavailable |

---

## ROLE PERMISSIONS SUMMARY

| Endpoint Group | customer | driver | cashier | manager | super_admin |
|----------------|----------|--------|---------|---------|-------------|
| Auth (login/register) | ✅ | ✅ | ✅ | ✅ | ✅ |
| Products (read) | ✅ | ✅ | ✅ | ✅ | ✅ |
| Products (write) | ❌ | ❌ | stock only | ✅ | ✅ |
| Cart | ✅ | ❌ | ❌ | ❌ | ✅ |
| Orders (place) | ✅ | ❌ | ✅ (CoD) | ❌ | ✅ |
| Orders (manage) | own only | ❌ | ✅ | ✅ | ✅ |
| Deliveries | ❌ | own only | view | ✅ | ✅ |
| Discounts | ❌ | ❌ | ❌ | ✅ | ✅ |
| Reports | ❌ | ❌ | stock alerts | ✅ | ✅ |
| Users (manage) | own profile | own profile | own profile | ✅ | ✅ |
| Settings | ❌ | ❌ | ❌ | read only | ✅ |

---

## SETUP

1. Import database: `mysql -u root -p < hakunamatata_v3_schema.sql`
2. Copy API folder to `/var/www/html/hakunamatata_v3/api/`
3. Edit `config.php` — set DB credentials and JWT secret
4. Create uploads folder: `mkdir -p uploads/{products,avatars,brand} && chmod 755 uploads`
5. Enable Apache mod_rewrite: `a2enmod rewrite`
6. Test: `GET /api/ping` → `{ "status": "ok" }`

**Default credentials (all: Admin@1234)**
- Super Admin: `admin@hakunamatata.co.sz`
- Manager: `manager@hakunamatata.co.sz`
- Cashier 1: `cashier1@hakunamatata.co.sz`
- Driver: `sipho@hakunamatata.co.sz`
- Customer: `john@example.com`
