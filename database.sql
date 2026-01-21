-- =============================================
-- Job Dating Database Schema
-- =============================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `user_sessions`;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

-- =============================================
-- Table: users
-- =============================================
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('student','admin') NOT NULL DEFAULT 'student',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_email` (`email`),
    KEY `idx_role` (`role`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: students
-- =============================================
CREATE TABLE `students` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `promotion` varchar(50) NOT NULL,
    `specialization` varchar(100) NOT NULL,
    `cv_path` varchar(255) DEFAULT NULL,
    `linkedin_url` varchar(255) DEFAULT NULL,
    `portfolio_url` varchar(255) DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `skills` text DEFAULT NULL,
    `looking_for_job` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_user_id` (`user_id`),
    KEY `idx_promotion` (`promotion`),
    KEY `idx_specialization` (`specialization`),
    KEY `idx_looking_for_job` (`looking_for_job`),
    CONSTRAINT `fk_students_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: user_sessions (for session management)
-- =============================================
CREATE TABLE `user_sessions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `session_id` varchar(255) NOT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `expires_at` timestamp NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 2 HOUR),
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_session_id` (`session_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires_at` (`expires_at`),
    CONSTRAINT `fk_sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: announcements (for job postings)
-- =============================================
CREATE TABLE `announcements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `company` varchar(255) NOT NULL,
    `location` varchar(255) DEFAULT NULL,
    `type` enum('internship','job','alternance') NOT NULL DEFAULT 'internship',
    `duration` varchar(100) DEFAULT NULL,
    `salary` varchar(100) DEFAULT NULL,
    `requirements` text DEFAULT NULL,
    `contact_email` varchar(255) NOT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `expires_at` timestamp NULL DEFAULT NULL,
    `created_by` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_expires_at` (`expires_at`),
    KEY `idx_created_by` (`created_by`),
    CONSTRAINT `fk_announcements_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: applications (student job applications)
-- =============================================
CREATE TABLE `applications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `student_id` int(11) NOT NULL,
    `announcement_id` int(11) NOT NULL,
    `cover_letter` text DEFAULT NULL,
    `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_application` (`student_id`, `announcement_id`),
    KEY `idx_student_id` (`student_id`),
    KEY `idx_announcement_id` (`announcement_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_applications_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_applications_announcement_id` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: login_attempts (for security)
-- =============================================
CREATE TABLE `login_attempts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `success` tinyint(1) NOT NULL DEFAULT 0,
    `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_ip_address` (`ip_address`),
    KEY `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Insert sample data
-- =============================================

-- Insert admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin Principal', 'admin@jobdating.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample students
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Jean Dupont', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Marie Martin', 'marie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Pierre Durand', 'pierre.durand@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Insert student details
INSERT INTO `students` (`user_id`, `promotion`, `specialization`, `bio`, `skills`) VALUES
(2, '2024-2025', 'informatique', 'Étudiant passionné en développement web', 'PHP, JavaScript, MySQL, React'),
(3, '2024-2025', 'genie-civil', 'Intéressé par les projets de construction durable', 'AutoCAD, Revit, BIM'),
(4, '2023-2024', 'marketing', 'Spécialisé en marketing digital', 'SEO, Google Ads, Social Media, Analytics');

-- Insert sample announcements
INSERT INTO `announcements` (`title`, `description`, `company`, `location`, `type`, `duration`, `requirements`, `contact_email`, `created_by`) VALUES
('Développeur Web Stage', 'Recherche développeur web pour stage de 6 mois. Connaissance de PHP/MySQL requise.', 'Tech Company', 'Paris', 'internship', '6 mois', 'PHP, MySQL, HTML, CSS, JavaScript. Anglais technique requis.', 'rh@techcompany.com', 1),
('Stage Génie Civil', 'Stage en génie civil pour projet d\'infrastructure.', 'Construction Pro', 'Lyon', 'internship', '3 mois', 'AutoCAD, Revit. Permis de conduire nécessaire.', 'jobs@constructionpro.com', 1),
('Marketing Digital Alternance', 'Alternance en marketing digital pour étudiant Bac+2.', 'Digital Agency', 'Marseille', 'alternance', '2 ans', 'Connaissance des réseaux sociaux, Google Analytics.', 'contact@digitalagency.com', 1);

-- =============================================
-- Indexes for performance optimization
-- =============================================

-- Additional indexes for better performance
CREATE INDEX `idx_users_email_role` ON `users` (`email`, `role`);
CREATE INDEX `idx_students_promo_spec` ON `students` (`promotion`, `specialization`);
CREATE INDEX `idx_announcements_type_active` ON `announcements` (`type`, `is_active`);
CREATE INDEX `idx_applications_status_student` ON `applications` (`status`, `student_id`);
CREATE INDEX `idx_login_attempts_email_ip_time` ON `login_attempts` (`email`, `ip_address`, `attempted_at`);

-- =============================================
-- Views for common queries
-- =============================================

-- View: Student profiles with user info
CREATE VIEW `student_profiles` AS
SELECT 
    u.id,
    u.name,
    u.email,
    u.created_at as user_created_at,
    s.promotion,
    s.specialization,
    s.cv_path,
    s.linkedin_url,
    s.portfolio_url,
    s.bio,
    s.skills,
    s.looking_for_job,
    s.created_at as student_created_at
FROM users u
INNER JOIN students s ON u.id = s.user_id
WHERE u.role = 'student';

-- View: Active announcements with company info
CREATE VIEW `active_announcements` AS
SELECT 
    a.id,
    a.title,
    a.description,
    a.company,
    a.location,
    a.type,
    a.duration,
    a.salary,
    a.requirements,
    a.contact_email,
    a.expires_at,
    a.created_at,
    u.name as created_by_name
FROM announcements a
INNER JOIN users u ON a.created_by = u.id
WHERE a.is_active = 1 
AND (a.expires_at IS NULL OR a.expires_at > CURRENT_TIMESTAMP);

-- =============================================
-- Stored procedures for common operations
-- =============================================

DELIMITER //

-- Procedure: Get student applications
CREATE PROCEDURE `GetStudentApplications`(IN student_user_id INT)
BEGIN
    SELECT 
        a.id as application_id,
        a.status,
        a.applied_at,
        ann.title,
        ann.company,
        ann.location,
        ann.type
    FROM applications a
    INNER JOIN announcements ann ON a.announcement_id = ann.id
    INNER JOIN students s ON a.student_id = s.id
    WHERE s.user_id = student_user_id
    ORDER BY a.applied_at DESC;
END //

-- Procedure: Get announcement statistics
CREATE PROCEDURE `GetAnnouncementStats`()
BEGIN
    SELECT 
        COUNT(*) as total_announcements,
        COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_announcements,
        COUNT(CASE WHEN type = 'internship' THEN 1 END) as internships,
        COUNT(CASE WHEN type = 'job' THEN 1 END) as jobs,
        COUNT(CASE WHEN type = 'alternance' THEN 1 END) as alternances
    FROM announcements;
END //

DELIMITER ;

-- =============================================
-- Final notes
-- =============================================

-- Default passwords for sample accounts:
-- Admin: admin@jobdating.com / admin123
-- Students: *.email.com / password

-- Remember to:
-- 1. Change default passwords in production
-- 2. Set up proper database user permissions
-- 3. Configure backup strategy
-- 4. Set up monitoring for the database
