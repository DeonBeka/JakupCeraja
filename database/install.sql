-- =====================================================================
-- Shtëpia Botuese "Jakup Ceraja"  –  full database schema
-- Run this once on an EMPTY database (local MySQL / TiDB Cloud).
-- It does not create the database itself, so select it first.
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    photo VARCHAR(255),
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    author_id INT NULL,
    category_id INT NULL,
    cover VARCHAR(255),
    description TEXT,
    publish_date DATE NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_books_author (author_id),
    KEY idx_books_category (category_id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    image VARCHAR(255),
    excerpt TEXT,
    category VARCHAR(100),
    published_at DATE NULL,
    featured TINYINT(1) DEFAULT 0,
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(100),
    subject VARCHAR(255),
    message TEXT,
    status VARCHAR(20) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255),
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_name VARCHAR(255),
    logo VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(100),
    address VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtitle VARCHAR(255),
    title VARCHAR(255),
    description TEXT,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- One row per order (a customer checkout)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(20) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(100) NOT NULL,
    country VARCHAR(100),
    city VARCHAR(100),
    address VARCHAR(255),
    note TEXT,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    shipping_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_order_code (order_code)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- The books inside each order (title/price copied so history stays correct)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NULL,
    book_title VARCHAR(255) NOT NULL,
    book_cover VARCHAR(255),
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    KEY idx_items_order (order_id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- PHP sessions (needed on Vercel, where servers don't share files)
CREATE TABLE IF NOT EXISTS php_sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    data MEDIUMTEXT NOT NULL,
    updated_at INT NOT NULL,
    KEY idx_sessions_updated (updated_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Starting data
-- ---------------------------------------------------------------------

INSERT INTO categories (name, icon) VALUES
('Histori', 'landmark'),
('Poezi', 'feather'),
('Përkthime', 'globe'),
('Ese', 'pen'),
('Roman', 'book'),
('Psikologji', 'brain');

INSERT INTO settings (website_name, email, phone, address, facebook, instagram) VALUES
('Shtëpia Botuese "Jakup Ceraja"', 'info@jakupceraja.com', '+383 XX XXX XXX', 'Kosovë', '#', '#');

INSERT INTO hero_slides (subtitle, title, description, button_text, button_link, image, is_active, sort_order) VALUES
('Shtëpia Botuese "Jakup Ceraja"', 'Libra që ruajnë kulturën dhe frymëzojnë breza.', 'Shtëpia Botuese "Jakup Ceraja" sjell botime cilësore, autorë shqiptarë dhe përkthime nga letërsia botërore.', 'Shfleto Librat', 'books.php', 'hero-1.jpg', 1, 1),
('Botimet e Reja', 'Zbuloni botimet më të reja.', 'Romane, poezi, histori dhe përkthime të përzgjedhura me kujdes.', 'Botimet e Reja', 'books.php', 'hero-2.jpg', 1, 2),
('Porosit Online', 'Porositni librat tuaj nga shtëpia.', 'Shtoni librat në shportë dhe ne jua dërgojmë në adresën tuaj.', 'Shiko Librat', 'books.php', 'hero-3.jpg', 1, 3);

-- No admin account is created here: the first visit to /admin/login.php
-- asks you to create one.
