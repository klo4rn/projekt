
CREATE TABLE konto_uzytkownika (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    adres VARCHAR(255),
    data_urodzenia DATE,
    id_pytania_pomocniczego INT,
    odpowiedz VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE product_categories (
    product_id INT,
    category_id INT,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE product_parameters (
    product_id INT,
    parameter_id INT,
    value VARCHAR(100) NOT NULL,
    PRIMARY KEY (product_id, parameter_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (parameter_id) REFERENCES parameters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status ENUM('Nowe', 'W realizacji', 'Zakończone') DEFAULT 'Nowe',
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES konto_uzytkownika(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE shipping_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    cost DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE order_items (
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE order_shipping (
    order_id INT PRIMARY KEY,
    shipping_method_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE order_payment (
    order_id INT PRIMARY KEY,
    payment_method_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO konto_uzytkownika (login, haslo, email, adres, data_urodzenia, id_pytania_pomocniczego, odpowiedz)
VALUES 
('user1', 'haslohaslo', 'aa@xd.pl', 'ul. Kwiatowa 10, Warszawa', '1990-05-12', 1, 'aaa'),
('dawidjasper', 'Dawidkamilwojcikxd', 'dawidkamiljasper@gmail.com', 'ul. Warszawska 28, Pabianice', '1985-08-20', 2, 'xdxdxd');

INSERT INTO categories (name)
VALUES 
('Elektronika'),
('Moda'),
('Dom i ogród');

INSERT INTO products (name, description, price, stock, image)
VALUES 
('Trapfon', 'Trapfon z dużym ekranem', 1500.00, 10, 'trapfon.jpg'),
('Laptop', 'Laptop do pracy i rozrywki', 3500.00, 5, 'laptop.jpg'),
('Bluza z kapturem', 'Bluza z kapturem', 120.00, 25, 'bluza.jpg');

INSERT INTO product_categories (product_id, category_id)
VALUES 
(1, 1),  
(2, 1),  
(3, 2);  

INSERT INTO parameters (name)
VALUES 
('Kolor'),
('Rozmiar'),
('Pojemność');

INSERT INTO product_parameters (product_id, parameter_id, value)
VALUES 
(1, 1, 'Czarny'),    
(2, 3, '512GB'),      
(3, 2, 'L');          

INSERT INTO orders (user_id, status, total_price)
VALUES 
(1, 'Nowe', 1500.00),  
(NULL, 'W realizacji', 120.00); 

INSERT INTO order_items (order_id, product_id, quantity, price)
VALUES 
(1, 1, 1, 1500.00),  
(2, 3, 2, 120.00);   

INSERT INTO shipping_methods (name, cost)
VALUES 
('Kurier', 15.00),
('Paczkomat', 10.00),
('Odbiór osobisty', 0.00);

INSERT INTO payment_methods (name)
VALUES 
('Przelew bankowy'),
('Karta kredytowa'),
('Płatność przy odbiorze');

INSERT INTO order_shipping (order_id, shipping_method_id)
VALUES 
(1, 1),  
(2, 2);  

INSERT INTO order_payment (order_id, payment_method_id)
VALUES 
(1, 2),  
(2, 3);  

