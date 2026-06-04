CREATE DATABASE IF NOT EXISTS cats_db;
USE cats_db;

CREATE TABLE IF NOT EXISTS cat_myths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL
);

INSERT INTO cat_myths (title, description, image_path) VALUES 
('Deities in Egypt', 'In Ancient Egypt, black cats were revered as sacred creatures, symbolizing protection, grace, and good luck.', 'images/cat1.jpg'),
('Symbol of Happiness', 'In Great Britain, Ireland, and Japan, it is believed that if a black cat crosses your path, it actually brings immense good luck.', 'images/cat2.jpg'),
('Strong Health', 'Studies show that the genetic mutations that cause their coats to be black actually provide them with a stronger immune system.', 'images/cat3.jpg');
