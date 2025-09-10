-- ======================================
-- ROLES Y PERMISOS EN MYSQL
-- ======================================

-- Crear usuario para estudiantes (sin contraseña aquí)
CREATE USER IF NOT EXISTS 'db_student'@'%' IDENTIFIED BY '{{DB_STUDENT_PASS}}';
-- Permiso de conexión a la base de datos
GRANT USAGE ON *.* TO 'db_student'@'%';

-- Estudiantes pueden:
GRANT SELECT, INSERT, UPDATE ON {{DB_DATABASE}}.projects TO 'db_student'@'%';

--GRANT SELECT ON {{DB_DATABASE}}.courses TO 'db_student'@'%';
--GRANT SELECT ON {{DB_DATABASE}}.professors TO 'db_student'@'%';
--GRANT SELECT ON {{DB_DATABASE}}.research_staff TO 'db_student'@'%';
---- Nota: no damos permisos sobre `{{DB_DATABASE}}.users`
--
---- ======================================
--
---- Crear usuario para profesores
--CREATE USER IF NOT EXISTS 'db_professor'@'%' IDENTIFIED BY '{{DB_PROFESSOR_PASS}}';
--GRANT USAGE ON *.* TO 'db_professor'@'%';
--
---- Profesores pueden leer todas las tablas menos `users`
--GRANT SELECT ON {{DB_DATABASE}}.projects TO 'db_professor'@'%';
--GRANT SELECT ON {{DB_DATABASE}}.courses TO 'db_professor'@'%';
--GRANT SELECT ON {{DB_DATABASE}}.research_staff TO 'db_professor'@'%';
---- Profesores pueden también crear proyectos
--GRANT INSERT, UPDATE ON {{DB_DATABASE}}.projects TO 'db_professor'@'%';
--
---- ======================================
--
---- Crear usuario para research_staff
--CREATE USER IF NOT EXISTS 'db_research_staff'@'%' IDENTIFIED BY '{{DB_RESEARCH_PASS}}';
--GRANT USAGE ON *.* TO 'db_research_staff'@'%';
--
---- Research staff puede hacer de todo en projects, courses y research
--GRANT SELECT, INSERT, UPDATE, DELETE ON {{DB_DATABASE}}.projects TO 'db_research_staff'@'%';
--GRANT SELECT, INSERT, UPDATE, DELETE ON {{DB_DATABASE}}.courses TO 'db_research_staff'@'%';
--GRANT SELECT, INSERT, UPDATE, DELETE ON {{DB_DATABASE}}.research_staff TO 'db_research_staff'@'%';
--
---- Solo lectura en professors
--GRANT SELECT ON {{DB_DATABASE}}.professors TO 'db_research_staff'@'%';

-- Compatibilidad con versiones antiguas
FLUSH PRIVILEGES;

