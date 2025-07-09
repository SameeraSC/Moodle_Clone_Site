
-- Create the database
CREATE DATABASE IF NOT EXISTS bitlessons;
USE bitlessons;

-- Table: modules
CREATE TABLE IF NOT EXISTS modules (
  module_code VARCHAR(10) NOT NULL PRIMARY KEY,
  module_name VARCHAR(100) DEFAULT NULL,
  Year INT(10) NOT NULL,
  sem INT(10) NOT NULL,
  image_path VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: lessons
CREATE TABLE IF NOT EXISTS lessons (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  module_code VARCHAR(10),
  lesson_number VARCHAR(10),
  week INT(11)NOT NULL,
  title VARCHAR(255),
  file_path VARCHAR(255),
  file_type VARCHAR(50),
  FOREIGN KEY (module_code) REFERENCES modules(module_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
