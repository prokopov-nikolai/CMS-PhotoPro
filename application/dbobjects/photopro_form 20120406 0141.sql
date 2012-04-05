-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.63.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 06.04.2012 1:48:00
-- Версия сервера: 5.1.58-1ubuntu1
-- Версия клиента: 4.1

-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

--
-- Описание для таблицы ci_form
--
DROP TABLE IF EXISTS ci_form;
CREATE TABLE ci_form (
  form_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор формы',
  form_title VARCHAR(255) NOT NULL COMMENT 'Название формы',
  form_url VARCHAR(255) NOT NULL COMMENT 'Урл формы - второй ключ',
  message_email VARCHAR(255) DEFAULT NULL COMMENT 'Email куда будут отправлять',
  message_subject VARCHAR(255) DEFAULT NULL COMMENT 'Тема сообщения',
  PRIMARY KEY (form_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 16384
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы ci_form_field
--
DROP TABLE IF EXISTS ci_form_field;
CREATE TABLE ci_form_field (
  field_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор поля',
  field_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Время добавления',
  field_title VARCHAR(255) NOT NULL COMMENT 'Заголовок поля',
  field_url VARCHAR(255) NOT NULL COMMENT 'Урл - ключ поля',
  field_type ENUM('text','textarea','select','phone','email','checkbox') NOT NULL DEFAULT 'text' COMMENT 'Тип поля',
  field_select VARCHAR(255) DEFAULT NULL COMMENT 'Значения для списка через ;',
  field_redactor TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Использовать редактор',
  field_validate ENUM('none','number','text','number_text','phone','email') NOT NULL DEFAULT 'none' COMMENT 'Валидация поля',
  field_mask VARCHAR(32) DEFAULT NULL COMMENT 'Маска телефона',
  field_necessarily TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Поле обязательно для заполнения',
  form_id INT(11) UNSIGNED NOT NULL COMMENT 'Идентификатор формы',
  PRIMARY KEY (field_id),
  INDEX FK_ci_form_field_ci_form_form_id (form_id),
  UNIQUE INDEX UK_ci_form_field (field_url, form_id),
  CONSTRAINT FK_ci_form_field_ci_form_form_id FOREIGN KEY (form_id)
    REFERENCES ci_form(form_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 3276
CHARACTER SET utf8
COLLATE utf8_general_ci;

-- 
-- Вывод данных для таблицы ci_form
--
INSERT INTO ci_form VALUES 
  (1, 'Купить авто', 'kupit-avto', 'prokopov-nikolaj@yandex.ru', 'Новая заявка на Покупку авто!');

-- 
-- Вывод данных для таблицы ci_form_field
--
INSERT INTO ci_form_field VALUES 
  (1, '2012-04-05 23:42:23', 'ФИО', 'fio', 'text', '', 0, 'text', '', 1, 1),
  (2, '2012-04-05 23:42:35', 'Телефон', 'telefon', 'phone', '', 0, 'phone', '(999) 999-99-99', 1, 1),
  (3, '2012-04-05 23:42:59', 'Ваш отзыв с картикой', 'vash-otziv-s-kartikoy', 'textarea', '', 1, 'none', '', 0, 1),
  (4, '2012-04-05 23:44:30', 'Выпадающий список', 'vipadayuschiy-spisok', 'select', 'зачение 1;значение2;значние3', 0, 'none', '', 1, 1),
  (5, '2012-04-06 01:25:12', 'У меня есть авто', 'u-menya-est-avto', 'checkbox', '', 0, 'none', '', 0, 1);

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;