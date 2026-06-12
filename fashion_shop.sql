-- ============================================
-- Fashion Shop - Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS fashion_shop
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fashion_shop;

-- ============================================
-- Bảng danh mục sản phẩm
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    icon        VARCHAR(50),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Bảng sản phẩm
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(200) NOT NULL,
    price           DECIMAL(12,0) NOT NULL,
    image           VARCHAR(255),
    description     TEXT,
    category_id     INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Bảng đơn hàng (orders)
-- Lưu toàn bộ thông tin khách hàng + đơn hàng
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    customer_name       VARCHAR(100) NOT NULL COMMENT 'Tên khách hàng',
    customer_phone      VARCHAR(20) NOT NULL COMMENT 'Số điện thoại',
    customer_email      VARCHAR(100) COMMENT 'Email (tuỳ chọn)',
    shipping_address    TEXT NOT NULL COMMENT 'Địa chỉ giao hàng',
    city                VARCHAR(50) COMMENT 'Tỉnh/Thành phố',
    district            VARCHAR(50) COMMENT 'Quận/Huyện',
    note                TEXT COMMENT 'Ghi chú đơn hàng',
    payment_method      VARCHAR(30) NOT NULL DEFAULT 'cod' COMMENT 'cod | bank | ewallet',
    subtotal            DECIMAL(12,0) NOT NULL COMMENT 'Tạm tính',
    shipping_fee        DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Phí vận chuyển',
    total               DECIMAL(12,0) NOT NULL COMMENT 'Tổng thanh toán',
    status              VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending | confirmed | shipping | delivered | cancelled',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Bảng chi tiết đơn hàng (order_items)
-- Lưu từng sản phẩm trong đơn
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT NOT NULL,
    product_id      INT NOT NULL,
    product_name    VARCHAR(200) NOT NULL COMMENT 'Lưu tên SP tại thời điểm mua',
    product_image   VARCHAR(255) COMMENT 'Lưu ảnh SP tại thời điểm mua',
    price           DECIMAL(12,0) NOT NULL COMMENT 'Giá tại thời điểm mua',
    quantity        INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Bảng người dùng (users)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    fullname        VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    phone           VARCHAR(20) DEFAULT NULL,
    password        VARCHAR(255) NOT NULL COMMENT 'Bcrypt hash',
    address         TEXT DEFAULT NULL,
    avatar          VARCHAR(255) DEFAULT NULL,
    reset_token     VARCHAR(100) DEFAULT NULL COMMENT 'Token khôi phục mật khẩu',
    reset_expires   DATETIME DEFAULT NULL COMMENT 'Hạn token',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
