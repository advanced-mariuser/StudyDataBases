CREATE DATABASE crm CHARACTER SET = utf8mb4;
CREATE USER 'crmuser'@'localhost' IDENTIFIED BY 'Ook4au5a';
GRANT ALL PRIVILEGES ON crm.* TO 'crmuser'@'localhost';