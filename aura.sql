-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2025 at 06:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aura`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_order` (IN `userId` INT, OUT `orderId` INT)   BEGIN
    INSERT INTO orders (user_id) VALUES (userId);
    SET orderId = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_order` (IN `orderId` INT)   BEGIN
    -- Restore product quantities
    UPDATE products
    JOIN order_item ON products.product_id = order_item.product_id
    SET 
        product_quantity = product_quantity + order_item.quantity
    WHERE 
        order_item.order_id = orderId;

    DELETE FROM order_item WHERE order_id = orderId;

    DELETE FROM orders WHERE order_id = orderId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `modify_order` (IN `orderId` INT, IN `productId` INT, IN `newQuantity` INT)   BEGIN
    UPDATE order_item
    SET 
        quantity = newQuantity, 
        price = (SELECT product_price FROM products WHERE product_id = productId)
    WHERE 
        order_id = orderId AND product_id = productId;

    UPDATE orders
    SET 
        total_amount = (SELECT SUM(price * quantity) FROM order_item WHERE order_id = orderId)
    WHERE 
        order_id = orderId;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calculate_total` (`orderId` INT) RETURNS FLOAT DETERMINISTIC BEGIN
    -- Calculate the total price for a given order ID
    RETURN IFNULL(
        (SELECT SUM(price * quantity) FROM order_item WHERE order_id = orderId),
        0
    );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `check_availability` (`productId` INT) RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE stock INT;

    -- Fetch the stock quantity for the product
    SELECT product_quantity INTO stock FROM products WHERE product_id = productId;

    -- Return availability status based on stock
    IF stock > 0 THEN
        RETURN 'Available';
    ELSE
        RETURN 'Unavailable';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 't-shirt');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','not confirmed','confirmed','on delivery','delivered','canceled') DEFAULT 'pending',
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_status`, `creation_date`, `updated_date`) VALUES
(1, 1, 170.00, 'not confirmed', '2024-12-27 16:43:37', '2024-12-27 16:44:07'),
(2, 2, 170.00, 'not confirmed', '2025-01-02 07:54:34', '2025-01-02 07:54:48'),
(3, 1, 170.00, 'not confirmed', '2025-01-03 08:37:51', '2025-01-03 08:38:14'),
(4, 2, 410.00, 'not confirmed', '2025-01-06 21:35:20', '2025-01-06 21:35:24'),
(5, 1, 170.00, 'pending', '2025-01-06 22:50:17', '2025-01-06 22:50:17'),
(677, 1, 100.00, 'pending', '2025-01-06 23:02:19', '2025-01-06 23:02:19'),
(678, 1, 170.00, 'pending', '2025-01-06 23:05:41', '2025-01-06 23:05:41'),
(679, 1, 170.00, 'pending', '2025-01-06 23:10:33', '2025-01-06 23:10:33'),
(680, 1, 170.00, 'pending', '2025-01-07 22:50:06', '2025-01-07 22:50:06'),
(681, 1, 100.00, 'pending', '2025-01-07 22:50:10', '2025-01-07 22:50:10'),
(682, 1, 100.00, 'pending', '2025-01-07 22:50:26', '2025-01-07 22:50:26'),
(683, 2, 170.00, 'pending', '2025-01-07 22:51:09', '2025-01-07 22:51:09'),
(684, 2, 100.00, 'pending', '2025-01-07 22:51:12', '2025-01-07 22:51:12'),
(685, 2, 100.00, 'pending', '2025-01-07 22:51:43', '2025-01-07 22:51:43'),
(686, 2, 0.00, 'pending', '2025-01-07 22:52:18', '2025-01-07 22:52:18'),
(687, 2, 170.00, 'pending', '2025-01-07 22:59:20', '2025-01-07 22:59:20'),
(688, 2, 100.00, 'pending', '2025-01-07 22:59:23', '2025-01-07 22:59:23'),
(689, 2, 0.00, 'pending', '2025-01-07 23:18:15', '2025-01-07 23:18:15'),
(690, 2, 0.00, 'pending', '2025-01-07 23:18:27', '2025-01-07 23:18:27'),
(691, 2, 290.00, 'pending', '2025-01-07 23:19:03', '2025-01-07 23:19:03'),
(692, 2, 290.00, 'pending', '2025-01-07 23:19:07', '2025-01-07 23:19:07'),
(693, 2, 290.00, 'pending', '2025-01-07 23:29:57', '2025-01-07 23:29:57'),
(694, 2, 170.00, 'pending', '2025-01-07 23:30:26', '2025-01-07 23:30:26'),
(695, 2, 170.00, 'pending', '2025-01-07 23:30:30', '2025-01-07 23:30:30'),
(696, 2, 170.00, 'pending', '2025-01-07 23:33:35', '2025-01-07 23:33:35'),
(697, 2, 170.00, 'pending', '2025-01-07 23:33:38', '2025-01-07 23:33:38'),
(698, 2, 170.00, 'pending', '2025-01-07 23:37:57', '2025-01-07 23:37:57'),
(699, 2, 170.00, 'pending', '2025-01-07 23:38:01', '2025-01-07 23:38:01'),
(700, 2, 170.00, 'pending', '2025-01-07 23:40:23', '2025-01-07 23:40:23'),
(701, 2, 170.00, 'pending', '2025-01-07 23:40:26', '2025-01-07 23:40:26'),
(702, 2, 170.00, 'pending', '2025-01-07 23:44:10', '2025-01-07 23:44:10'),
(703, 2, 170.00, 'pending', '2025-01-07 23:44:14', '2025-01-07 23:44:14'),
(704, 2, 170.00, 'pending', '2025-01-07 23:46:37', '2025-01-07 23:46:37'),
(705, 2, 170.00, 'pending', '2025-01-07 23:46:42', '2025-01-07 23:46:42'),
(706, 2, 170.00, 'pending', '2025-01-07 23:48:57', '2025-01-07 23:48:57'),
(707, 2, 170.00, 'pending', '2025-01-07 23:49:05', '2025-01-07 23:49:05'),
(708, 2, 170.00, 'pending', '2025-01-07 23:50:45', '2025-01-07 23:50:45'),
(709, 2, 170.00, 'pending', '2025-01-07 23:50:49', '2025-01-07 23:50:49'),
(710, 2, 170.00, 'pending', '2025-01-07 23:54:39', '2025-01-07 23:54:39'),
(711, 2, 170.00, 'pending', '2025-01-07 23:54:42', '2025-01-07 23:54:42'),
(712, 2, 170.00, 'pending', '2025-01-08 00:02:35', '2025-01-08 00:02:35'),
(713, 2, 170.00, 'pending', '2025-01-08 00:04:07', '2025-01-08 00:04:07'),
(714, 2, 170.00, 'pending', '2025-01-08 00:04:10', '2025-01-08 00:04:10'),
(715, 2, 170.00, 'pending', '2025-01-08 00:07:42', '2025-01-08 00:07:42'),
(716, 2, 100.00, 'confirmed', '2025-01-08 00:07:45', '2025-01-08 00:08:00'),
(717, 2, 170.00, 'pending', '2025-01-08 00:14:28', '2025-01-08 00:14:28'),
(718, 2, 170.00, 'confirmed', '2025-01-08 00:27:31', '2025-01-08 00:28:18'),
(719, 2, 170.00, 'confirmed', '2025-01-08 00:31:00', '2025-01-08 00:31:44'),
(720, 2, 170.00, 'confirmed', '2025-01-08 00:35:04', '2025-01-08 00:36:32'),
(721, 2, 170.00, 'pending', '2025-01-08 09:13:16', '2025-01-08 09:13:16'),
(722, 2, 170.00, 'confirmed', '2025-01-08 09:13:48', '2025-01-08 09:14:13'),
(723, 2, 170.00, 'pending', '2025-01-08 12:06:19', '2025-01-08 12:06:19'),
(724, 3, 410.00, 'confirmed', '2025-01-12 22:28:43', '2025-01-12 22:31:53'),
(725, 3, 290.00, 'confirmed', '2025-01-12 23:30:05', '2025-01-12 23:30:27'),
(726, 3, 170.00, 'confirmed', '2025-01-12 23:31:14', '2025-01-12 23:31:50'),
(727, 3, 170.00, 'confirmed', '2025-01-12 23:37:51', '2025-01-12 23:38:40'),
(728, 3, 170.00, 'confirmed', '2025-01-12 23:42:04', '2025-01-12 23:43:02'),
(729, 2, 170.00, 'pending', '2025-03-18 01:01:25', '2025-03-18 01:01:25');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `before_insert_order` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE user_exists INT;
    SELECT COUNT(*) INTO user_exists FROM user WHERE user_id = NEW.user_id;
    
    IF user_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'User does not exist';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `size`, `color`) VALUES
(1, 1, 4, 1, 120.00, 'XS', 'dark-Green'),
(2, 2, 3, 1, 120.00, 'XS', 'Red'),
(3, 3, 4, 1, 120.00, 'XS', 'dark-Green'),
(4, 4, 3, 2, 120.00, 'L', 'dark-Green'),
(5, 4, 4, 1, 120.00, 'XL', 'Black'),
(6, 5, 8, 1, 120.00, 'XL', 'White'),
(7, 678, 7, 1, 120.00, 'XS', 'Black'),
(8, 679, 3, 1, 120.00, 'XS', 'Red'),
(9, 680, 4, 1, 120.00, 'XS', 'dark-Green'),
(10, 683, 3, 1, 120.00, 'XS', 'Red'),
(11, 687, 4, 1, 120.00, 'XL', 'Black'),
(12, 691, 3, 1, 120.00, 'XL', 'dark-Green'),
(13, 691, 3, 1, 120.00, 'XS', 'Red'),
(14, 694, 4, 1, 120.00, 'XS', 'Black'),
(15, 696, 12, 1, 120.00, 'XS', 'Blue'),
(16, 698, 7, 1, 120.00, 'XS', 'Red'),
(17, 700, 13, 1, 120.00, 'XS', 'Red'),
(18, 702, 9, 1, 120.00, 'XS', 'Blue'),
(19, 704, 12, 1, 120.00, 'XS', 'White'),
(20, 706, 11, 1, 120.00, 'XS', 'Blue'),
(21, 708, 17, 1, 120.00, 'XS', 'Blue'),
(22, 710, 13, 1, 120.00, 'XS', 'White'),
(23, 712, 3, 1, 120.00, 'XS', 'Red'),
(24, 713, 4, 1, 120.00, 'XS', 'dark-Green'),
(25, 715, 9, 1, 120.00, 'XS', 'Blue'),
(26, 717, 3, 1, 120.00, 'XS', 'Red'),
(27, 718, 3, 1, 120.00, 'XS', 'Red'),
(28, 719, 13, 1, 120.00, 'XS', 'Red'),
(29, 720, 3, 1, 120.00, 'XS', 'Red'),
(30, 721, 3, 1, 120.00, 'XS', 'Red'),
(31, 722, 4, 1, 120.00, 'XS', 'dark-Green'),
(32, 723, 3, 1, 120.00, 'XL', 'Red'),
(33, 724, 3, 1, 120.00, 'XL', 'Black'),
(34, 724, 11, 2, 120.00, 'XL', 'Black'),
(35, 725, 3, 1, 120.00, 'XS', 'Red'),
(36, 725, 7, 1, 120.00, 'L', 'Red'),
(37, 726, 13, 1, 120.00, 'XS', 'Red'),
(38, 727, 3, 1, 120.00, 'XS', 'Red'),
(39, 728, 15, 1, 120.00, 'L', 'Blue'),
(40, 729, 4, 1, 120.00, 'XS', 'Black');

--
-- Triggers `order_item`
--
DELIMITER $$
CREATE TRIGGER `before_insert_order_item` BEFORE INSERT ON `order_item` FOR EACH ROW BEGIN
    DECLARE product_stock INT;

    SELECT product_quantity INTO product_stock FROM products WHERE product_id = NEW.product_id;

    IF product_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock';
    ELSE
        
        SET NEW.price = (SELECT product_price FROM products WHERE product_id = NEW.product_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('Cash on Delivery','Card Payment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `main_img_url` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `product_price`, `product_quantity`, `color`, `main_img_url`, `category_id`, `creation_date`) VALUES
(3, 'bimo shirt', 'adventure timw - bimo shirt in different colors', 120.00, 200, 'Red,dark-Green,Black,White', 'imgs/bimo_black_01.gif', 1, '2024-12-18 10:52:52'),
(4, 'breaking bad t-shirt', 'breaking bad walter white t-shirt', 120.00, 200, 'dark-Green,Black,White', 'imgs/heisenbergBlack_green_backgroud_T_Shirts_Front_and_Back_View_Mockup_01.gif', 1, '2024-12-24 14:22:09'),
(5, 'skeleton', 'skeleton t-shirt ', 120.00, 100, 'Red,Blue,dark-Green,Black,White,Purple,light-blue', 'imgs/skeletor-black_01.gif', 1, '2024-12-24 14:22:54'),
(7, 'gojo sataro t-shirt', 'gojo sataro fro jujutsu kaisen', 120.00, 120, 'Red,Blue,Black,Purple,light-blue', 'imgs/gojosataro-black.gif', 1, '2025-01-02 21:00:43'),
(8, 'sons of anarchy', 'sons of anarchy t-shirt', 120.00, 120, 'Red,Blue,dark-Green,Black,White', 'imgs/sons-of-anarchy_01.gif', 1, '2025-01-02 21:02:02'),
(9, 'yujiro hanma t-shirt', 'yujiro hanma t-shirt', 120.00, 111, 'Blue,dark-Green,Black,White', 'imgs/yujiro-hanma-nicke-black.gif', 1, '2025-01-02 21:03:25'),
(10, 'vagabond t-shirt', 'vagabond t-shirt', 120.00, 111, 'Blue,Black,White', 'imgs/vagabond.gif', 1, '2025-01-02 21:05:03'),
(11, 'berserk t-shirt', 'berserk t-shirt', 120.00, 120, 'Blue,dark-Green,Black,White', 'imgs/berserk-WHITE1.gif', 1, '2025-01-02 21:05:45'),
(12, 'lightning t-shirt', 'lightning t-shirt', 120.00, 111, 'Blue,dark-Green,Black,White', 'imgs/that-woman-White_T-Shirt_Mockup.gif', 1, '2025-01-02 21:06:46'),
(13, 'skeletor t-shirt', 'skeletor t-shirt', 120.00, 111, 'Red,Blue,Black,White', 'imgs/skeletor-black_02.gif', 1, '2025-01-02 21:09:11'),
(14, 'breaking bad t-shirt', 'breaking bad heisenberg t-shirt', 120.00, 111, 'Red,dark-Green,Black,White', 'imgs/White_T_Shirts_Front_and_Back_View_Mockup-heisenberg_02.gif', 1, '2025-01-02 21:10:28'),
(15, 'sons of anarchy', 'sons of anarchy', 120.00, 111, 'Blue,dark-Green,Black,White', 'imgs/sons-of-anarchy-white_02.gif', 1, '2025-01-02 21:11:13'),
(16, 'test today', 'jhjghxghw', 150.00, 111, 'dark-Green,Black,White', 'imgs/bimo_black_01.gif', 1, '2025-01-03 08:41:19'),
(17, 'test today', 'rsgdwe', 120.00, 33, 'Blue,dark-Green,Black,White,Purple', 'imgs/berserk-WHITE1.gif', 1, '2025-01-06 21:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_img`
--

CREATE TABLE `product_img` (
  `img_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `img_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_img`
--

INSERT INTO `product_img` (`img_id`, `product_id`, `img_url`) VALUES
(1, 3, 'imgs/bimo_black_01.gif'),
(2, 3, 'imgs/bimo_white.gif'),
(3, 4, 'imgs/heisenbergBlack_green_backgroud_T_Shirts_Front_and_Back_View_Mockup_01.gif'),
(4, 4, 'imgs/heisenbergBlack_green_backgroud_T_Shirts_Front_and_Back_View_Mockup_02.gif'),
(5, 4, 'imgs/White_T_Shirts_Front_and_Back_View_Mockup-heisenberg_01.gif'),
(6, 4, 'imgs/White_T_Shirts_Front_and_Back_View_Mockup-heisenberg_02.gif'),
(7, 5, 'imgs/skeletor-black_01.gif'),
(8, 5, 'imgs/skeletor-black_02.gif'),
(9, 5, 'imgs/skeletor-white_01.gif'),
(10, 5, 'imgs/skeletor-white_02.gif'),
(13, 7, 'imgs/gojosataro-black.gif'),
(14, 8, 'imgs/sons-of-anarchy_01.gif'),
(15, 8, 'imgs/sons-of-anarchy_02.gif'),
(16, 8, 'imgs/sons-of-anarchy-white_01.gif'),
(17, 8, 'imgs/sons-of-anarchy-white_02.gif'),
(18, 9, 'imgs/yujiro-hanma-nicke-black.gif'),
(19, 10, 'imgs/vagabond.gif'),
(20, 11, 'imgs/berserk-WHITE1.gif'),
(21, 12, 'imgs/vagabond.gif'),
(22, 13, 'imgs/skeletor-black_02.gif'),
(23, 14, 'imgs/White_T_Shirts_Front_and_Back_View_Mockup-heisenberg_02.gif'),
(24, 15, 'imgs/sons-of-anarchy-white_02.gif'),
(25, 16, 'imgs/bimo_black_01.gif'),
(26, 16, 'imgs/bimo_white.gif'),
(27, 17, 'imgs/berserk-WHITE1.gif'),
(28, 17, 'imgs/bimo_black_01.gif'),
(29, 17, 'imgs/bimo_white.gif'),
(30, 17, 'imgs/gojosataro-black.gif');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_firstname` varchar(50) NOT NULL,
  `user_lastname` varchar(50) NOT NULL,
  `user_phonenum` varchar(15) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_phonenum`, `user_email`, `user_pass`, `role`, `creation_date`) VALUES
(1, 'ahmed', 'mohamed', '0620401166', 'mohamed@gmail.com', '345', 'user', '2024-12-18 10:32:30'),
(2, 'mohamed', 'elanmbri', '0620441166', 'mohamedelanmbri@gmail.com', 'uchiha12.', 'admin', '2024-12-18 10:42:01'),
(3, 'mohamed', 'elanmbri', '0611106159', 'elanmbri.mohamed0@gmail.com', 'uchiha123.', 'user', '2025-01-12 22:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_adress`
--

CREATE TABLE `user_adress` (
  `user_adress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_adress` text NOT NULL,
  `first_lastname` varchar(100) NOT NULL,
  `user_neighborhood` varchar(100) NOT NULL,
  `user_city` varchar(100) NOT NULL,
  `user_region` varchar(100) NOT NULL,
  `user_zipcode` varchar(20) NOT NULL,
  `user_phonenum` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_adress`
--

INSERT INTO `user_adress` (`user_adress_id`, `user_id`, `user_adress`, `first_lastname`, `user_neighborhood`, `user_city`, `user_region`, `user_zipcode`, `user_phonenum`) VALUES
(1, 1, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(2, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(3, 1, 'tamesna', 'mohamed elanmbri', 'nour', 'rabat', 'rabat', '12200', '000000'),
(4, 2, 'tamesna', 'mohamed elanmbri', 'nour', 'tamesna', 'rabat', '123', '3332244'),
(5, 1, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(6, 1, 'edewefrg', 'test price', 'nour', 'rabat', 'rabat', '123', '3332244'),
(7, 1, 'edewefrg', 'mohamed elanmbri ya rbi tkhdm', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(8, 1, 'edewefrg', 'test', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(9, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(10, 2, 'Ddcyu', 'Gyrsd', 'Cchhrd', 'Ccbh', 'Dduh', '5554', '4577'),
(11, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'rabat', '123', '3332244'),
(12, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'weqwf', '123', '3332244'),
(13, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'weqwf', '123', '3332244'),
(14, 2, 'edewefrg', 'mohamed elanmbri', 'nour', 'rabat', 'weqwf', '123', '3332244'),
(15, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(16, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'weqwf', '123', '3332244'),
(17, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(18, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(19, 2, 'eegr', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(20, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(21, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(22, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'rabat', '123', '3332244'),
(23, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(24, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'rabat', '123', '3332244'),
(25, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'rabat', '123', '3332244'),
(26, 2, 'edewefrg', 'test price', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(27, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(28, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'rabat', 'weqwf', '123', '3332244'),
(29, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'qefgeqwaa', 'weqwf', '123', '3332244'),
(30, 2, 'edewefrg', 'mohamed elanmbri', 'qqsd', 'tamesna', 'rabat', '123', '3332244'),
(31, 3, 'Tamesna', 'Asmae elanmbri', 'Nour2', 'Tamesna', 'Rabat', '10100', '0620441166'),
(32, 3, 'Tamesna', 'Hamza', 'Doha', 'Sidi Yahya Zaer', 'Rabat', '10105', '0606441060'),
(33, 3, 'The', 'Elanmbri', 'The', 'Sidi Yahya Zaer', 'Tanger', '10105', '6627282642'),
(34, 3, 'The', 'Elanmbri', 'The only', 'Rabat', 'Rabat', '10105', '6774699'),
(35, 3, 'I’m going', 'Elanmbri', 'I’m not going', 'Rabat', 'Rabat', '10105', '74477329'),
(36, 2, 'edewefrg', 'mohamed elanmbri', 'nour', 'qefgeqwaa', 'weqwf', '123', '3332244');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_img`
--
ALTER TABLE `product_img`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_email_2` (`user_email`);

--
-- Indexes for table `user_adress`
--
ALTER TABLE `user_adress`
  ADD PRIMARY KEY (`user_adress_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=730;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_img`
--
ALTER TABLE `product_img`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_adress`
--
ALTER TABLE `user_adress`
  MODIFY `user_adress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_img`
--
ALTER TABLE `product_img`
  ADD CONSTRAINT `product_img_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_adress`
--
ALTER TABLE `user_adress`
  ADD CONSTRAINT `user_adress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
