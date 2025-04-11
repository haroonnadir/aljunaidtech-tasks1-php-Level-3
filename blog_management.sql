-- Create Database
CREATE DATABASE IF NOT EXISTS blog_management;
USE blog_management;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'new_user') DEFAULT 'new_user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT(11) NOT NULL,
    status ENUM('pending', 'approved', 'disapproved') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Admin User (Default)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$2y$10$T3yQWqJHh8HkK0DjFQsvoOTq0nYlzTqf2npRpjqExph3G1MGG.fDW', 'admin');

-- Insert Sample Blog Posts
INSERT INTO blog_posts (title, content, user_id, status) VALUES
('First Blog Post', 'This is the content of the first post.', 1, 'approved'),
('Second Blog Post', 'This is another blog post.', 1, 'pending');
