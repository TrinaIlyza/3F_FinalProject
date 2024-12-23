CREATE DATABASE finalproj;
use finalproj;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','therapist','admin') NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE users
    AUTO_INCREMENT=1000;

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Prim', 'primusr@gmail.com', '099999999', 'admin');

ALTER TABLE users   
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
VALUES ('Chubby', 'chubby@gmail.com', '09776878546', 'admin');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Shaggy', 'shaggy@gmail.com', '09778901456', 'therapist');

INSERT INTO users (full_name, email, phone_number, role)
VALUES ('Jupiter S.', 'jupiterss@gmail.com', '09999999999', 'therapist');

CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    duration INT,
    price DECIMAL(10,2),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO services (service_name, description, duration, price)
VALUES ('Relaxation Massage', 'A full-body massage designed to relieve tension, reduce stress, and promote relaxation. Using a blend of soothing oils and gentle techniques, this massage targets key areas like the neck, shoulders, and lower back for an overall sense of well-being.', '60', '1000');

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    therapist_id INT NOT NULL,
    FOREIGN KEY (therapist_id) REFERENCES users(user_id),
    service_id INT NOT NULL,
    FOREIGN KEY (service_id) REFERENCES services(service_id),
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'canceled'),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id),
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'paypal') NOT NULL,
    payment_status ENUM('paid', 'unpaid', 'refunded') NOT NULL DEFAULT 'unpaid',
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    FOREIGN KEY (therapist_id) REFERENCES users(user_id),
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL
);

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE promotions (
    promo_id INT AUTO_INCREMENT PRIMARY KEY,
    promo_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    discount_percent DECIMAL(5,2) NOT NULL CHECK (discount_percent BETWEEN 0 AND 100),
    start_date DATE,
    end_date DATE
);