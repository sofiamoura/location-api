DROP TABLE IF EXISTS country;
DROP TABLE IF EXISTS state;
DROP TABLE IF EXISTS city;

CREATE TABLE country (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    short_name VARCHAR(255) NOT NULL,
    phone_code VARCHAR(255) NOT NULL,
    flag VARCHAR(255) NOT NULL
);

CREATE TABLE state (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    id_country INT NOT NULL,
    FOREIGN KEY (id_country) REFERENCES country(id)
);

CREATE TABLE city (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    timezone VARCHAR(255),
    id_state INT,
    id_country INT,
    FOREIGN KEY (id_state) REFERENCES state(id),
    FOREIGN KEY (id_country) REFERENCES country(id),
    CHECK (
        (id_state IS NOT NULL AND id_country IS NULL) OR
        (id_country IS NOT NULL AND id_state IS NULL)
    )
);