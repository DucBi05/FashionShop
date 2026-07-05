-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 05, 2026 lúc 05:26 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `fashion_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 'test_session_001', 1, 2, '2026-06-05 06:22:48'),
(2, 'test_session_001', 3, 1, '2026-06-05 06:22:48'),
(3, 'test_session_001', 8, 3, '2026-06-05 06:22:48'),
(4, 'test_session_001', 2, 1, '2026-06-05 06:22:48'),
(5, 'test_session_001', 5, 2, '2026-06-05 06:22:48'),
(11, '7', 64, 16, '2026-06-28 14:08:59'),
(12, '7', 63, 2, '2026-06-28 14:09:07'),
(13, '7', 62, 1, '2026-06-28 14:10:18'),
(14, '7', 61, 1, '2026-06-28 14:11:23'),
(15, '7', 60, 2, '2026-06-28 14:13:12'),
(16, '7', 59, 1, '2026-06-28 14:14:50'),
(17, '7', 46, 12, '2026-06-29 17:39:21'),
(18, '7', 57, 1, '2026-06-29 17:50:21'),
(19, '7', 58, 1, '2026-06-29 17:50:36'),
(20, '7', 45, 1, '2026-06-29 17:50:46'),
(21, '7', 47, 1, '2026-06-29 17:50:48'),
(22, '7', 48, 1, '2026-06-29 17:50:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`) VALUES
(1, 'Áo', '👕'),
(2, 'Quần', '👖'),
(3, 'Váy', '👗'),
(4, 'Túi', '👜'),
(5, 'Giày', '👟'),
(6, 'Phụ kiện', '💍');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'user1 vừa tạo tài khoản', 1, '2026-05-31 14:44:52'),
(2, 8, 'Nguyễn Văn A vừa đăng ký tài khoản mới.', 1, '2026-06-17 04:38:46'),
(3, 11, 'Nguyễn Văn C vừa đăng ký tài khoản mới.', 1, '2026-07-02 13:14:30'),
(4, 50, 'hi', 1, '2026-07-02 14:18:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(30) NOT NULL DEFAULT 'cod',
  `subtotal` decimal(12,0) NOT NULL,
  `shipping_fee` decimal(12,0) NOT NULL DEFAULT 0,
  `total` decimal(12,0) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `city`, `district`, `note`, `payment_method`, `subtotal`, `shipping_fee`, `total`, `status`, `created_at`) VALUES
(1, 1, 'Nhật', '08192616182', '', 'TPHCM', NULL, NULL, NULL, 'cod', 0, 0, 1500000, 'Delivered', '2026-05-23 16:55:03'),
(2, 5, 'Thành', '08192616184', '', 'TPHCM', NULL, NULL, NULL, 'cod', 0, 0, 3000000, 'Delivered', '2026-06-16 16:55:36'),
(6, 7, 'Nguyễn Đức Bỉ', '0912817612', 'ducbi1@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'cod', 2400000, 0, 2400000, 'Confirmed', '2026-06-19 16:46:36'),
(7, 7, 'Nguyễn Đức Bỉ', '0912817612', 'ducbi1@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'cod', 5050000, 0, 5050000, 'Shipping', '2026-06-19 16:50:57'),
(8, 7, 'Nguyễn Đức Bỉ', '0912817612', 'ducbi1@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'bank', 900000, 0, 900000, 'Cancelled', '2026-06-19 16:57:00'),
(9, 7, 'Nguyễn Đức Bỉ', '0912817612', 'ducbi1@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'cod', 500000, 0, 500000, 'Pending', '2026-06-19 17:36:34'),
(10, 7, 'Nguyễn Đức Bỉ', '0912817612', 'ducbi1@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'cod', 1000000, 0, 1000000, 'Delivered', '2026-06-19 17:45:08'),
(12, 11, 'Nguyễn Văn C', '0925257211', 'cuser@gmail.com', '566 Nguyễn Thái Sơn', 'TPHCM', 'Quận 1', '', 'cod', 1100000, 0, 1100000, 'Delivered', '2026-07-02 14:07:10'),
(13, 15, 'Nguyễn Văn D', '0901234568', 'duser@gmail.com', '566 Nguyễn Thái Sơn', 'Thành phố Hồ Chí Minh', 'Quận Gò Vấp', '', 'cod', 400000, 30000, 430000, 'Pending', '2026-07-05 15:17:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `quantity`, `price`) VALUES
(1, 1, 4, '', NULL, 2, 520000),
(3, 6, 10, 'Running Shoes', 'running.jpg', 2, 1200000),
(4, 7, 3, 'Jeans Slim Fit', 'jeans.jpg', 4, 450000),
(5, 7, 5, 'Vintage Dress', 'dress.jpg', 5, 650000),
(6, 8, 3, 'Jeans Slim Fit', 'jeans.jpg', 2, 450000),
(7, 9, 1, 'Oversize T-Shirt', 'tshirt.jpg', 2, 250000),
(8, 10, 1, 'Oversize T-Shirt', 'tshirt.jpg', 4, 250000),
(9, 12, 64, 'Beanie Hat', 'beanie.png', 5, 220000),
(10, 13, 63, 'Silver Ring', 'ring.jpg', 1, 180000),
(11, 13, 64, 'Beanie Hat', 'beanie.png', 1, 220000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sold` int(11) DEFAULT 0,
  `discount_percent` int(3) DEFAULT 0,
  `badge` varchar(20) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `review_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `image`, `description`, `stock`, `created_at`, `sold`, `discount_percent`, `badge`, `rating`, `review_count`) VALUES
(1, 1, 'Oversize T-Shirt', 250000.00, 'tshirt.jpg', 'Áo vải mịn, mát mẻ.', 13, '2026-05-23 11:51:56', 4, 10, 'new', 0.0, 0),
(2, 1, 'Polo Basic', 320000.00, 'polo.jpg', 'Áo polo basic trẻ trung năng động.', 15, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(3, 2, 'Jeans Slim Fit', 450000.00, 'jeans.jpg', 'Quần jeans slim fit co giãn thoải mái.', 10, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(4, 2, 'Cargo Pants', 520000.00, 'cargo.jpg', 'Quần cargo phong cách Hàn Quốc.', 12, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(5, 3, 'Vintage Dress', 650000.00, 'dress.jpg', 'Váy vintage nữ tính thanh lịch.', 8, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(6, 3, 'Mini Skirt', 350000.00, 'skirt.jpg', 'Chân váy ngắn trẻ trung.', 14, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(7, 4, 'Leather Bag', 780000.00, 'bag.jpg', 'Túi xách da cao cấp.', 4, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(8, 4, 'Tote Bag', 290000.00, 'tote.jpg', 'Túi tote thời trang unisex.', 18, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(9, 5, 'Sneaker Basic', 890000.00, 'sneaker.jpg', 'Giày sneaker basic dễ phối đồ.', 9, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(10, 5, 'Running Shoes', 1200000.00, 'running.jpg', 'Giày chạy bộ nhẹ và êm.', 6, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(11, 6, 'Silver Necklace', 220000.00, 'necklace.jpg', 'Dây chuyền bạc phong cách tối giản.', 25, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(12, 6, 'Fashion Cap', 180000.00, 'cap.jpg', 'Nón thời trang street style.', 30, '2026-05-23 11:51:56', 0, 0, NULL, 0.0, 0),
(45, 1, 'Graphic T-Shirt', 280000.00, 'graphic-tshirt.jpg', 'Áo thun graphic phong cách streetwear.', 20, '2026-06-27 09:11:25', 35, 10, NULL, 4.8, 215),
(46, 1, 'Hoodie Oversize', 550000.00, 'hoodie.jpg', 'Áo hoodie form rộng unisex.', 15, '2026-06-27 09:11:25', 48, 0, 'best', 4.9, 387),
(47, 1, 'Oxford Shirt', 420000.00, 'oxford-shirt.jpg', 'Áo sơ mi Oxford lịch lãm.', 18, '2026-06-27 09:11:25', 22, 5, NULL, 4.5, 102),
(48, 1, 'Sweater Basic', 480000.00, 'sweater.jpg', 'Áo len basic mùa đông.', 12, '2026-06-27 09:11:25', 17, 20, NULL, 4.7, 88),
(49, 2, 'Jogger Pants', 390000.00, 'jogger.jpg', 'Quần jogger thể thao.', 25, '2026-06-27 09:11:25', 41, 10, NULL, 4.8, 191),
(50, 2, 'Wide Leg Pants', 460000.00, 'wideleg.jpg', 'Quần ống rộng phong cách Hàn Quốc.', 14, '2026-06-27 09:11:25', 29, 0, 'new', 4.6, 75),
(51, 2, 'Short Jeans', 320000.00, 'short-jeans.jpg', 'Quần short jeans năng động.', 30, '2026-06-27 09:11:25', 18, 15, NULL, 4.4, 53),
(52, 3, 'Floral Dress', 720000.00, 'floral-dress.jpg', 'Váy hoa nữ tính.', 9, '2026-06-27 09:11:25', 26, 20, NULL, 4.9, 145),
(53, 3, 'Maxi Dress', 850000.00, 'maxi-dress.jpg', 'Váy maxi dự tiệc.', 6, '2026-06-27 09:11:25', 12, 10, 'new', 4.8, 82),
(54, 3, 'Pleated Skirt', 410000.00, 'pleated-skirt.jpg', 'Chân váy xếp ly.', 16, '2026-06-27 09:11:25', 23, 5, NULL, 4.5, 66),
(55, 4, 'Crossbody Bag', 620000.00, 'crossbody.jpg', 'Túi đeo chéo thời trang.', 11, '2026-06-27 09:11:25', 31, 15, NULL, 4.7, 123),
(56, 4, 'Canvas Backpack', 550000.00, 'backpack.jpg', 'Balo canvas trẻ trung.', 13, '2026-06-27 09:11:25', 28, 10, NULL, 4.6, 97),
(57, 4, 'Mini Handbag', 690000.00, 'mini-handbag.jpg', 'Túi xách mini cao cấp.', 8, '2026-06-27 09:11:25', 15, 0, 'best', 4.9, 178),
(58, 5, 'Chunky Sneaker', 1100000.00, 'chunky.jpg', 'Giày chunky sneaker cá tính.', 10, '2026-06-27 09:11:25', 55, 0, 'best', 5.0, 412),
(59, 5, 'High Top Sneaker', 980000.00, 'hightop.jpg', 'Sneaker cổ cao.', 12, '2026-06-27 09:11:25', 37, 5, NULL, 4.8, 224),
(60, 5, 'Loafer Shoes', 890000.00, 'loafer.jpg', 'Giày loafer thanh lịch.', 9, '2026-06-27 09:11:25', 19, 15, NULL, 4.6, 84),
(61, 6, 'Leather Belt', 250000.00, 'belt.jpg', 'Thắt lưng da cao cấp.', 35, '2026-06-27 09:11:25', 40, 0, 'new', 4.5, 131),
(62, 6, 'Round Sunglasses', 320000.00, 'sunglasses.jpg', 'Kính mát thời trang.', 22, '2026-06-27 09:11:25', 33, 10, NULL, 4.7, 149),
(63, 6, 'Silver Ring', 180000.00, 'ring.jpg', 'Nhẫn bạc tối giản.', 39, '2026-06-27 09:11:25', 28, 20, NULL, 4.6, 118),
(64, 6, 'Beanie Hat', 220000.00, 'beanie.png', 'Mũ len thời trang.', 22, '2026-06-27 09:11:25', 22, 5, NULL, 4.3, 61);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Bcrypt hash',
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `reset_token` varchar(100) DEFAULT NULL COMMENT 'Token khôi phục mật khẩu',
  `reset_expires` datetime DEFAULT NULL COMMENT 'Hạn token',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `avatar`, `email`, `phone`, `address`, `password`, `role`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'Nhật', NULL, 'nhat99@gmail.com', NULL, NULL, '123456', 'customer', NULL, NULL, '2026-06-03 13:32:35', '2026-06-16 15:57:30'),
(2, 'Administrator', NULL, 'admin@gmail.com', NULL, NULL, '$2y$10$Age1tBcHnr8vtkuJY6LMo.iwxFtTbm14osIAVgkW1o7dCR1xCyYEa', 'admin', NULL, NULL, '2026-06-04 12:57:49', '2026-06-16 15:57:30'),
(5, 'Thành', 'default.jpg', 'thanh@gmail.com', '0901234560', 'TPHCM', '$2y$10$iSAdA5MiJfFd/KNdphWN0OOP6tOP8UX5/eqR/gmUrVWBxKb1Rjimm', 'customer', NULL, NULL, '2026-06-12 05:23:00', '2026-06-16 15:57:30'),
(7, 'Nguyễn Đức Bỉ', 'avatar_7_1781670309.webp', 'ducbi1@gmail.com', '0925257214', 'Thành phố Hồ Chí Minh', '$2y$10$1wyCA1qkybAEzPPXXDKbou5n6Bwd7hLdbEcRNm6SBERXYRyPtg0MG', 'customer', NULL, NULL, '2026-06-16 15:59:29', '2026-07-03 16:06:22'),
(8, 'Nguyễn Văn A', NULL, 'auser@gmail.com', NULL, NULL, '$2y$10$uc9asxkVbW8t9AAFx9gLj.rdK1.QybqxVFdCDZYhvU3jKHcYMSoCW', 'customer', NULL, NULL, '2026-06-17 04:38:46', '2026-06-17 04:38:46'),
(9, 'Nguyễn Văn B', 'default.jpg', 'buser@gmail.com', '0901234567', 'TPHCM', '$2y$10$jEttTMaF1Hz6V2XUUXa3W.0ZOTlB0XXYl/Kyc7HTVguWNydVqlzDq', 'customer', NULL, NULL, '2026-07-01 19:04:49', '2026-07-01 19:04:49'),
(11, 'Nguyễn Văn C', 'avatar_11_1782999059.jpg', 'cuser@gmail.com', '0925257211', 'TPHCM', '$2y$10$HG80ZQXRpmgVGs3.cpf8PedU/8xLlcxE/H2HnsE0fya.vLhgCasV2', 'customer', NULL, NULL, '2026-07-02 13:14:30', '2026-07-02 14:04:10'),
(15, 'Nguyễn Văn D', '1783263836_cap.jpg', 'duser@gmail.com', '0901234568', 'Thành phố Hồ Chí Minh', '$2y$10$L9BnN/il9znIHEhDH686FOrSawPjXd0jZwQwB8d4iizFok7rQDfLa', 'customer', NULL, NULL, '2026-07-05 15:03:56', '2026-07-05 15:16:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(23, 11, 64, '2026-07-02 13:47:00'),
(24, 11, 63, '2026-07-02 13:47:11'),
(25, 7, 64, '2026-07-05 05:00:04'),
(26, 7, 56, '2026-07-05 05:00:07'),
(27, 7, 47, '2026-07-05 05:00:15');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
