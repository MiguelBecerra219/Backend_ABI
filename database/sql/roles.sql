-- ======================================
-- ROLES Y PERMISOS EN MYSQL
-- ======================================

-- Crear usuario basico para login
CREATE USER IF NOT EXISTS 'db_user'@'%' IDENTIFIED BY '{{DB_USER_PASS}}';
-- Permiso de conexión a la base de datos
GRANT USAGE ON *.* TO 'db_user'@'%';

-- los usuarios basicos pueden:
GRANT SELECT ON {{DB_DATABASE}}.users TO 'db_user'@'%';

-- Crear usuario para estudiantes
CREATE USER IF NOT EXISTS 'db_student'@'%' IDENTIFIED BY '{{DB_STUDENT_PASS}}';
-- Permiso de conexión a la base de datos
GRANT USAGE ON *.* TO 'db_student'@'%';

-- Estudiantes pueden:
GRANT SELECT ON {{DB_DATABASE}}.departments TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.cities TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.city_program TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.programs TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.research_groups TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.investigation_lines TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.thematic_areas TO 'db_student'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.projects TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.project_statuses TO 'db_student'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_framework_project TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.content_frameworks TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.frameworks TO 'db_student'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.versions TO 'db_student'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_version TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.contents TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.professors TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.professor_project TO 'db_student'@'%';
GRANT SELECT ON {{DB_DATABASE}}.students TO 'db_student'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.student_project TO 'db_student'@'%';
GRANT SELECT, UPDATE ON {{DB_DATABASE}}.users TO 'db_student'@'%';

---- ======================================
--
---- Crear usuario para profesores
CREATE USER IF NOT EXISTS 'db_professor'@'%' IDENTIFIED BY '{{DB_PROFESSOR_PASS}}';
GRANT USAGE ON *.* TO 'db_professor'@'%';
--
---- Profesores pueden :
GRANT SELECT ON {{DB_DATABASE}}.departments TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.cities TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.city_program TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.programs TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.research_groups TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.investigation_lines TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.thematic_areas TO 'db_professor'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.projects TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.project_statuses TO 'db_professor'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_framework_project TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.content_frameworks TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.frameworks TO 'db_professor'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.versions TO 'db_professor'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_version TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.contents TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.professors TO 'db_professor'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.professor_project TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.students TO 'db_professor'@'%';
GRANT SELECT ON {{DB_DATABASE}}.student_project TO 'db_professor'@'%';
GRANT SELECT, UPDATE ON {{DB_DATABASE}}.users TO 'db_professor'@'%';
--
---- ======================================
--
---- Crear usuario para research_staff
CREATE USER IF NOT EXISTS 'db_research_staff'@'%' IDENTIFIED BY '{{DB_RESEARCH_PASS}}';
GRANT USAGE ON *.* TO 'db_research_staff'@'%';
--
---- Research staff puede:
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.departments TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.cities TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.city_program TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.programs TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.research_groups TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.investigation_lines TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.thematic_areas TO 'db_research_staff'@'%';
GRANT SELECT, UPDATE ON {{DB_DATABASE}}.projects TO 'db_research_staff'@'%';
GRANT SELECT ON {{DB_DATABASE}}.project_statuses TO 'db_research_staff'@'%';
GRANT SELECT ON {{DB_DATABASE}}.content_framework_project TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_frameworks TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.frameworks TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.versions TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.content_version TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.contents TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.professors TO 'db_research_staff'@'%';
GRANT SELECT ON {{DB_DATABASE}}.professor_project TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.students TO 'db_research_staff'@'%';
GRANT SELECT ON {{DB_DATABASE}}.student_project TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.users TO 'db_research_staff'@'%';
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.research_staff TO 'db_research_staff'@'%';

-- Compatibilidad con versiones antiguas
FLUSH PRIVILEGES;

