-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.63.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 03.04.2012 22:52:27
-- Версия сервера: 5.1.56
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
-- Описание для таблицы gram_catalogpro_char
--
DROP TABLE IF EXISTS gram_catalogpro_char;
CREATE TABLE gram_catalogpro_char (
  char_id SMALLINT(6) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор характеристики',
  char_title VARCHAR(255) NOT NULL COMMENT 'Название характеристики',
  char_type ENUM('select','text') NOT NULL DEFAULT 'text' COMMENT 'Тип характеристики',
  char_unit VARCHAR(50) DEFAULT NULL COMMENT 'Единица измерения',
  char_sort TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Сортировка',
  category_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Идентификатор категории',
  PRIMARY KEY (char_id),
  INDEX category_category_id (category_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 2048
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы gram_catalogpro_make
--
DROP TABLE IF EXISTS gram_catalogpro_make;
CREATE TABLE gram_catalogpro_make (
  make_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор производителя',
  make_url VARCHAR(255) NOT NULL COMMENT 'Урл производителя',
  make_title VARCHAR(255) DEFAULT NULL COMMENT 'Название производителя',
  make_description TEXT DEFAULT NULL COMMENT 'Описание производителя',
  make_keywords VARCHAR(255) DEFAULT NULL COMMENT 'Ключевые слова для производителя',
  make_rating_sum INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Сумма всех оценок',
  make_rating_count INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество оценок',
  make_rating_itog FLOAT(4, 2) NOT NULL DEFAULT 0.00 COMMENT 'Итого средний рейтинг',
  PRIMARY KEY (make_id),
  UNIQUE INDEX UK_makeit_catalogpro_make_make_title (make_title),
  UNIQUE INDEX UK_makeit_catalogpro_make_make_url (make_url)
)
ENGINE = INNODB
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 2730
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы gram_catalogpro_category
--
DROP TABLE IF EXISTS gram_catalogpro_category;
CREATE TABLE gram_catalogpro_category (
  category_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор категории',
  category_parent_id INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Идентификатор родительской категории',
  category_url VARCHAR(255) NOT NULL COMMENT 'URL категории',
  category_title VARCHAR(255) DEFAULT NULL COMMENT 'Название категории',
  category_keywords VARCHAR(255) DEFAULT NULL COMMENT 'Ключевые фразы',
  category_description VARCHAR(255) DEFAULT NULL COMMENT 'Описание категории',
  make_id INT(11) UNSIGNED NOT NULL COMMENT 'Идентификатор производителя',
  PRIMARY KEY (category_id),
  UNIQUE INDEX category_id (category_id),
  INDEX FK_catalogpro_category_catalogpro_make_make_id (make_id),
  UNIQUE INDEX UK_makeit_catalogpro_category_category_title (category_title),
  UNIQUE INDEX UK_makeit_catalogpro_category_category_url (category_url),
  CONSTRAINT FK_catalogpro_category_catalogpro_make_make_id FOREIGN KEY (make_id)
    REFERENCES gram_catalogpro_make(make_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 12
AVG_ROW_LENGTH = 1820
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы gram_catalogpro_char_select
--
DROP TABLE IF EXISTS gram_catalogpro_char_select;
CREATE TABLE gram_catalogpro_char_select (
  char_id SMALLINT(6) UNSIGNED NOT NULL COMMENT 'Идентификатор характеристики',
  char_value VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Значение характеристики (текст)',
  char_value_sort SMALLINT(6) UNSIGNED NOT NULL COMMENT 'Сортирова значений характеристики',
  PRIMARY KEY (char_id, char_value),
  CONSTRAINT FK_catalogpro_char_select_catalogpro_char_char_id FOREIGN KEY (char_id)
    REFERENCES gram_catalogpro_char(char_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 1170
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Справочник значений характеристик';

--
-- Описание для таблицы gram_catalogpro_product
--
DROP TABLE IF EXISTS gram_catalogpro_product;
CREATE TABLE gram_catalogpro_product (
  product_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор производителя',
  product_url VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'урл продукта',
  product_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Название производителя',
  product_keywords VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Ключевые слова для производителя',
  product_description TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Описание производителя',
  product_rating_sum INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Сумма всех оценок',
  product_rating_count INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество оценок',
  product_rating_itog FLOAT(4, 2) NOT NULL DEFAULT 0.00 COMMENT 'Итого средний рейтинг',
  product_views BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество просмотров',
  product_pod_zakaz TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Товар под заказ',
  product_special TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Спец предложение',
  product_in_slider TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Показывать в слайдере',
  product_hide TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Скрыть товар',
  product_date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Время добавления товара',
  category_id INT(11) UNSIGNED NOT NULL COMMENT 'Идентификатор категории',
  make_id INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (product_id),
  INDEX category_id (category_id),
  INDEX fk_gram_catalogpro_product_1 (make_id),
  INDEX IX_gram_catalogpro_product_product_in_slider (product_in_slider),
  INDEX IX_gram_catalogpro_product_product_special (product_special),
  INDEX UK_makeit_catalogpro_product_product_title (product_title),
  CONSTRAINT FK_catalogpro_product_catalogpro_category_category_id FOREIGN KEY (category_id)
    REFERENCES gram_catalogpro_category(category_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_gram_catalogpro_product_1 FOREIGN KEY (make_id)
    REFERENCES gram_catalogpro_make(make_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AUTO_INCREMENT = 14
AVG_ROW_LENGTH = 1638
CHARACTER SET utf8
COLLATE utf8_bin;

--
-- Описание для таблицы gram_catalogpro_char_value
--
DROP TABLE IF EXISTS gram_catalogpro_char_value;
CREATE TABLE gram_catalogpro_char_value (
  product_id INT(11) UNSIGNED NOT NULL COMMENT 'Идентификатор продукта',
  char_id SMALLINT(6) UNSIGNED NOT NULL COMMENT 'Идентификатор характеристики',
  char_value VARCHAR(255) DEFAULT NULL COMMENT 'Значение характеристики (текст)',
  PRIMARY KEY (product_id, char_id),
  INDEX IX_catalogpro_char_value (char_id, char_value),
  INDEX product_char_char_id (char_id),
  CONSTRAINT FK_catalogpro_char_value_catalogpro_char_char_id FOREIGN KEY (char_id)
    REFERENCES gram_catalogpro_char(char_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_catalogpro_char_value_catalogpro_product_product_id FOREIGN KEY (product_id)
    REFERENCES gram_catalogpro_product(product_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 227
CHARACTER SET utf8
COLLATE utf8_general_ci;

-- 
-- Вывод данных для таблицы gram_catalogpro_char
--
INSERT INTO gram_catalogpro_char VALUES 
  (1, 'Год выпуска', 'text', NULL, 2, NULL),
  (2, 'Пробег', 'text', 'мили', 1, NULL),
  (3, 'КПП', 'select', NULL, 2, NULL),
  (4, 'Цвет', 'select', NULL, 3, NULL),
  (5, 'Цена', 'text', 'руб.', 4, NULL),
  (6, 'Двигатель', 'text', '', 10, NULL),
  (8, 'Тип двигателя', 'select', NULL, 5, NULL),
  (10, 'Тип кузова', 'select', '', 9, NULL);

-- 
-- Вывод данных для таблицы gram_catalogpro_make
--
INSERT INTO gram_catalogpro_make VALUES 
  (2, 'bmw', 'BMW', '\n<p>BMW 1123123\n<br></p>', 'BMW123', 0, 0, 0.00),
  (5, 'audi-1', 'AUDI', '\n<p>AUDI</p>', 'AUDI,audi,ауди', 0, 0, 0.00),
  (6, 'toyota', 'Toyota', '\n<p><strong>Toyota</strong>&nbsp; описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описание описанием\n<br></p>', 'Toyota', 0, 0, 0.00),
  (7, 'porsche', 'Porsche', '\n', '', 0, 0, 0.00),
  (8, 'chevrolet', 'Chevrolet', '\n', '', 0, 0, 0.00),
  (9, 'mercedes', 'Mercedes', '\n', '', 0, 0, 0.00);

-- 
-- Вывод данных для таблицы gram_catalogpro_category
--
INSERT INTO gram_catalogpro_category VALUES 
  (3, 0, 'a4', 'A4', 'A4', '\n<p>A4</p>', 5),
  (4, 0, 'a6', 'A6', 'A6', '\n<p>A6</p>', 5),
  (5, 0, 'highlander', 'Highlander', 'Highlander', '\n<p><strong>Highlander </strong><strong>Highlander </strong><strong>Highlander</strong></p>', 6),
  (6, 0, 'land-cruiser-prado-150', 'Land Cruiser Prado 150', 'Land Cruiser Prado 150', '\n<p><strong>Land Cruiser Prado 150</strong> \n<br></p>', 6),
  (7, 0, 'avensis-ii', 'Avensis II', 'Avensis II', '\n<p><strong>Avensis II</strong></p>', 6),
  (8, 0, 'cayman', 'Cayman', '', '\n', 7),
  (9, 0, 'bmw-x6', 'BMW X6', '', '\n<br>\n<ul>\n\t<li> Антиблокировочная система (ABS)</li>\n\t<li> Антипробуксовочная система</li>\n\t<li> Бортовой компьютер</li>\n\t<li> Датчик дождя</li>\n\t<li> Датчик света</li>\n\t<li> Кондиционер (климат 3-зонный и выше)</li>\n\t<li> Круиз-контроль</li>\n\t<li> Ксено', 2),
  (10, 0, 'camaro', 'Camaro', '', '\n', 8),
  (11, 0, 'benz-gl', 'Benz GL', '', '\n', 9);

-- 
-- Вывод данных для таблицы gram_catalogpro_char_select
--
INSERT INTO gram_catalogpro_char_select VALUES 
  (3, 'Автомат', 0),
  (3, 'Механическая', 1),
  (4, 'Белый', 0),
  (4, 'Красный', 0),
  (4, 'Синий', 0),
  (4, 'Черный', 0),
  (4, 'Черный металлик', 0),
  (8, 'Бензин', 0),
  (8, 'Дизель', 0),
  (10, 'Внедорожник', 0),
  (10, 'Купе', 0),
  (10, 'Седан', 0),
  (10, 'Хэтчбек - 3d', 0),
  (10, 'Хэтчбек - 5d', 0);

-- 
-- Вывод данных для таблицы gram_catalogpro_product
--
INSERT INTO gram_catalogpro_product VALUES 
  (1, 'audi-a4-2010', 'Ауди А4 2010', 'Ауди А4 2010, Audi А4 2010', '\n<p>Ауди А4 2010 \n<br></p>', 0, 0, 0.00, 7, 0, 0, 0, 0, '2012-03-29 22:31:38', 3, 5),
  (2, 'toyota-highlander', 'Toyota Highlander', 'Toyota Highlander', '\n<ul>                \n\t<li>Антиблокировочная система (ABS)</li>                \n\t<li>Антипробуксовочная система</li>                \n\t<li>Бортовой компьютер</li>                \n\t<li>Датчик дождя</li>                \n\t<li>Датчик света</li>                \n\t<li>Кондиционер (климат 3-зонный и выше)</li>                \n\t<li>Круиз-контроль</li>                \n\t<li>Ксеноновые фары</li>                \n\t<li>Легкосплавные диски</li>                \n\t<li>Люк</li>                \n\t<li>Мультимедиа (CD+NAVI)</li>                \n\t<li>Обогрев зеркал</li>                \n\t<li>Омыватель фар</li>                \n\t<li>Охранная система</li>                \n\t<li>Парктроник</li>                \n\t<li>Подушки безопасности (фронтальные + боковые)</li>                \n\t<li>Рег-ка сиденья водителя (с памятью)</li>                \n\t<li>Рег-ка сиденья пассажира (электропривод)</li>                \n\t<li>Регулировка руля (электро)</li>                \n\t<li>Салон (кожа)</li>                \n\t<li>Система курсовой стабилизации</li>                \n\t<li>Тонированные стекла</li>                \n\t<li>Усилитель рулевого управления (гидро)</li>                \n\t<li>Цвет салона (темный)</li>                \n\t<li>Центральный замок</li>                \n\t<li>Электрозеркала</li>                \n\t<li>Электростеклоподъемники (все)</li>                \n\t<li>Покупалась у дилера, по ПТС 2010 г.в.,сервисная книжка, все ТО у дилера.  Самая полная комплектация "Люкс плюс". Без аварий, не крашеная.</li>            \n</ul>', 0, 0, 0.00, 5, 0, 0, 0, 0, '2012-03-29 22:31:38', 5, 6),
  (5, 'porsche-cayman', 'Porsche Cayman', '', '\n<p> Антиблокировочная система (ABS)\n<br> Антипробуксовочная система\n<br> Бортовой компьютер\n<br> Датчик дождя\n<br> Датчик света\n<br> Кондиционер (климат 2-зонный)\n<br> Круиз-контроль\n<br> Ксеноновые фары\n<br> Легкосплавные диски\n<br> Мультимедиа (CD)\n<br> Обогрев зеркал\n<br> Обогрев сидений\n<br> Омыватель фар\n<br>\n<br></p>', 0, 0, 0.00, 6, 0, 0, 1, 0, '2012-03-29 22:31:38', 8, 7),
  (6, 'bmw-x6', 'BMW X6', '', '\n<ul> \n <li> Антиблокировочная система (ABS)</li> \n <li> Антипробуксовочная система</li> \n <li> Бортовой компьютер</li> \n <li> Датчик дождя</li> \n <li> Датчик света</li> \n <li> Кондиционер (климат 3-зонный и выше)</li> \n <li> Круиз-контроль</li> \n <li> Ксеноновые фары</li> \n <li> Легкосплавные диски</li> \n <li> Люк</li> \n <li> Мультимедиа (CD)</li> \n <li> Обогрев зеркал</li> \n <li> Обогрев сидений</li> \n <li> Омыватель фар</li> \n <li> Охранная система</li> \n <li> Парктроник</li> \n <li> Подушки безопасности (фронтальные + боковые)</li> \n <li> Рег-ка сиденья водителя (с памятью)</li> \n <li> Рег-ка сиденья пассажира (электропривод)</li> \n <li> Регулировка руля (электро)</li> \n <li> Салон (кожа)</li> \n <li> Система курсовой стабилизации</li> \n <li> Тонированные стекла</li> \n <li> Усилитель рулевого управления (гидро)</li> \n <li> Цвет салона (светлый)</li> \n <li> Центральный замок</li> \n <li> Электрозеркала</li> \n <li> Электростеклоподъемники (все)</li>\n</ul>\n<p>\n<br></p>', 0, 0, 0.00, 4, 1, 0, 1, 0, '2012-03-29 22:31:38', 9, 2),
  (7, 'chevrolet-camaro-1', 'Chevrolet Camaro', '', '\n<ul>\n\t<li>Антиблокировочная система (ABS)</li>\n\t<li>Антипробуксовочная система</li>\n\t<li>Бортовой компьютер</li>\n\t<li>Датчик дождя</li>\n\t<li>Датчик света</li>\n\t<li>Кондиционер (да)</li>\n\t<li>Ксеноновые фары</li>\n\t<li>Легкосплавные диски</li>\n\t<li>Мультимедиа (CD)</li>\n\t<li>Обогрев зеркал</li>\n\t<li>Обогрев сидений</li>\n</ul>\n<ul>\n\t<li>Охранная система</li>\n\t<li>Подушки безопасности (фронтальные + боковые)</li>\n\t<li>Рег-ка сиденья водителя (электропривод)</li>\n\t<li>Рег-ка сиденья пассажира (электропривод)</li>\n\t<li>Салон (кожа)</li>\n\t<li>Система курсовой стабилизации</li>\n\t<li>Усилитель рулевого управления (гидро)</li>\n\t<li>Цвет салона (темный)</li>\n\t<li>Центральный замок</li>\n\t<li>Электрозеркала</li>\n\t<li>Электростеклоподъемники (все)</li>\n</ul>', 0, 0, 0.00, 1, 0, 0, 0, 0, '2012-03-29 22:31:38', 10, 8),
  (9, 'mercedes-benz-gl', 'Mercedes Benz GL', '', '\n<ul>\n\t<li>Антиблокировочная система (ABS)</li>\n\t<li>Антипробуксовочная система</li>\n\t<li>Бортовой компьютер</li>\n\t<li>Датчик дождя</li>\n\t<li>Датчик света</li>\n\t<li>Кондиционер (климат 3-зонный и выше)</li>\n\t<li>Круиз-контроль</li>\n\t<li>Легкосплавные диски</li>\n\t<li>Люк</li>\n\t<li>Мультимедиа (CD)</li>\n\t<li>Обогрев зеркал</li>\n\t<li>Обогрев сидений</li>\n\t<li>Омыватель фар</li>\n\t<li>Охранная система</li>\n</ul>\n<ul>\n\t<li>Парктроник</li>\n\t<li>Подушки безопасности (фронтальные + боковые)</li>\n\t<li>Рег-ка сиденья водителя (электропривод)</li>\n\t<li>Рег-ка сиденья пассажира (электропривод)</li>\n\t<li>Регулировка руля (электро)</li>\n\t<li>Салон (кожа)</li>\n\t<li>Система курсовой стабилизации</li>\n\t<li>Тонированные стекла</li>\n\t<li>Усилитель рулевого управления (гидро)</li>\n\t<li>Цвет салона (светлый)</li>\n\t<li>Центральный замок</li>\n\t<li>Электрозеркала</li>\n\t<li>Электростеклоподъемники (все)</li>\n</ul>', 0, 0, 0.00, 28, 0, 1, 0, 0, '2012-03-29 22:31:38', 11, 9),
  (10, 'mercedes-benz-gl-1', 'Mercedes Benz GL', '', '\n<ul>\n\t<li>Антиблокировочная система (ABS)</li>\n\t<li>Антипробуксовочная система</li>\n\t<li>Бортовой компьютер</li>\n\t<li>Датчик дождя</li>\n\t<li>Датчик света</li>\n\t<li>Кондиционер (климат 3-зонный и выше)</li>\n\t<li>Круиз-контроль</li>\n\t<li>Легкосплавные диски</li>\n\t<li>Люк</li>\n\t<li>Мультимедиа (CD)</li>\n\t<li>Обогрев зеркал</li>\n\t<li>Обогрев сидений</li>\n\t<li>Омыватель фар</li>\n\t<li>Охранная система</li>\n</ul>\n<ul>\n\t<li>Парктроник</li>\n\t<li>Подушки безопасности (фронтальные + боковые)</li>\n\t<li>Рег-ка сиденья водителя (электропривод)</li>\n\t<li>Рег-ка сиденья пассажира (электропривод)</li>\n\t<li>Регулировка руля (электро)</li>\n\t<li>Салон (кожа)</li>\n\t<li>Система курсовой стабилизации</li>\n\t<li>Тонированные стекла</li>\n\t<li>Усилитель рулевого управления (гидро)</li>\n\t<li>Цвет салона (светлый)</li>\n\t<li>Центральный замок</li>\n\t<li>Электрозеркала</li>\n\t<li>Электростеклоподъемники (все)</li>\n</ul>', 0, 0, 0.00, 0, 0, 1, 0, 0, '2012-03-29 22:31:38', 11, 9),
  (11, 'mercedes-benz-gl-3', 'Mercedes Benz GL', '', '\n<ul>\n\t<li>Антиблокировочная система (ABS)</li>\n\t<li>Антипробуксовочная система</li>\n\t<li>Бортовой компьютер</li>\n\t<li>Датчик дождя</li>\n\t<li>Датчик света</li>\n\t<li>Кондиционер (климат 3-зонный и выше)</li>\n\t<li>Круиз-контроль</li>\n\t<li>Легкосплавные диски</li>\n\t<li>Люк</li>\n\t<li>Мультимедиа (CD)</li>\n\t<li>Обогрев зеркал</li>\n\t<li>Обогрев сидений</li>\n\t<li>Омыватель фар</li>\n\t<li>Охранная система</li>\n</ul>\n<ul>\n\t<li>Парктроник</li>\n\t<li>Подушки безопасности (фронтальные + боковые)</li>\n\t<li>Рег-ка сиденья водителя (электропривод)</li>\n\t<li>Рег-ка сиденья пассажира (электропривод)</li>\n\t<li>Регулировка руля (электро)</li>\n\t<li>Салон (кожа)</li>\n\t<li>Система курсовой стабилизации</li>\n\t<li>Тонированные стекла</li>\n\t<li>Усилитель рулевого управления (гидро)</li>\n\t<li>Цвет салона (светлый)</li>\n\t<li>Центральный замок</li>\n\t<li>Электрозеркала</li>\n\t<li>Электростеклоподъемники (все)</li>\n</ul>', 0, 0, 0.00, 4, 0, 1, 1, 0, '2012-03-29 22:31:38', 11, 9),
  (12, 'dsfgdsg', 'Mercedes Benz GL  sdfdsf w433 dsfgdsg', 'dfgf', '\n<p>dfgdsfg</p>', 0, 0, 0.00, 7, 0, 1, 0, 0, '2012-03-29 22:31:38', 11, 9),
  (13, 't78969', 'Mercedes Benz GL dsfdsf sdf t78969', '68796789', '\n<p>67896789</p>', 0, 0, 0.00, 1, 0, 0, 0, 0, '2012-03-28 22:31:38', 11, 9);

-- 
-- Вывод данных для таблицы gram_catalogpro_char_value
--
INSERT INTO gram_catalogpro_char_value VALUES 
  (2, 1, '2008'),
  (5, 1, '2008'),
  (6, 1, '2008'),
  (9, 1, '2008'),
  (10, 1, '2008'),
  (11, 1, '2008'),
  (1, 1, '2010'),
  (7, 1, '2010'),
  (12, 1, '2010'),
  (13, 1, '2011'),
  (1, 2, '10000'),
  (5, 2, '32000'),
  (6, 2, '32000'),
  (12, 2, '55555'),
  (2, 2, '58000'),
  (7, 2, '60000'),
  (9, 2, '60000'),
  (10, 2, '60000'),
  (11, 2, '60000'),
  (13, 2, '88888'),
  (2, 3, 'Автомат'),
  (5, 3, 'Автомат'),
  (6, 3, 'Автомат'),
  (7, 3, 'Автомат'),
  (9, 3, 'Автомат'),
  (10, 3, 'Автомат'),
  (11, 3, 'Автомат'),
  (12, 3, 'Автомат'),
  (13, 3, 'Автомат'),
  (1, 3, 'Механическая'),
  (5, 4, 'Белый'),
  (6, 4, 'Белый'),
  (12, 4, 'Белый'),
  (13, 4, 'Белый'),
  (7, 4, 'Красный'),
  (1, 4, 'Синий'),
  (2, 4, 'Черный'),
  (9, 4, 'Черный'),
  (10, 4, 'Черный'),
  (11, 4, 'Черный металлик'),
  (13, 5, '10000'),
  (1, 5, '100000'),
  (12, 5, '100000'),
  (2, 5, '1399000'),
  (7, 5, '1549000'),
  (9, 5, '1930000'),
  (11, 5, '1930000'),
  (5, 5, '2150000'),
  (6, 5, '2150000'),
  (6, 6, '2979 см³/306 л.с./Бензин турбонаддув'),
  (11, 6, '2987 см³ / 211 л.с.'),
  (7, 6, '3564 см³ / 312 л.с. '),
  (1, 8, 'Бензин'),
  (2, 8, 'Бензин'),
  (6, 8, 'Бензин'),
  (7, 8, 'Бензин'),
  (9, 8, 'Бензин'),
  (10, 8, 'Бензин'),
  (11, 8, 'Бензин'),
  (12, 8, 'Бензин'),
  (13, 8, 'Бензин'),
  (5, 8, 'Дизель'),
  (1, 10, 'Внедорожник'),
  (2, 10, 'Внедорожник'),
  (6, 10, 'Внедорожник'),
  (9, 10, 'Внедорожник'),
  (10, 10, 'Внедорожник'),
  (11, 10, 'Внедорожник'),
  (12, 10, 'Внедорожник'),
  (13, 10, 'Внедорожник'),
  (5, 10, 'Купе'),
  (7, 10, 'Купе');

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;