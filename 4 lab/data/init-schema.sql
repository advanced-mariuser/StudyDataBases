CREATE TABLE client (
                        client_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        first_name VARCHAR(50) NOT NULL,
                        last_name VARCHAR(50) NOT NULL,
                        phone VARCHAR(20) NOT NULL,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE master (
                        master_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        first_name VARCHAR(50) NOT NULL,
                        last_name VARCHAR(50) NOT NULL,
                        phone VARCHAR(20) NOT NULL,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE appointment (
                             appointment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                             client_id INT NOT NULL,
                             master_id INT NOT NULL,
                             date DATETIME NOT NULL,
                             created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             FOREIGN KEY (client_id) REFERENCES client(client_id),
                             FOREIGN KEY (master_id) REFERENCES master(master_id) ON DELETE RESTRICT
);