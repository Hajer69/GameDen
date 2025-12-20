-- ============================================
-- CREATE TABLES 
-- ============================================

USE gamedendb;

-- Create games table 
CREATE TABLE games (
    game_name VARCHAR(100) PRIMARY KEY,
    category VARCHAR(50),
    difficulty VARCHAR(20),
    players VARCHAR(50),
    rating DECIMAL(3,2)    
);

-- Game categories table
CREATE TABLE game_categories (
    category_name VARCHAR(40) PRIMARY KEY,
    game_count INT(4),
    Description TEXT,
    skills_developed VARCHAR(100)
    
);

-- Game users table
CREATE TABLE users (
    username VARCHAR(40) PRIMARY KEY,
    password VARCHAR(40),
    email VARCHAR(100),
    fullName VARCHAR(100),
    favoriteGame VARCHAR(100)
);



