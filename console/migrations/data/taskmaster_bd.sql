/*Таблица для справочника городов*/

CREATE TABLE IF NOT EXISTS city (
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
latitude DECIMAL(10,7) NOT NULL,
longitude DECIMAL(10,7) NOT NULL
);


/*Таблица для справочника категорий*/

CREATE TABLE IF NOT EXISTS category (
id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL UNIQUE,
icon VARCHAR(100) NOT NULL
);

/*Таблица для хранения пользователей*/

CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL UNIQUE,
	email VARCHAR(50) NOT NULL UNIQUE,
    reg_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	last_visit_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    password CHAR(64) NOT NULL
);


/*Таблица для хранения контактов пользователей*/

CREATE TABLE IF NOT EXISTS user_profile (
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_id INT UNSIGNED NOT NULL,
birth_date TIMESTAMP,
description text,
avatar VARCHAR(100),
city_id INT UNSIGNED NOT NULL,
address VARCHAR(255),
phone VARCHAR(12) UNIQUE,
skype VARCHAR(50) UNIQUE,
other VARCHAR(50) UNIQUE,

FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE,
FOREIGN KEY (city_id) REFERENCES city (id)
);

/*Таблица для хранения заданий*/

CREATE TABLE IF NOT EXISTS task (
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(100) NOT NULL,
description text NOT NULL,
category_id TINYINT UNSIGNED NOT NULL,
client_id INT UNSIGNED NOT NULL,
executor_id INT UNSIGNED,
budget INT UNSIGNED,
status TINYINT UNSIGNED NOT NULL,
due_date TIMESTAMP NOT NULL,
creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
city_id INT UNSIGNED,
address VARCHAR(255),
latitude DECIMAL(10,7),
longitude DECIMAL(10,7),

FOREIGN KEY (client_id) REFERENCES user (id),
FOREIGN KEY (executor_id) REFERENCES user (id),
FOREIGN  KEY (city_id) REFERENCES  city (id),
FOREIGN  KEY (category_id) REFERENCES  category (id)

);

/*Таблица для примеров работ пользователя*/

CREATE TABLE IF NOT EXISTS user_portfolio(
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_id INT UNSIGNED NOT NULL,
file VARCHAR(255) NOT NULL,

FOREIGN KEY (user_id) REFERENCES user (id)
);


/*Таблица для вложенных файлов к заданию*/

CREATE TABLE IF NOT EXISTS task_files(
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
task_id INT UNSIGNED NOT NULL,
url VARCHAR(255) NOT NULL,
name VARCHAR(100) NOT NULL,

FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE
);


/*Таблица для специализаций пользователей*/

CREATE TABLE IF NOT EXISTS user_category(
user_id INT UNSIGNED NOT NULL,
category_id TINYINT UNSIGNED NOT NULL,
active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,

FOREIGN KEY (category_id) REFERENCES category (id),
FOREIGN KEY (user_id) REFERENCES user (id)
);


/*Таблица для откликов по задачам*/

CREATE TABLE IF NOT EXISTS respond(
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_id INT UNSIGNED NOT NULL,
task_id INT UNSIGNED NOT NULL,
description text NOT NULL,
rate INT UNSIGNED NOT NULL,
status TINYINT UNSIGNED NOT NULL DEFAULT 1,
creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id) REFERENCES user (id),
FOREIGN KEY (task_id) REFERENCES task(id)
);

/*Таблица для отзывов по работе испонителя*/

CREATE TABLE IF NOT EXISTS recall(
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
task_id INT UNSIGNED NOT NULL,
description text NOT NULL,
rating TINYINT UNSIGNED NOT NULL,
creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (task_id) REFERENCES task (id)
);

/*Таблица для переписки по задаче*/

CREATE TABLE IF NOT EXISTS correspondence(
id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
task_id INT UNSIGNED NOT NULL,
user_id INT UNSIGNED NOT NULL,
content TEXT NOT NULL,
creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id) REFERENCES user (id),
FOREIGN KEY (task_id) REFERENCES task(id)
);

/*Хранение избранных пользователей*/

CREATE TABLE IF NOT EXISTS user_favorite(
chooser_id INT UNSIGNED NOT NULL,
chosen_id INT UNSIGNED NOT NULL,

FOREIGN KEY (chooser_id) REFERENCES user (id),
FOREIGN KEY (chosen_id) REFERENCES user (id)
);

/*Настройки пользователя*/

 CREATE TABLE IF NOT EXISTS user_settings(
 user_id INT UNSIGNED NOT NULL,
 new_message TINYINT UNSIGNED NOT NULL DEFAULT 1,
 task_actions TINYINT UNSIGNED NOT NULL DEFAULT 1,
 new_recall TINYINT UNSIGNED NOT NULL DEFAULT 1,
 hide_profile TINYINT UNSIGNED NOT NULL DEFAULT 0,
 contacts_only_for_client TINYINT UNSIGNED NOT NULL DEFAULT 0,

FOREIGN KEY (user_id) REFERENCES user (id)
 );
