CREATE DATABASE IF NOT EXISTS kopipiro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kopipiro;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  description TEXT
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  description TEXT,
  price DECIMAL(12,2) NOT NULL,
  image VARCHAR(255),
  rating DECIMAL(2,1) NOT NULL DEFAULT 0,
  review_count INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Satu baris cart = satu produk. session_id dipakai untuk pengunjung.
CREATE TABLE carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  session_id VARCHAR(128) NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_cart_product (session_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  cashier_id INT NULL,
  customer_name VARCHAR(100) NOT NULL,
  customer_email VARCHAR(120),
  total_before_discount DECIMAL(12,2) NOT NULL,
  discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_payment DECIMAL(12,2) NOT NULL,
  status ENUM('Menunggu','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(120) NOT NULL,
  quantity INT NOT NULL,
  price_each DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT INTO roles (role_name) VALUES ('owner'), ('customer'), ('kasir');
INSERT INTO categories (name, description) VALUES
('Coffee', 'Produk kopi'),
('Non-Coffee', 'Minuman tanpa kopi'),
('Food', 'Makanan dan toast');

-- Buat akun owner melalui halaman setup_owner.php setelah database diimpor.
