-- ============================================================
-- StrideOn - Base de Dados Completa
-- Desenvolvido por: Eng. Software Malvin Manguele
-- ============================================================

CREATE DATABASE IF NOT EXISTS strideon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE strideon;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(80),
    image VARCHAR(255),
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela do carrinho
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Inserir produtos de exemplo
INSERT INTO products (name, description, price, category, image, stock, featured) VALUES
('Air Phantom X1', 'Tênis de alta performance com sola de borracha premium e cabedal em malha respirável. Edição limitada streetwear.', 4500.00, 'Sneakers', 'shoe1.jpg', 15, 1),
('Urban Force Pro', 'Design robusto e moderno para o dia a dia urbano. Estrutura em couro sintético com detalhes metálicos.', 3800.00, 'Lifestyle', 'shoe2.jpg', 20, 1),
('Shadow Glide Elite', 'Silhueta minimalista com amortecimento avançado. A escolha dos runners urbanos.', 5200.00, 'Running', 'shoe3.jpg', 10, 1),
('Neon Street Racer', 'Inspirado nas pistas de corrida. Visual agressivo com detalhes em vermelho intenso.', 4100.00, 'Sport', 'shoe4.jpg', 18, 1),
('Classic Heritage Low', 'Um clássico reinventado com materiais premium. Versatilidade entre o casual e o formal.', 2900.00, 'Classic', 'shoe5.jpg', 25, 0),
('Apex Trainer V2', 'Projetado para máxima performance em treinos intensos. Suporte total e estilo inigualável.', 4750.00, 'Training', 'shoe6.jpg', 12, 0),
('Midnight Luxe', 'Elegância noturna em cada passo. Acabamento premium com detalhes dourados.', 6000.00, 'Luxury', 'shoe7.jpg', 8, 1),
('Coast Runner', 'Leveza e conforto para longas caminhadas. Design costeiro e descontraído.', 3200.00, 'Casual', 'shoe8.jpg', 30, 0);

-- Inserir admin padrão (senha: Admin@123)
INSERT INTO users (name, email, password, role) VALUES
('Admin StrideOn', 'admin@strideon.co.mz', '$2y$12$LJomHBgDQ1qz3QLz0dRFtO6fL0LNzfS4XLdS5Fo7kGAy5wuFJrXFi', 'admin');
