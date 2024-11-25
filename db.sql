-- Create the database if not exists
CREATE DATABASE IF NOT EXISTS `exam_management_system`;

USE `exam_management_system`;

-- Create the institutions table
CREATE TABLE IF NOT EXISTS `institutions` (
    `institution_id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `institution_name` VARCHAR(100) NOT NULL,
    `location` VARCHAR(255),
    `contact` VARCHAR(15),
    `email` VARCHAR(100),
    `website` VARCHAR(255)
);

-- Create the users table with relation to institutions
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `contact_number` VARCHAR(15) NOT NULL,
    `institution_id` VARCHAR(50) NOT NULL,
    `institution_name` VARCHAR(100),  -- Institution name (for students and admins)
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`institution_id`)
);

-- Create the exams table
CREATE TABLE IF NOT EXISTS `exams` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_name` VARCHAR(100) NOT NULL,
    `exam_date` DATE NOT NULL,
    `institution_id` VARCHAR(50) NOT NULL,
    FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`institution_id`)
);

-- Create the questions table
CREATE TABLE IF NOT EXISTS `questions` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT NOT NULL,
    `question_text` TEXT NOT NULL,
    `option_a` VARCHAR(255) NOT NULL,
    `option_b` VARCHAR(255) NOT NULL,
    `option_c` VARCHAR(255) NOT NULL,
    `option_d` VARCHAR(255) NOT NULL,
    `correct_answer` ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`)
);

-- Create the student_exams table for assigning exams to students
CREATE TABLE IF NOT EXISTS `student_exams` (
    `student_id` INT NOT NULL,
    `exam_id` INT NOT NULL,
    PRIMARY KEY (`student_id`, `exam_id`),
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`)
);

CREATE TABLE exam_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    attended_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (exam_id) REFERENCES exams(id)
);
CREATE TABLE exam_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (exam_id) REFERENCES exams(id)
);
