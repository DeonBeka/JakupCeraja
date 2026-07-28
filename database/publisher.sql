CREATE DATABASE IF NOT EXISTS jakup_ceraja
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE jakup_ceraja;

-- =========================
-- CATEGORIES
-- =========================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- AUTHORS
-- =========================

CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    photo VARCHAR(255),
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- BOOKS
-- =========================

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_id INT,
    category_id INT,
    cover VARCHAR(255),
    description TEXT,
    publish_date DATE,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (author_id) REFERENCES authors(id)
    ON DELETE SET NULL,

    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE SET NULL
);

-- =========================
-- NEWS
-- =========================

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- GALLERY
-- =========================

CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- CONTACT MESSAGES
-- =========================

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255),
    email VARCHAR(255),
    subject VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- ADMIN USERS
-- =========================

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255),
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- SETTINGS
-- =========================

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_name VARCHAR(255),
    logo VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(100),
    address VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255)
);

-- =========================
-- DEFAULT CATEGORIES
-- =========================

INSERT INTO categories (name, icon) VALUES
('Roman','book'),
('Poezi','feather'),
('Përkthime','globe'),
('Libra për Fëmijë','child'),
('Histori','landmark'),
('Ese','pen');