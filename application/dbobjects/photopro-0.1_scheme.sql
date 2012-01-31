SELECT 1;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;

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
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE CASCADE ON UPDATE CASCADE
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
  category_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Идетнификатор категории',
  user_id CHAR(22) NOT NULL COMMENT 'Идентификатор пользователя',
  PRIMARY KEY (page_id, page_url),
  INDEX IX_{db_dbprefix}page_category_id (category_id),
  INDEX user_page (user_id),
  CONSTRAINT category_page FOREIGN KEY (category_id)
    REFERENCES {db_dbprefix}category(category_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT user_page FOREIGN KEY (user_id)
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE CASCADE ON UPDATE CASCADE
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
    REFERENCES {db_dbprefix}user(user_uniqid) ON DELETE CASCADE ON UPDATE CASCADE
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

INSERT INTO {db_dbprefix}user VALUES ('4ee835b19afdc600047933', 'granit-reka@yandex.ru', '0a235e36c91e48506ded579f56132caa', 'Бегека', 'Наталья', '', 0, '<p>Фотография для меня - это нечто особенное. С того дня когда я сделала свой первый снимок и по настоящий момент, фотография остается для меня маленьким чудом. Это чудо, сохранять моменты и эмоции, лица и события, места и приключения.....</p>\r\n<p>Каждый кадр, сделанный мной, таит за собой большой труд, огромную любовь и просто кучу положительной энергии, которые я вкладываю в фотосессии.</p>', NULL, 1, '2011-12-14 09:35:45');

INSERT INTO {db_dbprefix}gallery VALUES (1, '9-mesyatsev', '9 месяцев', '9 месяцев,беременность фото,беременные мамашки', '9 месяцев в ожидании чуда!!!<br/>', NULL, '4ee835b19afdc600047933'), (2, 'malishi-chigrishi', 'Малыши-чигрыши', 'малыши-чигрыши,малыши,самые маленькие', 'Ох, уж эти детишки)))<br/>', NULL, '4ee835b19afdc600047933'), (3, 'nashi-detki', 'Наши детки', 'Наши детки', 'Никто так искринне и чисто не может радоваться жизни, как наши дети...', NULL, '4ee835b19afdc600047933');

INSERT INTO {db_dbprefix}image VALUES ('4ee8363f2fa39', 'avatar_4ee835b19afdc600047933.jpg', 'avatar_4ee835b19afdc6000479333f2fa39.jpg', 200, 343, 26996, NULL, '4ee835b19afdc600047933'), ('4ee83f2b728a4', 'foto-1-hkicft.jpg', 'foto-1-hkicft2b728a4.jpg', 1140, 761, 122739, 1, '4ee835b19afdc600047933'), ('4ee83f2c39e24', 'foto-120-ftiuiq.jpg', 'foto-120-ftiuiq2c39e24.jpg', 1140, 761, 54490, 1, '4ee835b19afdc600047933'), ('4ee83f2d95024', 'foto-215-ajccmp.jpg', 'foto-215-ajccmp2d95024.jpg', 527, 790, 45125, 1, '4ee835b19afdc600047933'), ('4ee83f2e2971e', 'img_5722-wivrlr.jpg', 'img_5722-wivrlr2e2971e.jpg', 527, 790, 80416, 1, '4ee835b19afdc600047933'), ('4ee83f2f7c8a2', 'img_6018-tmotka.jpg', 'img_6018-tmotka2f7c8a2.jpg', 527, 790, 93630, 1, '4ee835b19afdc600047933'), ('4ee83f303d541', 'img_6032-tpnhvt.jpg', 'img_6032-tpnhvt303d541.jpg', 527, 790, 78526, 1, '4ee835b19afdc600047933'), ('4ee83f31433a0', 'img_6380-jxpekh.jpg', 'img_6380-jxpekh31433a0.jpg', 527, 790, 108215, 1, '4ee835b19afdc600047933'), ('4ee83f3226bd1', 'img_6539-fcwuww.jpg', 'img_6539-fcwuww3226bd1.jpg', 527, 790, 68064, 1, '4ee835b19afdc600047933'), ('4ee83f33207c6', 'img_6595-uoluws.jpg', 'img_6595-uoluws33207c6.jpg', 1140, 761, 171683, 1, '4ee835b19afdc600047933'), ('4ee83f343db6c', 'img_7523-kknytp.jpg', 'img_7523-kknytp343db6c.jpg', 542, 790, 64071, 1, '4ee835b19afdc600047933'), ('4ee843181db18', '9-midayy.jpg', '9-midayy181db18.jpg', 527, 790, 134873, 2, '4ee835b19afdc600047933'), ('4ee843192c485', 'arseniy-6-wprrag.jpg', 'arseniy-6-wprrag192c485.jpg', 520, 790, 56035, 2, '4ee835b19afdc600047933'), ('4ee8431a2ad88', 'foto-30-myicxk.jpg', 'foto-30-myicxk1a2ad88.jpg', 1140, 761, 179602, 2, '4ee835b19afdc600047933'), ('4ee8431ae07d5', 'foto-206-1-obyxor.jpg', 'foto-206-1-obyxor1ae07d5.jpg', 1140, 784, 129559, 2, '4ee835b19afdc600047933'), ('4ee8431b975c7', 'img_0105-fklwng.jpg', 'img_0105-fklwng1b975c7.jpg', 527, 790, 73590, 2, '4ee835b19afdc600047933'), ('4ee8431ccfe7c', 'img_1313-bqrmfj.jpg', 'img_1313-bqrmfj1ccfe7c.jpg', 1140, 738, 129365, 2, '4ee835b19afdc600047933'), ('4ee8431d9f4ee', 'img_1330-oggtnw.jpg', 'img_1330-oggtnw1d9f4ee.jpg', 1140, 761, 99582, 2, '4ee835b19afdc600047933'), ('4ee8431ec4a42', 'img_1369-pahkxm.jpg', 'img_1369-pahkxm1ec4a42.jpg', 527, 790, 78234, 2, '4ee835b19afdc600047933'), ('4ee8431f5b198', 'img_1450-xoyyst.jpg', 'img_1450-xoyyst1f5b198.jpg', 1063, 790, 132670, 2, '4ee835b19afdc600047933'), ('4ee8432011072', 'img_6043-gskwns.jpg', 'img_6043-gskwns2011072.jpg', 1140, 761, 87521, 2, '4ee835b19afdc600047933'), ('4ee84320c7b01', 'img_6337-2-ehtdki.jpg', 'img_6337-2-ehtdki20c7b01.jpg', 527, 790, 108515, 2, '4ee835b19afdc600047933'), ('4ee843216e267', 'img_6372-mowtfc.jpg', 'img_6372-mowtfc216e267.jpg', 527, 790, 115523, 2, '4ee835b19afdc600047933'), ('4ee8432219531', 'img_6593-dugulq.jpg', 'img_6593-dugulq2219531.jpg', 527, 790, 61572, 2, '4ee835b19afdc600047933'), ('4ee84322c5498', 'img_61402-asysuw.jpg', 'img_61402-asysuw22c5498.jpg', 1140, 782, 81242, 2, '4ee835b19afdc600047933'), ('4ee844f2137c1', 'img_0493-hvdemm.jpg', 'img_0493-hvdemmf2137c1.jpg', 1140, 760, 128047, 3, '4ee835b19afdc600047933'), ('4ee8455437ff6', 'foto-59-hvybvx.jpg', 'foto-59-hvybvx5437ff6.jpg', 1140, 760, 125383, 3, '4ee835b19afdc600047933'), ('4ee8455586f6d', 'foto-100-ihkwhf.jpg', 'foto-100-ihkwhf5586f6d.jpg', 1140, 760, 129602, 3, '4ee835b19afdc600047933'), ('4ee8455659f6c', 'img_0045-lntnji.jpg', 'img_0045-lntnji5659f6c.jpg', 1140, 760, 87782, 3, '4ee835b19afdc600047933'), ('4ee84557300d1', 'img_7022-txuebx.jpg', 'img_7022-txuebx57300d1.jpg', 1126, 790, 151691, 3, '4ee835b19afdc600047933'), ('4ee845587a037', 'img_7064-mvtbun.jpg', 'img_7064-mvtbun587a037.jpg', 1140, 760, 152314, 3, '4ee835b19afdc600047933'), ('4ee84559a46e0', 'img_7291-ahcuut.jpg', 'img_7291-ahcuut59a46e0.jpg', 1117, 790, 189443, 3, '4ee835b19afdc600047933');

INSERT INTO {db_dbprefix}tag VALUES ('ангел', '4ee83f2b728a4'), ('беременность', '4ee83f2b728a4'), ('будущая мама', '4ee83f2b728a4'), ('двое', '4ee83f2c39e24'), ('в ожидании чуда', '4ee83f2c39e24'), ('беременность', '4ee83f2d95024'), ('красный', '4ee83f2d95024'), ('фоторамка', '4ee83f2e2971e'), ('двое', '4ee83f2e2971e'), ('беременность', '4ee83f2e2971e'), ('беременность', '4ee83f2f7c8a2'), ('двое', '4ee83f2f7c8a2'), ('беременность', '4ee83f303d541'), ('беременность', '4ee83f31433a0'), ('фоторамка', '4ee83f31433a0'), ('семья', '4ee83f3226bd1'), ('беременность', '4ee83f3226bd1'), ('уют', '4ee83f3226bd1'), ('розы', '4ee83f33207c6'), ('красный', '4ee83f33207c6'), ('беременность', '4ee83f33207c6'), ('беременность', '4ee83f343db6c'), ('футболист', '4ee843181db18'), ('малыш', '4ee843181db18'), ('малыш', '4ee843192c485'), ('смех', '4ee843192c485'), ('ангел', '4ee843192c485'), ('дети', '4ee8431a2ad88'), ('праздник', '4ee8431a2ad88'), ('день рождения', '4ee8431a2ad88'), ('малыши', '4ee8431ae07d5'), ('малыш', '4ee8431b975c7'), ('олененок', '4ee8431b975c7'), ('маленькая мудмазель', '4ee8431ccfe7c'), ('нежность', '4ee8431d9f4ee'), ('малыш', '4ee8431ec4a42'), ('ох я и не выспалася', '4ee8431ec4a42'), ('малыш', '4ee8431f5b198'), ('сон', '4ee8431f5b198'), ('малыш', '4ee8432011072'), ('маленькая пчелка', '4ee8432011072'), ('пчелка', '4ee8432011072'), ('малыш', '4ee84320c7b01'), ('мышонок', '4ee84320c7b01'), ('маленький мышонок', '4ee84320c7b01'), ('малыш', '4ee843216e267'), ('маленький зайчонок', '4ee843216e267'), ('малыш', '4ee8432219531'), ('ух ты', '4ee8432219531'), ('малыш', '4ee84322c5498'), ('роза', '4ee84322c5498'), ('сон', '4ee84322c5498'), ('джентельмен', '4ee844f2137c1'), ('роза', '4ee844f2137c1'), ('мальчик', '4ee844f2137c1'), ('джентельмен', '4ee8455437ff6'), ('братья', '4ee8455437ff6'), ('роза', '4ee8455437ff6'), ('джентельмен', '4ee8455586f6d'), ('роза', '4ee8455586f6d'), ('девочка', '4ee8455659f6c'), ('снежинка', '4ee8455659f6c'), ('юный фотограф', '4ee84557300d1'), ('девочка', '4ee84557300d1'), ('семья', '4ee845587a037'), ('девочка', '4ee84559a46e0'), ('поле', '4ee84559a46e0'), ('полевые цветы', '4ee84559a46e0'), ('венок', '4ee84559a46e0');



/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;