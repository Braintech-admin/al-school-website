-- ============================================================
-- A.L. Convent School — Complete Database Setup (v4 FINAL)
-- Includes: settings+logo+site_status+cta, pages CMS, hero slider,
--           albums+gallery, news+popup, stats, programs, facilities,
--           messages (flexible), enquiries, notices,
--           role-based admins (super/school) + forgot-password columns
-- Import: phpMyAdmin > Import > database.sql
-- ⚠️ Import ke baad password set karo — 2 tarike:
--    (a) CLI (local):   php reset-cli.php Admin@123
--    (b) phpMyAdmin:    local pe hash banao → UPDATE admins SQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS alschool_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alschool_db;

-- ============ 1. SITE SETTINGS ============
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(150) DEFAULT 'A.L. Convent School & Girls'' College',
    tagline VARCHAR(100) DEFAULT 'Nurture • Learn • Lead • Serve',
    logo VARCHAR(255) DEFAULT 'uploads/logo.png',
    address VARCHAR(255) DEFAULT 'Prayagraj, Uttar Pradesh',
    phone VARCHAR(50) DEFAULT '+91-XXXXXXXXXX',
    email VARCHAR(100) DEFAULT 'info@allahabadconventschool.in',
    timing VARCHAR(100) DEFAULT 'Mon - Sat: 8:00 AM - 4:00 PM',
    facebook VARCHAR(255) DEFAULT '#',
    instagram VARCHAR(255) DEFAULT '#',
    youtube VARCHAR(255) DEFAULT '#',
    cta_title VARCHAR(150) DEFAULT 'Admissions Open for 2026-27',
    cta_text VARCHAR(255) DEFAULT 'Give your child the gift of quality education at A.L. Convent School & Girls'' College.',
    site_status TINYINT(1) DEFAULT 1
);

-- ============ 2. STATS BAR ============
CREATE TABLE stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'fa-graduation-cap',
    number VARCHAR(20) NOT NULL,
    label VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

-- ============ 3. ABOUT SECTION (homepage) ============
CREATE TABLE about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'A Legacy of Learning and Character Building',
    description TEXT,
    image VARCHAR(255) DEFAULT 'uploads/about.jpg'
);

-- ============ 4. ACADEMIC PROGRAMS ============
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'fa-book-open',
    title VARCHAR(100) NOT NULL,
    classes VARCHAR(100),
    description TEXT,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1
);

-- ============ 5. FACILITIES ============
CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

-- ============ 6. NEWS & EVENTS (show_popup = website open popup) ============
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    image VARCHAR(255),
    status TINYINT(1) DEFAULT 1,
    show_popup TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ 7. ALBUMS (gallery groups) ============
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cover_image VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1
);

-- ============ 8. GALLERY (photos — album ke andar) ============
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT DEFAULT 1,
    title VARCHAR(150),
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0
);

-- ============ 9. CMS PAGES (About/Academics/Admissions/Administration) ============
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    page_title VARCHAR(255) NOT NULL,
    page_subtitle VARCHAR(255),
    content LONGTEXT,
    banner_image VARCHAR(255) DEFAULT 'uploads/hero.jpg'
);

-- ============ 10. MISSION / VISION / VALUES ============
CREATE TABLE mission_vision (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'fa-bullseye',
    title VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0
);

-- ============ 11. HERO SLIDER ============
CREATE TABLE hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    small_text VARCHAR(100) DEFAULT 'WELCOME TO',
    title_line1 VARCHAR(255) NOT NULL,
    title_line2 VARCHAR(255),
    subtitle TEXT,
    btn1_text VARCHAR(100) DEFAULT 'Admissions Open',
    btn1_link VARCHAR(255) DEFAULT 'page.php?slug=admissions',
    btn2_text VARCHAR(100) DEFAULT 'Learn More',
    btn2_link VARCHAR(255) DEFAULT 'page.php?slug=about',
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1
);

-- ============ 12. MESSAGES (Manager/Principal/Director — flexible) ============
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation VARCHAR(100) NOT NULL,
    name VARCHAR(100) DEFAULT '',
    photo VARCHAR(255) DEFAULT '',
    message TEXT,
    show_on_home TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1
);

-- ============ 13. ENQUIRIES (contact form) ============
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ 14. NOTICES (Super Admin → School Admin) ============
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ 15. ADMIN USERS (role: super/school + forgot password) ============
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) DEFAULT '',
    email VARCHAR(150) DEFAULT NULL,
    role VARCHAR(20) DEFAULT 'school',
    status TINYINT(1) DEFAULT 1,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    notices_seen_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

INSERT INTO settings () VALUES ((1));  -- defaults ke saath 1 row

INSERT INTO stats (icon, number, label, sort_order) VALUES
('fa-graduation-cap', '2002', 'Year of Establishment', 1),
('fa-users', '2000+', 'Students Empowered', 2),
('fa-user-tie', '80+', 'Experienced Faculty', 3),
('fa-trophy', '100%', 'Commitment to Excellence', 4);

INSERT INTO about (title, description, image) VALUES
('A Legacy of Learning and Character Building',
'Allahabad Convent School & Girls'' College, established in 2002, is committed to providing quality education in a safe, supportive and inspiring environment. We focus on academic excellence, moral values and all-round development to prepare our students for a brighter tomorrow.',
'uploads/about.jpg');

INSERT INTO programs (icon, title, classes, description, sort_order) VALUES
('fa-book-open', 'Primary Section', 'Classes I – V', 'Strong foundation for lifelong learning.', 1),
('fa-users', 'Junior High School', 'Classes VI – VIII', 'Academic growth with values and discipline.', 2),
('fa-user-graduate', 'High School', 'Classes IX – X', 'Preparing for a successful future.', 3),
('fa-school', 'Intermediate (Girls'' College)', 'Classes XI – XII', 'Higher education for brighter opportunities.', 4);

INSERT INTO facilities (icon, title, sort_order) VALUES
('fa-chalkboard', 'Spacious Classrooms', 1),
('fa-flask', 'Well Equipped Science Labs', 2),
('fa-desktop', 'Computer Lab', 3),
('fa-book', 'Library', 4),
('fa-basketball-ball', 'Sports & Games', 5),
('fa-music', 'Cultural Activities', 6),
('fa-shield-alt', 'Safe Campus with CCTV', 7),
('fa-bus', 'Transport Facility', 8);

INSERT INTO news (title, description, event_date, show_popup) VALUES
('Admissions Open for 2026-27', 'Applications are now open for all classes.', '2026-09-15', 1),
('Annual Sports Day', 'Join us for an exciting day of sports and talent.', '2026-09-10', 0),
('Teachers'' Day Celebration', 'A special event to honor our beloved teachers.', '2026-09-05', 0);

INSERT INTO albums (name, cover_image, sort_order) VALUES
('School Moments', '', 1);

INSERT INTO gallery (album_id, title, image, sort_order) VALUES
(1, 'School Building', 'uploads/gallery/school-building.jpg', 1),
(1, 'Classroom Learning', 'uploads/gallery/classroom.jpg', 2),
(1, 'Annual Function', 'uploads/gallery/annual-function.jpg', 3),
(1, 'Sports Day', 'uploads/gallery/sports.jpg', 4),
(1, 'Computer Lab', 'uploads/gallery/computer-lab.jpg', 5),
(1, 'Award Ceremony', 'uploads/gallery/awards.jpg', 6);

INSERT INTO pages (slug, page_title, page_subtitle, content, banner_image) VALUES
('about', 'About Our School', 'A Legacy of Learning and Character Building',
'<h3>Our Story</h3><p>Allahabad Convent School & Girls'' College, established in 2002, is committed to providing quality education in a safe, supportive and inspiring environment.</p><h3>Our Journey</h3><p>Over the years, thousands of students have excelled in academics, sports and cultural activities under the guidance of our dedicated faculty.</p><h3>What Makes Us Different</h3><p>We believe education is not just about books — it is about building character, instilling discipline and nurturing values.</p>',
'uploads/hero.jpg'),
('academics', 'Academics', 'Programs for a Brighter Tomorrow',
'<p>We offer a structured academic curriculum from Primary to Intermediate level, designed to bring out the best in every student.</p>',
'uploads/hero.jpg'),
('admissions', 'Admissions', 'Admissions Open for 2026-27',
'<p>Applications are now open for all classes. Visit the school office with required documents between 8:00 AM and 4:00 PM.</p>',
'uploads/hero.jpg'),
('administration', 'Administration', 'Leadership That Inspires',
'<p>Meet the leadership of Allahabad Convent School & Girls'' College — the people who guide our institution with vision, dedication and service.</p>',
'uploads/hero.jpg');

INSERT INTO mission_vision (icon, title, description, sort_order) VALUES
('fa-bullseye', 'Our Mission', 'To provide quality education that combines academic excellence with moral values, empowering every student to become confident, responsible and compassionate.', 1),
('fa-eye', 'Our Vision', 'To be a centre of learning where young minds are nurtured to lead, serve and build a brighter future for society and the nation.', 2),
('fa-heart', 'Our Values', 'Discipline, honesty, respect and service — the core values that guide everything we do at our institution.', 3);

INSERT INTO hero_slides (title_line1, title_line2, subtitle, image, sort_order) VALUES
('Empowering Young Minds,', 'Building Bright Futures',
'A nurturing environment for academic excellence, character building and holistic development of every student.',
'uploads/hero.jpg', 1),
('Shaping Future Leaders,', 'Through Values & Excellence',
'Discipline, dedication and determination — the three pillars of success we instill in every student.',
'uploads/hero2.jpg', 2);

INSERT INTO messages (designation, name, photo, message, show_on_home, sort_order) VALUES
('Manager', 'Manager', 'uploads/manager.jpg',
'Our institution is built on the vision of providing value-based education to every child. We are committed to creating an environment where students grow academically, morally and socially to serve the nation with pride.',
1, 1),
('Principal', 'Principal', 'uploads/principal.jpg',
'At Allahabad Convent School & Girls'' College, we believe in nurturing not just academic excellence but also strong values, confidence and compassion. Our aim is to empower every student to become a responsible citizen and a leader of tomorrow.',
1, 2);

INSERT INTO notices (title, description) VALUES
('Welcome to Admin Panel', 'Yahan aane wale notices login page aur dashboard par dikhenge.');

-- ⚠️ Password hash placeholder hai — import ke baad set karna zaroori:
-- (a) LOCAL (CLI):      php reset-cli.php Admin@123
-- (b) HOSTING (SQL):    local pe hash banao:
--     php -r "echo password_hash('Admin@123', PASSWORD_DEFAULT);"
--     phir: UPDATE admins SET password='<hash>', email='recovery@email.com' WHERE username='admin';
INSERT INTO admins (username, password, name, role, status) VALUES
('admin', 'SET_PASSWORD_AFTER_IMPORT', 'Super Admin', 'super', 1);

-- ============================================================
-- IMPORT KE BAAD STEPS:
-- 1. Admin password set karo (upar 2 tarike)
-- 2. Recovery email set karo (forgot password ke liye):
--    UPDATE admins SET email='apna@email.com' WHERE username='admin';
-- 3. uploads/ folder me images daalo:
--    logo.png, hero.jpg, hero2.jpg, about.jpg,
--    manager.jpg, principal.jpg, braintech-logo.svg,
--    gallery/ folder (6 photos)
-- 4. config/db.php + config/mail.php hosting credentials se update
-- ============================================================