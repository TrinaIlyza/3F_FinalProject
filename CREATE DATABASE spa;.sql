 CREATE DATABASE spa;
use spa;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255),
    role ENUM('customer','therapist','admin') NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

ALTER TABLE users
    AUTO_INCREMENT=1000;

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Prim', 'primusr@gmail.com', '099999999', 'admin');

ALTER TABLE users   //timestamp does not become null
    MODIFY COLUMN created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Zayasha Pastor', 'zpastor@gmail.com', '09150324709', 'therapist');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('James Pajado', 'jdopa@gmail.com', '09307487890', 'customer');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Ivy Noreen Aquino', 'ivyaquino981@gmail.com', '09776878546', 'customer');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Trina Ilyza Sobrepena', 'tids@gmail.com', '09150324708', 'therapist');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Aila Marie Nieva', 'amnieva@gmail.com', '09666666666', 'customer');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Kyra Joey Pastor', 'kjpastor@gmail.com', '09352857910', 'admin');

CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    duration INT,
    price DECIMAL(10,2),
    created_at CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT **
    therapist_id INT **
    service_id INT **
    appointment_id DATE,
    start_time TIME,
    end_time TIME,
    status ENUM('pending', 'confirmed', 'completed', 'canceled'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP   
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT **
    amount DECIMAL(10,2),
    payment_method ENUM('cash', 'credit_card', 'paypal'),
    payment_status ENUM('paid', 'unpaid', 'refunded'),
    transaction_id VARCHAR(10,2),
    payment_date TIMESTAMP
);

CREATE TABLE availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT **
    date DATE,
    start_time TIME,
    end_time TIME
);

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT **
    user_id INT **
    rating INT,
    comment TEXT,
    created_at TIMESTAMP
);

CREATE TABLE promotions (
    promo_id INT AUTO_INCREMENT PRIMARY KEY,
    promo_code VARCHAR(50),
    description TEXT,
    discount_percent DECIMAL(5,2),
    start_date DATE,
    end_date DATE
);