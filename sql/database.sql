-- Create a database
CREATE DATABASE IF NOT EXISTS database_1;

-- Use the database
USE database_1;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  user_img VARCHAR(255) DEFAULT NULL,
  user_role VARCHAR(50) NOT NULL DEFAULT 'user'
);

-- Table for events
CREATE TABLE IF NOT EXISTS events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(255) NOT NULL,
  event_date DATE NOT NULL,
  event_type ENUM('conférence', 'forum', 'formation', 'voyage') NOT NULL,
  event_details TEXT,
  event_img VARCHAR(255),
  event_latitude DECIMAL(10, 8) DEFAULT NULL,
  event_longitude DECIMAL(11, 8) DEFAULT NULL,
  organizer_id INT,
  FOREIGN KEY (organizer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for event registrations
CREATE TABLE IF NOT EXISTS registrations (
  registration_id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  user_id INT,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert some test data
INSERT INTO users (username, email, password, user_img, user_role)
VALUES ('Club EIC', 'eic@gmail.com', 'secret', 'images/EIC.jpg', 'admin');

INSERT INTO events (organizer_id, event_name, event_date, event_details, event_img, event_latitude, event_longitude, event_type)
VALUES (1, 'Laravel framework', '2023-12-30', 'Laravel is the most popular framework in php.', 'images/laravel.jpg', 34.25257320, -6.53506217, 'formation');
