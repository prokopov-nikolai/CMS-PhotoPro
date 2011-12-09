SELECT 1;

DROP TABLE IF EXISTS {db_dbprefix}category;
CREATE TABLE {db_dbprefix}category (
  category_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор категории',
  category_url VARCHAR(255) NOT NULL COMMENT 'Часть урла категории',
  category_title VARCHAR(255) NOT NULL COMMENT 'Название категории',
  category_keywords VARCHAR(255) DEFAULT NULL COMMENT 'Ключевики для категории',
  category_description TEXT DEFAULT NULL COMMENT 'Описание категории',
  PRIMARY KEY (category_id, category_url),
  UNIQUE INDEX category_id (category_id),
  UNIQUE INDEX category_url (category_url)
)
ENGINE = {db_table_type}
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 16384
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}log;
CREATE TABLE {db_dbprefix}log (
  `key` VARCHAR(255) DEFAULT NULL,
  value VARCHAR(255) DEFAULT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 168
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}sessions;
CREATE TABLE {db_dbprefix}sessions (
  session_id VARCHAR(40) NOT NULL DEFAULT '0',
  ip_address VARCHAR(16) NOT NULL DEFAULT '0',
  user_agent VARCHAR(120) NOT NULL,
  last_activity INT(10) UNSIGNED NOT NULL DEFAULT 0,
  user_data TEXT DEFAULT NULL,
  PRIMARY KEY (session_id),
  INDEX last_activity_idx (last_activity)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 132
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}user;
CREATE TABLE {db_dbprefix}user (
  user_uniqid CHAR(22) NOT NULL COMMENT 'Идентификатор пользователя',
  user_email VARCHAR(255) NOT NULL COMMENT 'email пользователя',
  user_password VARCHAR(50) NOT NULL COMMENT 'Пароль в md5',
  user_last_name VARCHAR(255) DEFAULT NULL COMMENT 'Фамилия',
  user_first_name VARCHAR(255) DEFAULT NULL COMMENT 'Имя пользователя',
  user_patronymic VARCHAR(255) DEFAULT NULL COMMENT 'Отчество',
  user_sex TINYINT(1) UNSIGNED DEFAULT NULL COMMENT 'Пол пользователя (0 - ж, 1 - м)',
  user_about TEXT DEFAULT NULL COMMENT 'О себе',
  user_site VARCHAR(255) DEFAULT NULL COMMENT 'Сайт',
  user_subscribe TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Подписка на новости',
  user_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  PRIMARY KEY (user_uniqid),
  UNIQUE INDEX `email-pass` (user_email, user_password)
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 5461
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat}
COMMENT = 'Таблица всех пользователей';

DROP TABLE IF EXISTS {db_dbprefix}vote;
CREATE TABLE {db_dbprefix}vote (
  vote_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор голосования',
  vote_question TEXT NOT NULL COMMENT 'Вопрос',
  vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата добавления',
  user_uniqid CHAR(22) NOT NULL COMMENT 'Идентификатор пользователя',
  PRIMARY KEY (vote_id)
)
ENGINE = {db_table_type}
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 16384
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}gallery;
CREATE TABLE {db_dbprefix}gallery (
  gallery_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор галереи',
  gallery_url VARCHAR(255) NOT NULL COMMENT 'Часть урла галереи',
  gallery_title VARCHAR(255) NOT NULL COMMENT 'Название галереи',
  gallery_keywords VARCHAR(255) DEFAULT NULL COMMENT 'Ключевые слова',
  gallery_description TEXT DEFAULT NULL COMMENT 'Описание галереи',
  gallery_cover_id VARCHAR(13) DEFAULT NULL COMMENT 'Идентификатор обложки галереи',
  user_uniqid VARCHAR(22) NOT NULL COMMENT 'Индентификатор пользователя',
  PRIMARY KEY (gallery_id, gallery_url),
  UNIQUE INDEX gallery_id (gallery_id),
  UNIQUE INDEX gallery_url (gallery_url),
  INDEX user_gallery (user_uniqid),
  CONSTRAINT user_gallery FOREIGN KEY (user_uniqid)
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = {db_table_type}
AUTO_INCREMENT = 9
AVG_ROW_LENGTH = 4096
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}page;
CREATE TABLE {db_dbprefix}page (
  page_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор страницы',
  page_url VARCHAR(255) NOT NULL,
  page_content TEXT DEFAULT NULL COMMENT 'Текст страницы',
  page_title VARCHAR(255) DEFAULT NULL COMMENT 'Заголовок страницы',
  page_keywords VARCHAR(255) DEFAULT NULL COMMENT 'Ключевые слова для страницы',
  page_description VARCHAR(255) DEFAULT NULL COMMENT 'Описание для страницы',
  page_date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания страницы',
  page_date_modified DATETIME DEFAULT NULL COMMENT 'Время обновления записи',
  category_id INT(11) UNSIGNED NOT NULL COMMENT 'Идетнификатор категории',
  user_id CHAR(22) NOT NULL COMMENT 'Идентификатор пользователя',
  PRIMARY KEY (page_id, page_url),
  INDEX IX_{db_dbprefix}page_category_id (category_id),
  INDEX user_page (user_id),
  CONSTRAINT category_page FOREIGN KEY (category_id)
    REFERENCES {db_dbprefix}category(category_id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT user_page FOREIGN KEY (user_id)
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = {db_table_type}
AUTO_INCREMENT = 1
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}user_admin;
CREATE TABLE {db_dbprefix}user_admin (
  user_uniqid CHAR(22) NOT NULL COMMENT 'Идентификатор пользователя администратора',
  PRIMARY KEY (user_uniqid),
  UNIQUE INDEX user_id (user_uniqid),
  CONSTRAINT user_admin FOREIGN KEY (user_uniqid)
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 16384
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}vote_answer;
CREATE TABLE {db_dbprefix}vote_answer (
  answer_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор ответа',
  answer_text TEXT DEFAULT NULL COMMENT 'Текст ответа',
  vote_id INT(11) UNSIGNED NOT NULL COMMENT 'Идентификатор опроса',
  PRIMARY KEY (answer_id),
  INDEX FK_{db_dbprefix}vote_answer_{db_dbprefix}vote_vote_id (vote_id),
  CONSTRAINT FK_{db_dbprefix}vote_answer_{db_dbprefix}vote_vote_id FOREIGN KEY (vote_id)
    REFERENCES {db_dbprefix}vote(vote_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = {db_table_type}
AUTO_INCREMENT = 35
AVG_ROW_LENGTH = 5461
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}image;
CREATE TABLE {db_dbprefix}image (
  image_id CHAR(13) NOT NULL COMMENT 'Идентификатор картинки',
  image_name VARCHAR(255) DEFAULT NULL COMMENT 'Назнание файла в url',
  image_filename VARCHAR(255) NOT NULL COMMENT 'Название файла оригинала картинки',
  image_width SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Ширина изображения',
  image_height SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Высота изображения',
  image_size INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Размер изображения',
  gallery_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Идентификатор галереи',
  user_uniqid VARCHAR(22) NOT NULL COMMENT 'Идентификатор пользователя',
  PRIMARY KEY (image_id),
  INDEX gallery_image (gallery_id),
  INDEX user_image (user_uniqid),
  CONSTRAINT gallery_image FOREIGN KEY (gallery_id)
    REFERENCES {db_dbprefix}gallery(gallery_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT user_image FOREIGN KEY (user_uniqid)
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 1024
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}vote_log;
CREATE TABLE {db_dbprefix}vote_log (
  answer_id INT(11) UNSIGNED NOT NULL,
  session_id VARCHAR(255) DEFAULT NULL,
  INDEX FK_{db_dbprefix}vote_log_{db_dbprefix}vote_answer_answer_id (answer_id),
  CONSTRAINT FK_{db_dbprefix}vote_log_{db_dbprefix}vote_answer_answer_id FOREIGN KEY (answer_id)
    REFERENCES {db_dbprefix}vote_answer(answer_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 1365
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};

DROP TABLE IF EXISTS {db_dbprefix}tag;
CREATE TABLE {db_dbprefix}tag (
  tag_name VARCHAR(255) NOT NULL COMMENT 'Сам тег',
  image_id CHAR(13) NOT NULL COMMENT 'Идентификатор картинки',
  INDEX image_id (image_id),
  INDEX tag_name (tag_name),
  CONSTRAINT image_id FOREIGN KEY (image_id)
    REFERENCES {db_dbprefix}image(image_id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = {db_table_type}
AVG_ROW_LENGTH = 963
CHARACTER SET {db_char_set}
COLLATE {db_dbcollat};