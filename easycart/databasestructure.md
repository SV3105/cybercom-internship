# EasyCart Database Structure

This document outlines the table structure for the EasyCart application, designed for PostgreSQL. It follows the Entity-Attribute-Value (EAV) pattern for the Catalog and a strict Relational pattern for Sales/Orders.

---

## 1. Catalog System (Products & Categories)

_Stores what you sell. Flexible structure for attributes and classifications._

### Product Tables

| Table Name                      | Description                                  | Columns                                                                                                       |
| :------------------------------ | :------------------------------------------- | :------------------------------------------------------------------------------------------------------------ |
| **`catalog_product_entity`**    | The main product definition (The "Skeleton") | `entity_id` (PK, Serial)<br>`sku` (Unique)<br>`name`<br>`price`<br>`created_at`<br>`updated_at`               |
| **`catalog_product_attribute`** | Dynamic details (Sizes, Colors, etc.)        | `attribute_id` (PK, Serial)<br>`product_id` (FK)<br>`attribute_code` (e.g., 'color')<br>`value` (e.g., 'Red') |
| **`catalog_product_image`**     | Product Gallery                              | `image_id` (PK, Serial)<br>`product_id` (FK)<br>`image_path`<br>`is_thumbnail` (Boolean)                      |

### Category Tables

| Table Name                       | Description                                 | Columns                                                                          |
| :------------------------------- | :------------------------------------------ | :------------------------------------------------------------------------------- |
| **`catalog_category_entity`**    | Category identity                           | `entity_id` (PK, Serial)<br>`parent_id` (0 for root)<br>`created_at`             |
| **`catalog_category_attribute`** | Category details (Names, SEO, Descriptions) | `attribute_id` (PK, Serial)<br>`category_id` (FK)<br>`attribute_code`<br>`value` |
| **`catalog_category_products`**  | Mapping Products to Categories              | `id` (PK, Serial)<br>`category_id` (FK)<br>`product_id` (FK)                     |

### Brand Tables

| Table Name                    | Description                               | Columns                                                                       |
| :---------------------------- | :---------------------------------------- | :---------------------------------------------------------------------------- |
| **`catalog_brand_entity`**    | Brand identity                            | `entity_id` (PK, Serial)<br>`name` (e.g., 'Nike')<br>`created_at`             |
| **`catalog_brand_attribute`** | Brand details (Logo, Banner, Description) | `attribute_id` (PK, Serial)<br>`brand_id` (FK)<br>`attribute_code`<br>`value` |
| **`catalog_brand_products`**  | Mapping Products to Brands                | `id` (PK, Serial)<br>`brand_id` (FK)<br>`product_id` (FK)                     |

---

## 2. Sales Cart System (Temporary Storage)

_Stores the user's shopping session. Data lives here until checkout is complete._

| Table Name                | Description                   | Columns                                                                                                                                                  |
| :------------------------ | :---------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **`sales_cart`**          | The Shopping Session Basket   | `id` (PK, Serial)<br>`session_id` (Unique)<br>`user_id` (Nullable)<br>`is_active` (Boolean, default True)<br>`grand_total`<br>`created_at`               |
| **`sales_cart_products`** | Items currently in the basket | `id` (PK, Serial)<br>`cart_id` (FK)<br>`product_id` (FK)<br>`quantity`<br>`price` (Cached price)                                                         |
| **`sales_cart_address`**  | Temporary Checkout Address    | `id` (PK, Serial)<br>`cart_id` (FK)<br>`address_type` ('billing'/'shipping')<br>`firstname`<br>`lastname`<br>`email`<br>`street`<br>`city`<br>`postcode` |
| **`sales_cart_shipping`** | Selected Shipping Method      | `id` (PK, Serial)<br>`cart_id` (FK)<br>`method_code`<br>`carrier_code`<br>`price`                                                                        |
| **`sales_cart_payment`**  | Selected Payment Info         | `id` (PK, Serial)<br>`cart_id` (FK)<br>`method_code`<br>`po_number`                                                                                      |

---

## 3. Sales Order System (Permanent Record)

_Created on "Place Order". A frozen snapshot of the transaction._

| Table Name                 | Description                    | Columns                                                                                                                                                                                                   |
| :------------------------- | :----------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **`sales_order`**          | The Finalized Receipt          | `order_id` (PK, Serial)<br>`increment_id` (Order #)<br>`user_id`<br>`status` (pending, completed)<br>`subtotal`<br>`shipping_amount`<br>`tax_amount`<br>`grand_total`<br>`customer_email`<br>`created_at` |
| **`sales_order_products`** | Snapshot of purchased items    | `id` (PK, Serial)<br>`order_id` (FK)<br>`product_id` (FK)<br>`sku`<br>`name` (Frozen Name)<br>`price` (Frozen Price)<br>`quantity`<br>`total_price`                                                       |
| **`sales_order_address`**  | Final Shipping/Billing Address | `id` (PK, Serial)<br>`order_id` (FK)<br>`address_type`<br>`firstname`<br>`lastname`<br>`street`<br>`city`<br>`postcode`<br>`telephone`                                                                    |
| **`sales_order_payment`**  | Payment Record                 | `id` (PK, Serial)<br>`order_id` (FK)<br>`method`                                                                                                                                                          |

---

## 4. Key Logic & Rules

### Session Handling

1.  **Guest Identification**:
    - Guests do not have a `user_id`.
    - Instead, their specific identifier is the **`cart_id`** (from `sales_cart.id`).
    - This `cart_id` is stored in the user's browser session (`$_SESSION['cart_id']`).
2.  **Flow**:
    - Check `$_SESSION` for `cart_id`.
    - If **None** -> Create new `sales_cart` row -> Store new `cart_id` in Session.
    - If **Exists** -> Use `cart_id` to retrieve their cart from the database.

### Checkout Logic ("Place Order")

1.  **Freeze**: Copy all data from `sales_cart_*` tables into `sales_order_*` tables.
2.  **Deactivate**: Update `sales_cart` row: Set `is_active = 0`.
3.  **Clear**: Remove `cart_id` from PHP `$_SESSION`.
4.  **Result**: The Order is saved forever. The User is now "cartless" and ready to start a new shopping trip.
