DROP TABLE IF EXISTS country;
DROP TABLE IF EXISTS state;
DROP TABLE IF EXISTS city;

CREATE TABLE country (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    short_name VARCHAR(255) NOT NULL,
    phone_code VARCHAR(255) NOT NULL,
    flag VARCHAR(255) NOT NULL,
    geoname_id INT NOT NULL
);

CREATE TABLE state (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    geoname_id INT NOT NULL,
    id_country INT NOT NULL,
    geoname_id INT NOT NULL,
    FOREIGN KEY (id_country) REFERENCES country(id)
);

CREATE TABLE city (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    timezone VARCHAR(255),
    id_state INT,
    id_country INT,
    FOREIGN KEY (id_state) REFERENCES state(id),
);