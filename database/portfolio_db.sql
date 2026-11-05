-- Dynamic Portfolio Database
CREATE DATABASE IF NOT EXISTS portfolio_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- ============ USERS ============
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  profile_image VARCHAR(255) DEFAULT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_role (role),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============ CATEGORIES ============
CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ============ PROJECTS ============
CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description LONGTEXT NOT NULL,
  thumbnail VARCHAR(255) DEFAULT NULL,
  category_id INT UNSIGNED DEFAULT NULL,
  technologies VARCHAR(500) DEFAULT NULL,
  project_url VARCHAR(255) DEFAULT NULL,
  source_code_url VARCHAR(255) DEFAULT NULL,
  project_date DATE DEFAULT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  views INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_status (status),
  INDEX idx_featured (is_featured),
  INDEX idx_category (category_id),
  INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ============ PROJECT IMAGES ============
CREATE TABLE project_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(200) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  INDEX idx_project (project_id)
) ENGINE=InnoDB;

-- ============ SERVICES ============
CREATE TABLE services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  slug VARCHAR(170) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  icon VARCHAR(80) DEFAULT 'bi bi-briefcase',
  starting_price DECIMAL(10,2) DEFAULT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============ FAVORITES ============
CREATE TABLE favorites (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  project_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_project (user_id, project_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============ PROJECT INQUIRIES ============
CREATE TABLE project_inquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED DEFAULT NULL,
  subject VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  budget DECIMAL(10,2) DEFAULT NULL,
  deadline DATE DEFAULT NULL,
  status ENUM('pending','in_review','approved','in_progress','completed','rejected')
    NOT NULL DEFAULT 'pending',
  admin_response TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
  INDEX idx_user (user_id),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============ CONTACT MESSAGES ============
CREATE TABLE contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('unread','read','archived') NOT NULL DEFAULT 'unread',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============ NOTIFICATIONS ============
CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  link VARCHAR(255) DEFAULT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB;

-- ============ SITE SETTINGS ============
CREATE TABLE site_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value LONGTEXT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============ EXPERIENCE ============
CREATE TABLE experience (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  company VARCHAR(150) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE DEFAULT NULL,
  description TEXT,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ============ EDUCATION ============
CREATE TABLE education (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  degree VARCHAR(150) NOT NULL,
  institution VARCHAR(150) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE DEFAULT NULL,
  description TEXT,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ============ SKILLS ============
CREATE TABLE skills (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  proficiency TINYINT UNSIGNED NOT NULL DEFAULT 80,
  category VARCHAR(80) DEFAULT 'General',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ============ TESTIMONIALS ============
CREATE TABLE testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(100) NOT NULL,
  client_title VARCHAR(150) DEFAULT NULL,
  content TEXT NOT NULL,
  rating TINYINT UNSIGNED DEFAULT 5,
  status ENUM('published','hidden') DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============ PASSWORD RESETS ============
CREATE TABLE password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_token (token_hash),
  INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- ============ AUDIT LOGS ============
CREATE TABLE audit_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  action VARCHAR(100) NOT NULL,
  description TEXT,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============ SAMPLE DATA ============
-- Default admin password: Admin@12345 (CHANGE AFTER LOGIN)
INSERT INTO users (name, email, password, role, status) VALUES
('Site Administrator', 'admin@example.com',
 '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1Hc4Fxkq4dEHdHc9wV.6yDlLQ6i7wJi', 'admin', 'active');

INSERT INTO categories (name, slug, description) VALUES
('Web Development','web-development','Full-stack web applications'),
('UI/UX Design','ui-ux-design','User interface and experience design'),
('Branding','branding','Logos and brand identity');

INSERT INTO projects
(title, slug, description, category_id, technologies, is_featured, status, project_date) VALUES
('E-Commerce Platform','ecommerce-platform',
 'A full-featured e-commerce platform with cart, checkout, and admin panel.',
 1, 'PHP, MySQL, Bootstrap 5, JavaScript', 1, 'published', '2024-06-15'),
('SaaS Dashboard UI','saas-dashboard-ui',
 'A modern SaaS analytics dashboard with dark mode and data visualizations.',
 2, 'Figma, HTML, CSS, Chart.js', 1, 'published', '2024-09-01'),
('Startup Brand Identity','startup-brand-identity',
 'Complete branding package including logo, colors, and typography.',
 3, 'Illustrator, Photoshop', 0, 'published', '2023-12-10');

INSERT INTO services (title, slug, description, icon, starting_price, status) VALUES
('Web Development','web-development','Custom PHP/MySQL websites and web apps.','bi bi-code-slash',499.00,'published'),
('UI/UX Design','ui-ux-design','Modern, user-focused interface design.','bi bi-palette',299.00,'published'),
('Branding','branding','Logos, brand identity, and guidelines.','bi bi-award',199.00,'published');

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title','Portfolio | Full-Stack Developer'),
('site_description','Professional portfolio showcasing projects, services, and skills.'),
('owner_name','Alex Dev'),
('owner_title','Full-Stack PHP Developer'),
('owner_bio','I build fast, secure, and beautiful web applications with PHP, MySQL, and modern frontend tools.'),
('owner_email','hello@example.com'),
('owner_phone','+1 555 0100'),
('social_github','https://github.com/'),
('social_linkedin','https://linkedin.com/'),
('social_twitter',''),
('footer_text','© 2025 Alex Dev. All rights reserved.'),
('site_logo',''),
('site_favicon','');

INSERT INTO skills (name, proficiency, category) VALUES
('PHP',95,'Backend'),('MySQL',90,'Backend'),('JavaScript',85,'Frontend'),
('Bootstrap 5',90,'Frontend'),('UI/UX Design',80,'Design');