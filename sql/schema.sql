-- Schema Database Admin Panel My Profile (AICoding Dark Theme Design)

CREATE DATABASE IF NOT EXISTS my_profile_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_profile_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    description TEXT,
    type ENUM('blog', 'project') NOT NULL DEFAULT 'blog',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tags Table
CREATE TABLE IF NOT EXISTS tags (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    type ENUM('blog', 'project') NOT NULL DEFAULT 'blog',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Blogs Table
CREATE TABLE IF NOT EXISTS blogs (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(255),
    category_id CHAR(36),
    status ENUM('draft', 'published') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Blog Tags Pivot
CREATE TABLE IF NOT EXISTS blog_tags (
    blog_id CHAR(36),
    tag_id CHAR(36),
    PRIMARY KEY (blog_id, tag_id),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    project_url VARCHAR(255),
    category_id CHAR(36),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Project Tags Pivot
CREATE TABLE IF NOT EXISTS project_tags (
    project_id CHAR(36),
    tag_id CHAR(36),
    PRIMARY KEY (project_id, tag_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Default Admin User
-- Email: gaganbaonkk@gmail.com
-- Password: admin (Pre-hashed with PASSWORD_BCRYPT: $2y$10$7vIsk.eWfH/c8wH0V7Z.2O6/I.jU99V2Z6D5B4gI0S86I0b88W8f.)
-- You can change the password on first login
INSERT INTO users (id, name, email, phone, password_hash)
VALUES ('3ebdf237-779d-4cb0-a541-e12eb7e5ac55', 'Gagan Suganda', 'gaganbaonkk@gmail.com', '089664044727', '$2y$10$7vIsk.eWfH/c8wH0V7Z.2O6/I.jU99V2Z6D5B4gI0S86I0b88W8f.')
ON DUPLICATE KEY UPDATE id=id;
