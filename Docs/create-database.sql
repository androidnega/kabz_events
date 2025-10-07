-- KABZS EVENT Database Creation Script
-- Run this in MySQL command line or phpMyAdmin

-- Create the database
CREATE DATABASE IF NOT EXISTS event_management_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verify database was created
SHOW DATABASES LIKE 'event_management_db';

-- Use the database
USE event_management_db;

-- Show status
SELECT 'Database event_management_db created successfully!' AS Status;

