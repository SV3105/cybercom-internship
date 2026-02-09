# EasyCart Project Control Flow Documentation

This document outlines the execution path of the EasyCart application, from the initial request to the final response.

## 1. The Entry Point: `public/index.php`

Every single request to EasyCart (except for static files like CSS/JS) starts here.

- **Session Start**: `session_start()` initializes user sessions.
- **Config Loading**: `require_once '../config/config.php'` connects to the database.
- **Routing**:
  1.  The script captures the URL using `$_SERVER['REQUEST_URI']`.
  2.  It strips the base directory (e.g., `/cybercom-internship-v2/easycart/`) to get a clean "route" (e.g., `products`, `cart`, `admin/orders`).
  3.  It loads `routes/web.php` which acts as the **Map** for the application.

## 2. The Router: `routes/web.php`

This file returns a large associative array that maps **URLs** to **Controllers and Methods**.

- **Example Mapping**:
  ```php
  'admin/orders' => ['controller' => 'AdminController', 'method' => 'orders']
  ```
- The `index.php` checks if the requested URL exists in this map. If yes, it loads the corresponding controller file.

## 3. The Controller Layer: `controllers/`

Controllers handle the **Business Logic**. They act as middle-men between the Database (Model) and the User Interface (View).

### Flow inside a Controller Method:

1.  **Auth Check**: (Optional) Checks if `$_SESSION['user']` or `$_SESSION['admin_user']` is set.
2.  **Data Fetching**: Calls the **Model** to get data from the database.
3.  **View Rendering**: Includes the **Layout Header**, the specific **Page View**, and the **Layout Footer**.

**Example: `ProductController->list()`**

- Calls `ProductModel->getProducts()`.
- Includes `views/layouts/header.php`.
- Includes `views/products/list.php`.
- Includes `views/layouts/footer.php`.

## 4. The Model Layer: `models/`

Models are the only files that "talk" to the database using SQL.

- **PDO**: Every model receives a `$pdo` object in its constructor.
- **Fetch Methods**: Use `fetch()` for single rows and `fetchAll(PDO::FETCH_ASSOC)` for multiple rows.
- **Upsert Methods**: Use Prepared Statements (`prepare` + `execute`) to insert or update data securely.

## 5. The View Layer: `views/`

Views are mostly HTML mixed with simple PHP `echo` or `foreach` loops.

- **Dynamic Data**: Data passed from the Controller is available here as variables (e.g., `$products`, `$order`).
- **Partials**: Common elements like the Sidebar or Navigation are in `views/layouts/`.

---

## Key Execution Paths (Control Flows)

### A. Admin Product Export Flow

1.  **Browser**: Clicks `admin/export-download?filter=low_stock`.
2.  **Server (.htaccess)**: Rewrites to `index.php?filter=low_stock` (preserving params with `QSA`).
3.  **index.php**: Sees route `admin/export-download`, instantiates `AdminController`.
4.  **AdminController->exportProducts()**:
    - Reads `$_GET['filter']`.
    - Calls `$productModel->exportToCSV('low_stock')`.
5.  **ProductModel->exportToCSV()**:
    - Runs SQL `SELECT ... WHERE stock_qty < 10`.
    - Returns array of data.
6.  **AdminController**:
    - Sends CSV headers.
    - Writes data to `php://output` using `fputcsv()`.

### B. User Login Flow

1.  **Browser**: Submits Login Form to `login`.
2.  **index.php**: Routes to `AuthController->login()`.
3.  **AuthController->login()**:
    - Calls `$this->userModel->login($email, $password)`.
4.  **UserModel->login()**:
    - Fetches row by email.
    - Uses `password_verify($password, $hash)` to check credentials.
    - Returns user data or `false`.
5.  **AuthController**:
    - If success, saves user data to `$_SESSION['user']`.
    - Returns JSON `{"success": true}` to the frontend JS.

### C. Admin Order Management

1.  **Route**: `admin/order-view?id=4`.
2.  **AdminController->orderDetails()**:
    - Queries `AdminModel->getOrderDetails(4)`.
    - Queries `AdminModel->getOrderItems(4)`.
3.  **View**: `views/admin/order_details.php` renders the collected info.

---

## 📂 File Summary

| Directory      | Purpose                                     |
| :------------- | :------------------------------------------ |
| `public/`      | Assets (CSS/JS/Images) and the Entry Point. |
| `config/`      | Database and Global Constants.              |
| `controllers/` | Decisions and Logic.                        |
| `models/`      | Direct SQL Database interaction.            |
| `views/`       | HTML Templates and UI Layouts.              |
| `routes/`      | The URL-to-Code mapping logic.              |
