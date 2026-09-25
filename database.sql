-- ============================================================
-- A.L. Convent School — Complete Database Setup (v2)
-- Includes: settings+logo, pages CMS, hero slider, mission,
--           gallery, news, stats, programs, facilities, admins
-- Import: phpMyAdmin > Import > database.sql
-- ⚠️ Import ke baad EK BAAR reset-admin.php run karo
--    (admin password set karne ke liye)
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
    youtube VARCHAR(255) DEFAULT '#'
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

-- ============ 6. PRINCIPAL MESSAGE ============
CREATE TABLE principal_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT 'Principal',
    photo VARCHAR(255) DEFAULT 'uploads/principal.jpg',
    message TEXT
);

-- ============ 7. NEWS & EVENTS ============
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    image VARCHAR(255),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ 8. GALLERY ============
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0
);

-- ============ 9. CMS PAGES (About/Academics/Admissions/Facilities) ============
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

-- ============ 12. ADMIN USERS ============
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
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

INSERT INTO principal_message (name, photo, message) VALUES
('Principal',
'uploads/principal.jpg',
'At Allahabad Convent School & Girls'' College, we believe in nurturing not just academic excellence but also strong values, confidence and compassion. Our aim is to empower every student to become a responsible citizen and a leader of tomorrow.');

INSERT INTO news (title, description, event_date) VALUES
('Admissions Open for 2026-27', 'Applications are now open for all classes.', '2026-09-15'),
('Annual Sports Day', 'Join us for an exciting day of sports and talent.', '2026-09-10'),
('Teachers'' Day Celebration', 'A special event to honor our beloved teachers.', '2026-09-05');

INSERT INTO gallery (title, image, sort_order) VALUES
('School Building', 'uploads/gallery/school-building.jpg', 1),
('Classroom Learning', 'uploads/gallery/classroom.jpg', 2),
('Annual Function', 'uploads/gallery/annual-function.jpg', 3),
('Sports Day', 'uploads/gallery/sports.jpg', 4),
('Computer Lab', 'uploads/gallery/computer-lab.jpg', 5),
('Award Ceremony', 'uploads/gallery/awards.jpg', 6);

INSERT INTO pages (slug, page_title, page_subtitle, content, banner_image) VALUES
('about', 'About Our School', 'A Legacy of Learning and Character Building',
'<h3>Our Story</h3><p>Allahabad Convent School & Girls'' College, established in 2002, is committed to providing quality education in a safe, supportive and inspiring environment.</p><h3>Our Journey</h3><p>Over the years, thousands of students have excelled in academics, sports and cultural activities under the guidance of our dedicated faculty.</p><h3>What Makes Us Different</h3><p>We believe education is not just about books — it is about building character, instilling discipline and nurturing values.</p>',
'uploads/hero.jpg'),
('academics', '