/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.21-MariaDB : Database - herogame
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`herogame` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `herogame`;

/*Table structure for table `banner` */

DROP TABLE IF EXISTS `banner`;

CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci,
  `link` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `priority` int(11) DEFAULT NULL,
  `type` smallint(1) DEFAULT NULL,
  `page` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) NOT NULL COMMENT '0 hide/1 show',
  `is_video` tinyint(4) DEFAULT '0',
  `video_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `banner` */

insert  into `banner`(`id`,`title`,`image`,`link`,`description`,`priority`,`type`,`page`,`status`,`is_video`,`video_url`) values (6,'banner chinh 1','59357f3ee4c20pmrKBhO4Kh-U-d3UvgmnKHBcQvsCtqON.jpg','banner-chinh',NULL,NULL,1,'',1,0,''),(7,'banner chinh 2','59358164eaeccAa0FSHIGZ3hXGzb_XxUOT1Ydpkyk3mAK.jpg','',NULL,NULL,1,'',1,0,''),(8,'banner left','593587d411508bannerphu1.jpg','',NULL,NULL,2,'',1,0,''),(9,'banner giữa','59358b1058637bannerphu3.jpg','',NULL,NULL,3,'',1,0,''),(10,'banner right','59358b65c479ebannerphu2.jpg','',NULL,NULL,4,'',1,0,''),(11,'banner left 2','59358a6797fdcbannerphu2.jpg','',NULL,NULL,2,'',1,1,'https://www.youtube.com/watch?v=RRQDqurZJNk'),(12,'banner chan tran','593eb1d1a9e94bannerphu1.jpg','',NULL,NULL,5,'',1,0,''),(13,'banner footer duoi','593eb1e19b36fbannerphu3.jpg','',NULL,NULL,6,'',1,0,''),(14,'banner chính video','5941178bc9f8bquad_damage-1920x1200.jpg','',NULL,NULL,1,'0',1,1,'https://www.youtube.com/watch?v=LtKWvqLj16Q');

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `image` text COLLATE utf8_unicode_ci,
  `icon` text COLLATE utf8_unicode_ci,
  `icon_hover` text COLLATE utf8_unicode_ci,
  `meta_description` text COLLATE utf8_unicode_ci,
  `title_page` text COLLATE utf8_unicode_ci,
  `keyword` text COLLATE utf8_unicode_ci,
  `priority` int(11) DEFAULT NULL,
  `status` smallint(1) DEFAULT '0',
  `url_slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url_menu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `show_in_menu` int(1) DEFAULT NULL,
  `og_url` text COLLATE utf8_unicode_ci,
  `og_title` text COLLATE utf8_unicode_ci,
  `og_description` text COLLATE utf8_unicode_ci,
  `og_site_name` text COLLATE utf8_unicode_ci,
  `og_image` text COLLATE utf8_unicode_ci,
  `type_of_category` int(11) DEFAULT NULL COMMENT '1: Category for post, 2 category for product',
  `parent_category` int(11) DEFAULT NULL COMMENT 'category id parent',
  `level_category` int(1) DEFAULT '0' COMMENT 'level of category - 0 is parennt. 1 child 1, 2 child 2, max 2',
  `show_in_home_cate_page` smallint(1) DEFAULT '0',
  `show_list_product_home_page` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `category` */

insert  into `category`(`id`,`name`,`description`,`image`,`icon`,`icon_hover`,`meta_description`,`title_page`,`keyword`,`priority`,`status`,`url_slug`,`url_menu`,`show_in_menu`,`og_url`,`og_title`,`og_description`,`og_site_name`,`og_image`,`type_of_category`,`parent_category`,`level_category`,`show_in_home_cate_page`,`show_list_product_home_page`) values (4,'Máy Chơi Game',NULL,'/full/562017212719_3938359356a47078ca7.74238264_maychoigame.jpg','/full/972017121739_446995961bc738f28a4.80796582_game-b-24x24.png','/full/972017121739_614385961bc738f6716.07109898_game-w-24x24.png','máy chơi game','máy chơi game','may choi game',0,1,'may-choi-game','',0,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0),(5,'Phụ kiện game nintendo',NULL,'/full/56201721289_8559359356a790c5400.53966069_phukiengame.jpg','/full/56201721289_8133659356a790c8ea3.69922498_phukiengame.jpg',NULL,'','','',0,1,'phu-kien-game-nintendo','',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0),(6,'Phụ kiện game Sony',NULL,'/full/56201721290_948859356aac6fbdf8.08505303_sony.jpg','/full/56201721290_2654459356aac6ff856.36772413_sony.jpg',NULL,'','','',0,1,'phu-kien-game-sony','',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0),(7,'Thẻ Nhớ',NULL,'/full/562017212929_6536659356ac9435080.86081801_thenho.jpg','/full/562017212929_8283759356ac9438808.81506622_thenho.jpg',NULL,'','','',0,1,'the-nho','',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,0),(8,'Nintendo Switch',NULL,'/full/56201721320_5439859356b6068a6d3.27379329_bundle-gray-box_570.jpg','/full/56201721320_7794859356b6068e238.16890739_bundle-gray-box_570.jpg',NULL,'','','',0,1,'nintendo-switch','',NULL,NULL,NULL,NULL,NULL,NULL,1,10,2,0,0),(9,'Nintendo New 3DS',NULL,'/full/562017213750_3945059356cbe0ca055.77865247_71klclzdgpl_588.jpg','/full/562017213750_7679559356cbe0cde36.35569377_71klclzdgpl_588.jpg',NULL,'','','',0,1,'nintendo-new-3ds','',NULL,NULL,NULL,NULL,NULL,NULL,1,10,2,0,0),(10,'Máy Chơi Game NinTendo',NULL,'/full/562017214012_3951759356d4c18e8d9.68833090_nintendo.png','/full/97201712160_357735961bc10ad4139.31911339_game-b-24x24.png','/full/97201712160_462745961bc10adfc28.25485164_game-w-24x24.png','','','',0,1,'may-choi-game-nintendo','may-choi-game',0,NULL,NULL,NULL,NULL,NULL,1,4,1,1,1);

/*Table structure for table `gallery` */

DROP TABLE IF EXISTS `gallery`;

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_image` text COLLATE utf8_unicode_ci,
  `status` smallint(1) DEFAULT NULL,
  `type` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `gallery` */

/*Table structure for table `media` */

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url_thumnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `media` */

insert  into `media`(`media_id`,`title`,`url`,`url_thumnail`,`type`,`created_at`,`updated_at`,`created_by`,`updated_by`,`status`) values (27,'Ảnh nền trang web','/full/972017132251_544105961cbbbdaf576.15409470_background.png','/full/972017132251_544105961cbbbdaf576.15409470_background.png',1,'2017-07-09 13:22:51','2017-07-09 13:22:51','admin',NULL,1);

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_in_menu` int(1) DEFAULT '0',
  `parent_menu` int(11) DEFAULT NULL,
  `level` int(1) DEFAULT NULL,
  `priority` int(11) DEFAULT '0',
  `image_icon_hover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `menu` */

insert  into `menu`(`id`,`name`,`image_icon`,`url`,`image`,`show_in_menu`,`parent_menu`,`level`,`priority`,`image_icon_hover`) values (5,'Tin thị trường',NULL,'thi-truong',NULL,1,3,1,0,NULL),(3,'Tin Tức',NULL,'tin-tuc',NULL,1,0,0,0,NULL),(4,'Thông Tin',NULL,'thong-tin',NULL,1,3,1,0,NULL),(6,'Máy Chơi Game',NULL,'may-choi-game',NULL,1,0,0,0,NULL),(7,'Máy Chơi Game Cầm Tay',NULL,'may-choi-game-cam-tay',NULL,1,6,1,3,NULL),(8,'Đĩa Game',NULL,'dia-game',NULL,0,0,0,3,NULL);

/*Table structure for table `order` */

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total` float DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `phone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) DEFAULT '0',
  `is_pay` smallint(1) DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `promotion_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `score` int(11) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `order` */

insert  into `order`(`id`,`user_id`,`created_date`,`updated_date`,`updated_by`,`total`,`address`,`phone`,`email`,`status`,`is_pay`,`name`,`promotion_code`,`order_code`,`score`,`discount`) values (1,17,'2017-05-16 23:15:31','2017-05-16 23:15:31',NULL,6600000,'long an','01212188954','nguyenhoangphuongphuong@gmail.com',5,0,'phuong','PROMO','591b25a329a08',0,0);

/*Table structure for table `order_detail` */

DROP TABLE IF EXISTS `order_detail`;

CREATE TABLE `order_detail` (
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT '1',
  PRIMARY KEY (`id_order`,`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `order_detail` */

insert  into `order_detail`(`id_order`,`id_product`,`price`,`number`) values (1,1,3200000,2),(1,5,500000,1);

/*Table structure for table `permission` */

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `controller_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `permission` */

insert  into `permission`(`permission_id`,`permission_name`,`module_name`,`controller_name`,`action_name`) values (9,'View','site','banner','view'),(8,'Delete','site','category','delete'),(7,'Add','site','category','add'),(6,'Edit','site','category','edit'),(5,'View','site','category','view'),(4,'Delete','site','user','delete'),(3,'Add','site','user','add'),(2,'Edit','site','user','edit'),(1,'View','site','user','view'),(10,'Edit','site','banner','edit'),(11,'Add','site','banner','add'),(12,'Delete','site','banner','delete'),(13,'View','site','advertising','view'),(14,'Edit','site','advertising','edit'),(15,'Add','site','advertising','add'),(16,'Delete','site','advertising','delete'),(17,'View','site','banner-type','view'),(18,'Edit','site','banner-type','edit'),(19,'Add','site','banner-type','add'),(20,'Delete','site','banner-type','delete'),(21,'View','site','permission','view'),(22,'Edit','site','permission','edit'),(23,'Add','site','permission','add'),(24,'Delete','site','permission','delete'),(25,'View','site','post','view'),(26,'Edit','site','post','edit'),(27,'Add','site','post','add'),(28,'Delete','site','post','delete'),(29,'View','site','media','view'),(30,'Edit','site','media','edit'),(31,'Add','site','media','add'),(32,'Delete','site','media','delete'),(33,'View','site','menu-type','view'),(34,'Edit','site','menu-type','edit'),(35,'Add','site','menu-type','add'),(36,'Delete','site','menu-type','delete'),(37,'View','site','contact','view'),(38,'Edit','site','contact','edit'),(39,'Add','site','contact','add'),(40,'Delete','site','contact','delete'),(41,'View','site','gallery','view'),(42,'Edit','site','gallery','edit'),(43,'Add','site','gallery','add'),(44,'Delete','site','gallery','delete'),(45,'View','site','setting','view'),(46,'Edit','site','setting','edit'),(47,'Add','site','setting','add'),(48,'Delete','site','setting','delete'),(49,'View','site','comment','view'),(50,'Edit','site','comment','edit'),(51,'Delete','site','comment','delete'),(52,'View','site','contact-user','view'),(53,'Edit','site','contact-user','edit'),(54,'View','site','menu','view'),(55,'Edit','site','menu','edit'),(56,'Edit','site','approve-post','edit'),(57,'View','site','info','view'),(58,'Edit','site','info','edit'),(59,'Add','site','info','add'),(60,'Delete','site','info','delete'),(61,'View','site','group-category','view'),(62,'Edit','site','group-category','edit'),(63,'Add','site','group-category','add'),(64,'Delete','site','group-category','delete'),(65,'Add','site','menu','add'),(66,'Delete','site','menu','delete'),(67,'View','site','product','view'),(68,'Edit','site','product','edit'),(69,'Add','site','product','add'),(70,'Delete','site','product','delete'),(71,'View','site','xem-san-pham','view'),(72,'View','site','order','view'),(73,'Edit','site','order','edit'),(74,'Add','site','order','add'),(75,'Delete','site','order','delete'),(76,'View','site','promotion-code','view'),(77,'Edit','site','promotion-code','edit'),(78,'Add','site','promotion-code','add'),(79,'Delete','site','promotion-code','delete');

/*Table structure for table `post` */

DROP TABLE IF EXISTS `post`;

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `tag` text COLLATE utf8_unicode_ci,
  `summary` text COLLATE utf8_unicode_ci,
  `image_id` text COLLATE utf8_unicode_ci,
  `url_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) DEFAULT NULL,
  `og_url` text COLLATE utf8_unicode_ci,
  `og_title` text COLLATE utf8_unicode_ci,
  `og_description` text COLLATE utf8_unicode_ci,
  `og_site_name` text COLLATE utf8_unicode_ci,
  `og_image` text COLLATE utf8_unicode_ci,
  `description_meta` text COLLATE utf8_unicode_ci,
  `keyword_meta` text COLLATE utf8_unicode_ci,
  `relative_post` text COLLATE utf8_unicode_ci,
  `id_category` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `post` */

insert  into `post`(`post_id`,`title`,`content`,`tag`,`summary`,`image_id`,`url_name`,`priority`,`created_at`,`updated_at`,`created_by`,`updated_by`,`status`,`og_url`,`og_title`,`og_description`,`og_site_name`,`og_image`,`description_meta`,`keyword_meta`,`relative_post`,`id_category`,`id_menu`) values (5,'aaa','dsadas','qwewq','sadas','img_06_06_2017/585846_5936c60aa61f53.53058447.jpg','fsdsagfffasfd',NULL,'2016-09-19 22:14:42','2017-06-06 22:11:06','admin','admin',-1,'dsa','dqwe','ewq','eqwe','qwe','qwe',NULL,NULL,NULL,NULL),(7,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqw',NULL,'2016-09-19 22:18:02','2016-09-19 22:18:02','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqwdasd',NULL,'2016-09-19 22:19:03','2016-09-19 22:19:03','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqwdasdasd',NULL,'2016-09-19 22:20:24','2016-09-19 22:20:24','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqwdasdasdaaa',NULL,'2016-09-19 22:22:36','2016-09-19 22:22:36','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqwdasdasdaaaaaa',NULL,'2016-09-19 22:23:39','2016-09-19 22:23:39','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'asewq','ewqewqe',NULL,'qweqweqw',NULL,'qweqwdasdasdaaaaaadasd',NULL,'2016-09-19 22:24:28','2016-09-19 22:24:28','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'asewq','ewqewqe',NULL,'qweqweqw','14','qweqwdasdasdaaaaaadasdasdsd',NULL,'2016-09-19 22:25:06','2016-09-19 22:25:06','admin','admin',-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'bài viết mới','b&agrave;i viết mới',NULL,'b&agrave;i viết mới','img_06_06_2017/405823_5936c632ba30c7.32136268.jpg','bai-vit-mi',NULL,'2017-06-06 22:11:46','2017-06-06 22:11:46','admin','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `product` */

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `image` text COLLATE utf8_unicode_ci,
  `gallery` text COLLATE utf8_unicode_ci,
  `price` int(11) DEFAULT '0',
  `price_sales` int(11) DEFAULT '0',
  `tag` text COLLATE utf8_unicode_ci,
  `title_page` text COLLATE utf8_unicode_ci,
  `keyword` text COLLATE utf8_unicode_ci,
  `meta_description` text COLLATE utf8_unicode_ci,
  `priority` int(11) DEFAULT NULL,
  `url_product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_category` int(11) NOT NULL,
  `og_title` text COLLATE utf8_unicode_ci,
  `og_description` text COLLATE utf8_unicode_ci,
  `og_site_name` text COLLATE utf8_unicode_ci,
  `og_image` text COLLATE utf8_unicode_ci,
  `relative_product` text COLLATE utf8_unicode_ci,
  `new_product` smallint(1) DEFAULT NULL,
  `best_sell` smallint(1) DEFAULT NULL,
  `is_promotion` smallint(1) DEFAULT NULL,
  `status` smallint(1) DEFAULT '0',
  `og_url` text COLLATE utf8_unicode_ci,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `notice_message` text COLLATE utf8_unicode_ci,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'FFFFFF',
  `show_in_category_home_page` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `product` */

insert  into `product`(`id`,`title`,`description`,`content`,`image`,`gallery`,`price`,`price_sales`,`tag`,`title_page`,`keyword`,`meta_description`,`priority`,`url_product`,`id_category`,`og_title`,`og_description`,`og_site_name`,`og_image`,`relative_product`,`new_product`,`best_sell`,`is_promotion`,`status`,`og_url`,`updated_by`,`created_by`,`updated_date`,`created_date`,`notice_message`,`color`,`show_in_category_home_page`) values (9,'Nintendo Switch with gray joy-con','<h2>Nintendo Switch h&agrave;ng nhập khẩu (Gray)</h2>\r\n\r\n<p>Nếu bạn hỏi m&igrave;nh chọn m&aacute;y game n&agrave;o để cả gia đ&igrave;nh c&oacute; thể chơi chung với nhau th&igrave; m&igrave;nh sẽ n&oacute;i ngay: m&aacute;y của&nbsp;<a href=\"https://tinhte.vn/tags/nintendo/\" title=\"\">Nintendo</a>. Ở phi&ecirc;n bản mới nhất của họ l&agrave;&nbsp;<a href=\"https://tinhte.vn/tags/nintendo-switch/\" title=\"\">Nintendo Switch</a>&nbsp;th&igrave; yếu tố gia đ&igrave;nh vẫn được g&igrave;n giữ rất trọn vẹn, thể hiện qua c&aacute;c hạng mục gồm: nội dung vui vẻ, chơi được nhiều người, tay cầm nhỏ gọn v&agrave; c&oacute; nhiều t&iacute;nh năng hỗ trợ hai người chơi tương t&aacute;c với nhau, cực kỳ vui v&agrave; th&iacute;ch th&uacute;.</p>','<h2>Nintendo Switch h&agrave;ng nhập khẩu (Gray)</h2>\r\n\r\n<p>Nếu bạn hỏi m&igrave;nh chọn m&aacute;y game n&agrave;o để cả gia đ&igrave;nh c&oacute; thể chơi chung với nhau th&igrave; m&igrave;nh sẽ n&oacute;i ngay: m&aacute;y của&nbsp;<a href=\"https://tinhte.vn/tags/nintendo/\" title=\"\">Nintendo</a>. Ở phi&ecirc;n bản mới nhất của họ l&agrave;&nbsp;<a href=\"https://tinhte.vn/tags/nintendo-switch/\" title=\"\">Nintendo Switch</a>&nbsp;th&igrave; yếu tố gia đ&igrave;nh vẫn được g&igrave;n giữ rất trọn vẹn, thể hiện qua c&aacute;c hạng mục gồm: nội dung vui vẻ, chơi được nhiều người, tay cầm nhỏ gọn v&agrave; c&oacute; nhiều t&iacute;nh năng hỗ trợ hai người chơi tương t&aacute;c với nhau, cực kỳ vui v&agrave; th&iacute;ch th&uacute;.</p>\r\n\r\n<p>Trong hộp của Nintendo Switch gồm c&oacute;:</p>\r\n\r\n<ul>\r\n	<li>1 x m&aacute;y Nintendo Switch.</li>\r\n	<li>1 x dock sạc v&agrave; xuất h&igrave;nh ảnh sang TV.</li>\r\n	<li>1 x c&aacute;p HDMI.</li>\r\n	<li>1 x cục nguồn.</li>\r\n	<li>2 x tay cầm Joy-Con Left-Right (1 bộ).</li>\r\n	<li>2 x c&aacute;i grip t&iacute;ch hợp d&acirc;y đeo tay để gắn v&agrave;o 2 c&aacute;i Joy-Con.</li>\r\n	<li>1 x c&aacute;i khung để gắn 2 c&aacute;i Joy-Con lại với nhau v&agrave; biến th&agrave;nh một c&aacute;i gamepad lớn.<img alt=\"\" src=\"/ad-min/assets/js/libs/kcfinder/upload/images/bundle-gray-box_570.jpg\" style=\"width: 800px; height: 800px;\" /></li>\r\n</ul>','/full/562017214922_1673659356f7246bc81.13753523_bundle-gray-box_570.jpg','img_05_06_2017/685852_59356f724701f5.35584498.jpg',83000000,83000000,NULL,NULL,NULL,NULL,1,'nintendo-switch-with-gray-joy-con',10,NULL,NULL,NULL,NULL,NULL,0,1,0,1,NULL,'admin','admin','2017-06-05 21:49:22','2017-06-05 21:49:22',NULL,'FFFFFF',0),(10,'Nintendo Switch with neon blue and neon red joy‑con','<b>Nintendo Switch with neon blue and neon red joy‑con</b>','<h4>&nbsp;</h4>\r\n<b><i>Đ&aacute;nh gi&aacute; Nintendo Switch</i></b><br />\r\n<br />\r\n<b>Chủ nhật ng&agrave;y 12/3, l&ocirc; h&agrave;ng Nintendo Switch thứ 2 đ&atilde; cập bến&nbsp;<a href=\"local.herogame.vn/\" target=\"_blank\">Herogame</a>, sau khi l&ocirc; đầu ti&ecirc;n ra đi qu&aacute; nhanh trong&hellip; hai nốt nhạc. Ch&uacute;ng ta mới c&oacute; dịp cận cảnh v&agrave; chi&ecirc;m ngưỡng si&ecirc;u phẩm n&agrave;y.<br />\r\n<img alt=\"\" src=\"/ad-min/assets/js/libs/kcfinder/upload/images/DSC02048_zpsrc0up4se.jpg\" style=\"width: 1024px; height: 683px;\" /><br />\r\nNhư đ&atilde; biết,&nbsp;<a href=\"local.herogame.vn/\" target=\"_blank\">Nintendo Switch</a>&nbsp;l&agrave; chiếc m&aacute;y chơi game Console - lai giữa m&aacute;y chơi game cầm tay mới nhất của Nintendo. Điểm đặc trưng của&nbsp;<a href=\"local.herogame.vn/\" target=\"_blank\">Switch</a>&nbsp;l&agrave; độ tiện dụng cao, phục vụ chơi game di động, trong khi vẫn đ&aacute;p ứng được nhu cầu chơi game gia đ&igrave;nh sau khi bạn kết nối m&aacute;y với m&agrave;n h&igrave;nh TV lớn. C&ograve;n chưa kể đến khả năng lắp r&aacute;p độc quyền, cho ph&eacute;p tối đa tới 8 người c&ugrave;ng chơi tr&ecirc;n một m&aacute;y. C&oacute; thể n&oacute;i, Switch đưa đến một trải nghiệm chơi game mới lạ từ &ldquo;tương lai&rdquo; m&agrave; kh&oacute; c&oacute; một cỗ m&aacute;y n&agrave;o kh&aacute;c tr&ecirc;n thị trường c&oacute; thể đ&aacute;p ứng hoặc bắt chước.</b>','/full/562017215159_228365935700fe4fd71.71769855_nintendo-switch-console2-800x800.jpg','img_05_06_2017/221435_5935700fe53944.47928027.jpg,img_05_06_2017/138214_5935700fe574e1.34279203.jpg',8500000,8500000,NULL,NULL,NULL,NULL,NULL,'nintendo-switch-with-neon-blue-and-neon-red-joycon',10,NULL,NULL,NULL,NULL,NULL,0,1,0,1,NULL,'admin','admin','2017-06-05 21:51:59','2017-06-05 21:51:59',NULL,'FFFFFF',0),(11,'Game ONE PIECE Unlimited World R Deluxe Edition - Nintendo Switch','<b>Game ONE PIECE Unlimited World R Deluxe Edition - Nintendo Switch</b>','<b>Game ONE PIECE Unlimited World R Deluxe Edition - Nintendo Switch</b><br />\r\n&nbsp;','/full/562017215511_30145593570cfa84209.22468171_a15ybka4lkl_645.jpg','img_05_06_2017/641327_593570cfa87ed8.21004846.jpg',50000,50000,NULL,NULL,NULL,NULL,NULL,'game-one-piece-unlimited-world-r-deluxe-edition-nintendo-switch',5,NULL,NULL,NULL,NULL,NULL,0,0,1,1,NULL,'admin','admin','2017-06-07 17:53:51','2017-06-07 17:53:51',NULL,'FFFFFF',0),(12,'game Skylanders Imaginators Starter Pack - Nintendo Switch','game Skylanders Imaginators Starter Pack - Nintendo Switch','game Skylanders Imaginators Starter Pack - Nintendo Switch','/full/562017215619_29700593571138144c0.26023904_78-114-515-s08_644.jpg','img_05_06_2017/186920_59357113818770.19782995.jpg',1400000,1400000,NULL,NULL,NULL,NULL,NULL,'game-skylanders-imaginators-starter-pack-nintendo-switch',5,NULL,NULL,NULL,NULL,NULL,0,0,0,1,NULL,'admin','admin','2017-06-05 21:56:19','2017-06-05 21:56:19',NULL,'FFFFFF',0),(13,'Game ARMS US/JP - Nintendo Switch','<h1 itemprop=\"name\">Game ARMS US/JP - Nintendo Switch</h1>','<h1 itemprop=\"name\">Game ARMS US/JP - Nintendo Switch&#39;<img alt=\"\" src=\"/ad-min/assets/js/libs/kcfinder/upload/images/91suntgd0jl_639.jpg\" style=\"width: 800px; height: 800px;\" /></h1>','/full/562017221334_343445935751e975af0.88257502_91suntgd0jl_639.jpg','img_05_06_2017/718476_5935751e97a945.16998273.jpg',1500000,1500000,NULL,NULL,NULL,NULL,NULL,'game-arms-us-jp-nintendo-switch',5,NULL,NULL,NULL,NULL,NULL,1,0,0,1,NULL,'admin','admin','2017-06-05 22:13:34','2017-06-05 22:13:34',NULL,'FFFFFF',0),(14,'game Secret of Mana Collection - Nintendo Switch','<h1 itemprop=\"name\">game Secret of Mana Collection - Nintendo Switch</h1>','<h1 itemprop=\"name\">game Secret of Mana Collection - Nintendo Switch</h1>\r\n\r\n<h1 itemprop=\"name\"><strong>Đ&acirc;y l&agrave; bản kỉ niệm 25 năm game&nbsp;</strong><strong>Secret of Mana n&ecirc;n h&atilde;ng đ&atilde; l&agrave;m gộp chung bản 1 v&agrave; 2 l&ecirc;n hệ Nintendo switch</strong></h1>','/full/562017221546_20617593575a26b4fb2.88875676_71qlzpwcokl_636.jpg','img_05_06_2017/787506_593575a26bda02.59407677.jpg',1200000,1200000,NULL,NULL,NULL,NULL,NULL,'game-secret-of-mana-collection-nintendo-switch',5,NULL,NULL,NULL,NULL,NULL,0,0,0,1,NULL,'admin','admin','2017-06-05 22:15:46','2017-06-05 22:15:46',NULL,'FFFFFF',0),(15,'Super Bomberman R - US/JP','<h1 itemprop=\"name\">Super Bomberman R - US/JP</h1>','<h1 itemprop=\"name\">Super Bomberman R - US/JP</h1>','/full/562017222033_44952593576c1573fc5.81104042_b1hrj1rtxvjwxdkdv5ppdn-dgtfsc8nq_572.jpg','img_05_06_2017/468597_593576c1577dd3.74375084.jpg',1200000,1200000,NULL,NULL,NULL,NULL,NULL,'super-bomberman-r-us-jp',5,NULL,NULL,NULL,NULL,NULL,0,0,0,1,NULL,'admin','admin','2017-06-05 22:20:33','2017-06-05 22:20:33',NULL,'FFFFFF',0),(16,'Witcher 3: Wild Hunt Complete Edition - PS4','<h1 itemprop=\"name\">Witcher 3: Wild Hunt Complete Edition - PS4</h1>','<h1 itemprop=\"name\">Witcher 3: Wild Hunt Complete Edition - PS4</h1>','/full/562017222545_64496593577f9cbc5e9.50362501_a1f-kr5gcgl_551.jpg','img_05_06_2017/208465_593577f9cc11c8.91319870.jpg',1400000,1400000,NULL,NULL,NULL,NULL,NULL,'witcher-3-wild-hunt-complete-edition-ps4',6,NULL,NULL,NULL,NULL,NULL,0,0,1,1,NULL,'admin','admin','2017-06-05 22:25:45','2017-06-05 22:25:45',NULL,'FFFFFF',0),(17,'Dragon Ball Xenoverse 2 - PS4','<h1 itemprop=\"name\">Dragon Ball Xenoverse 2 - PS4</h1>','<h1 itemprop=\"name\">Dragon Ball Xenoverse 2 - PS4</h1>','/full/562017222745_9344259357871b09060.84311824_81gx3usnhol_505.jpg','img_05_06_2017/681397_59357871b0ce20.14115669.jpg',950000,950000,NULL,NULL,NULL,NULL,NULL,'dragon-ball-xenoverse-2-ps4',6,NULL,NULL,NULL,NULL,NULL,0,0,1,1,NULL,'admin','admin','2017-06-05 22:27:45','2017-06-05 22:27:45',NULL,'FFFFFF',0),(18,'Dishonored 2 Limited Edition - PS4','<h1 itemprop=\"name\">Dishonored 2 Limited Edition - PS4</h1>','<h1 itemprop=\"name\">Dishonored 2 Limited Edition - PS4</h1>','/full/562017222855_97846593578b7a2caa9.69542060_81a-w5sdy4l_504.jpg','img_05_06_2017/809540_593578b7a304e0.54649325.jpg',1050000,1050000,NULL,NULL,NULL,NULL,NULL,'dishonored-2-limited-edition-ps4',6,NULL,NULL,NULL,NULL,NULL,1,0,0,1,NULL,'admin','admin','2017-06-05 22:28:55','2017-06-05 22:28:55',NULL,'FFFFFF',0),(19,'Thẻ nhớ MicroSDXC SANDISK ULTRA 200G class 10 90m/s',NULL,'<b>Th&ocirc;ng số, cấu h&igrave;nh</b>&nbsp;Thẻ nhớ MicroSDXC SANDISK ULTRA 200G class 10 90m/s\r\n<table border=\"1\" cellpadding=\"1\">\r\n	<tbody>\r\n		<tr>\r\n			<td><b>Th&ocirc;ng số kĩ thuật</b></td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Loại thẻ nhớ&nbsp;</td>\r\n			<td>microSDXC&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>H&atilde;ng sản xuất</td>\r\n			<td>Sandisk</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Dung lượng</td>\r\n			<td>200GB</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Tốc độ đọc</td>\r\n			<td>90 MB/s</td>\r\n		</tr>\r\n		<tr>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Bảo h&agrave;nh</td>\r\n			<td>5 năm</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Tương th&iacute;ch với tất cả c&aacute;c thiết bị hỗ trợ chuẩn MicroSD như LG, Samsung, Sony, HTC, Nokia, Oppo...</strong></p>','/full/56201722309_50720593579019ef1d4.20737098_microsd-200g-sandisk-ultra-class-10-uhs-90m-hunggiamedia_244.jpg','img_05_06_2017/474274_593579019f2bc1.44488189.jpg',3790000,3790000,NULL,NULL,NULL,NULL,NULL,'th-nh-microsdxc-sandisk-ultra-200g-class-10-90m-s',4,NULL,NULL,NULL,NULL,NULL,1,0,0,1,NULL,'admin','admin','2017-06-07 18:23:32','2017-06-07 18:23:32','sadsad','FFFFFF',0),(20,'Ổ cứng HDD Samsung M3 500Gb USB 3.0','<h1 itemprop=\"name\">Ổ cứng HDD Samsung M3 500Gb USB 3.0</h1>','<h1 itemprop=\"name\">Ổ cứng HDD Samsung M3 500Gb USB 3.0</h1>','/full/562017223138_369445935795a30cdf5.22318835_o-cung-hdd-samsung-m3-500gb-hunggiamedia_319.jpg','img_05_06_2017/740082_5935795a3114e2.12945109.jpg,img_05_06_2017/998780_5935795a3153b4.18846750.jpg',1300000,1300000,NULL,NULL,NULL,NULL,NULL,'ocng-hdd-samsung-m3-500gb-usb-30',7,NULL,NULL,NULL,NULL,NULL,0,1,0,1,NULL,'admin','admin','2017-06-05 22:31:38','2017-06-05 22:31:38',NULL,'FFFFFF',0),(21,'Máy Nintendo Wii','<h1 itemprop=\"name\">M&aacute;y Nintendo Wii</h1>','<h1 itemprop=\"name\">M&aacute;y Nintendo Wii</h1>','/full/562017223240_654959357998b46d46.75360488_13445277-1035653619875434-4687951893503054121-n_366.jpg','img_05_06_2017/233978_59357998b536a5.88852139.jpg',1500000,1500000,NULL,NULL,NULL,NULL,NULL,'may-nintendo-wii',10,NULL,NULL,NULL,NULL,NULL,0,1,0,1,NULL,'admin','admin','2017-06-08 11:03:57','2017-06-08 11:03:57','ewqewqewqewq','#9e2222',1);

/*Table structure for table `promotion_code` */

DROP TABLE IF EXISTS `promotion_code`;

CREATE TABLE `promotion_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `percent` int(11) DEFAULT '0',
  `max_price` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `promotion_code` */

insert  into `promotion_code`(`id`,`name`,`code`,`startdate`,`enddate`,`percent`,`max_price`) values (1,'promotion','PROMO','2017-05-15 23:36:59','2017-05-19 23:37:02',10,300000);

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permission` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `role` */

insert  into `role`(`role_id`,`role_name`,`permission`,`created_at`,`updated_at`,`created_by`,`updated_by`,`status`) values (1,'Administrator','9,11,10,12,67,69,68,70,61,63,62,64,54,65,55,66,5,7,6,8,21,23,22,24,25,27,26,28,1,3,2,4,29,31,30,32,41,43,42,44,45,47,46,48,72,74,73,75,76,78,77,79','2016-05-16 00:00:00','2017-05-09 22:52:47','admin','admin',1),(2,'Editor','25,27,26,28','2016-05-27 05:23:45','2016-06-15 20:57:18','admin','admin',1),(3,'Authenticated User','1,5,9','2016-05-27 05:30:45',NULL,'admin',NULL,0),(4,'Khách Hàng','71','2016-11-08 22:34:37',NULL,'admin',NULL,1);

/*Table structure for table `session_tbl` */

DROP TABLE IF EXISTS `session_tbl`;

CREATE TABLE `session_tbl` (
  `id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `session_tbl` */

insert  into `session_tbl`(`id`,`modified`,`lifetime`,`data`) values ('09jecroftfrtdhajerjlt88kt0',1496762334,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1496776734;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('b3jatb6ot7p5at4u3gij0024g2',1496681459,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1496695859;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}s:16:\"CUSTOMMER_LOGING\";N;}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('8o89t13i6fulsalgclr3v8rq52',1496249829,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1496264229;}}MY_SESSION|a:2:{s:9:\"LANG_CODE\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('dsos0o15cinavlnf8im025gva6',1496080613,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1496095013;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('npuna1tshm3fgqouch4t0iih00',1496895596,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1496909996;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('cqg2qfn05300arnag74j001523',1497283362,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1497297762;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('l6uhr7bl9nrk1t1dv6vhlq53dn',1497288812,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1497303212;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";N;}'),('af45bcbq8gneimano9n439ssb1',1497322284,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1497336683;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";N;}'),('bu33ia8jus999ck8dlb8ns3mm4',1497519926,864000,'Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1497534326;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";N;}'),('7es7q2o3751m2otm1rnkoocio3',1498126569,864000,'Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1498140969;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";N;}'),('i7u9uk2td75p4uona2j17cdmb6',1499097926,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1499112326;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}s:16:\"CUSTOMMER_LOGING\";N;}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}'),('j4bd7rq509t7lvr9m79u6nrho6',1499583416,864000,'__ZF|a:1:{s:10:\"MY_SESSION\";a:1:{s:3:\"ENT\";i:1499597816;}}MY_SESSION|a:3:{s:9:\"LANG_CODE\";N;s:16:\"CUSTOMMER_LOGING\";N;s:14:\"ACCOUNT_LOGING\";a:16:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:10:\"first_name\";s:5:\"admin\";s:9:\"last_name\";s:6:\"trumso\";s:5:\"email\";s:15:\"admin@local.com\";s:7:\"role_id\";s:1:\"1\";s:10:\"created_at\";s:19:\"2016-08-23 17:40:04\";s:10:\"updated_at\";s:19:\"2016-08-23 17:40:07\";s:10:\"created_by\";s:5:\"admin\";s:10:\"updated_by\";s:5:\"admin\";s:6:\"status\";s:1:\"1\";s:20:\"failed_login_attempt\";s:1:\"0\";s:4:\"salt\";N;s:12:\"is_user_root\";s:1:\"1\";s:11:\"reset_token\";s:0:\"\";s:5:\"score\";s:1:\"0\";}}Zend_Auth|a:1:{s:5:\"LOGIN\";s:5:\"admin\";}');

/*Table structure for table `setting` */

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `setting` */

insert  into `setting`(`key`,`value`,`description`,`type`) values ('footer_youtube_video','https://www.youtube.com/embed/LtKWvqLj16Q','https://www.youtube.com/embed/LtKWvqLj16Q',1),('footer_backgound_mage','/upload/images/full/372017221227_49454595a5edb11ad36.46088575_5927c10a63212.jpg','Ảnh nên kế footer',1),('footer_copy_right','Copyright &copy; 2017 - 2019 <span>|</span> HeroGame 02-235-3036 <span>|</span> All Rights Reserved','bản Quyền footer',3),('footer_info_1','<h3>Li&ecirc;n hệ</h3>\r\n\r\n<div class=\"inner\">\r\n<h4>ĐỊA CHỈ:</h4>\r\n\r\n<p>- 141/1 Ho&agrave;ng Văn Thụ, P.8, Q. Ph&uacute; Nhuận</p>\r\n\r\n<h4>THỜI GIAN L&Agrave;M VIỆC:</h4>\r\n\r\n<p>- C&aacute;c ng&agrave;y trong tuần: 9h - 20h</p>\r\n\r\n<p>- Chủ nhật v&agrave; ng&agrave;y lễ: 9h - 19h</p>\r\n</div>','',3),('footer_info_2','<h3>Sản phẩm</h3>\r\n\r\n<div class=\"inner\">\r\n<ul>\r\n	<li><a href=\"#\">Danh mục 1:1</a></li>\r\n	<li><a href=\"#\">Danh mục 1:2</a></li>\r\n	<li><a href=\"#\">Danh mục 1:3</a></li>\r\n	<li><a href=\"#\">Danh mục 1:4</a></li>\r\n</ul>\r\n</div>','',3),('footer_info_3','<h3>Th&ocirc;ng tin</h3>\r\n\r\n<div class=\"inner\">\r\n<ul>\r\n	<li><a href=\"#\">Danh mục 2:1</a></li>\r\n	<li><a href=\"#\">Danh mục 2:2</a></li>\r\n	<li><a href=\"#\">Danh mục 2:3</a></li>\r\n	<li><a href=\"#\">Danh mục 2:4</a></li>\r\n	<li><a href=\"#\">Danh mục 2:5</a></li>\r\n</ul>\r\n</div>','',3),('meta_index_page','{\"og_site_name\":\"herogame site name\",\"og_url\":\"url\",\"og_image\":\"\",\"description_meta\":\"\",\"keyword_meta\":\"\",\"og_description\":\"hero game\",\"og_title\":\"title herogame\"}','meta support SEO for index page',1),('banner-list-product','STATUS_SHOW','ẩn hiện banner ở list product',2),('image-bottom','STATUS_HIDE','Ảnh và banner ở chân trang',2),('style','<style type=\"text/css\">\r\n	/* chỉnhstyle cua banner */ \r\n	.banner-rotator .banner-photo .imgh, .banner-rotator .banner-video .imgh { \r\n	     border-radius: .0rem;  /* banner không viền*/\r\n	}\r\n	/* chỉnh màu backgound của trang web*/ \r\n	.pcontent{ \r\n	    background-color: #fff;\r\n            /*background: url(/upload/images/full/972017132251_544105961cbbbdaf576.15409470_background.png) fixed no-repeat center;*/\r\n	}\r\n	/* menu trai*/\r\n	.side-box>header{\r\n		background: #1a9af5; /* màu nên phía trên menu bên trai*/\r\n		color: #fff;/* màu chữ*/\r\n	}\r\n	/*noi dung o giua*/\r\n	.quick-view .nav-tabs {\r\n		background: #1a9af5;/*mau nen*/\r\n	}\r\n	.quick-view .nav-tabs .nav-link{\r\n		color: #fff;/*mau chu*/\r\n	}\r\n        .quick-view .nav-tabs .nav-link.active {\r\n           background: #0570aa; // màu nên khi phần ở giữa được kích hoạt\r\n       }\r\n    /* item san pham*/\r\n     ul.grid .category-item, ul.grid .product-item, ul.slider .category-item, ul.slider .product-item {\r\n                 border: 3px solid #1b9efb; /* màu viền*/\r\n                 border-style: dashed; /* viền đứt khúc*/\r\n     }\r\n   /* menu item*/\r\n  .category-menu>ul li a {\r\n                   background: #f1f1f1; /* màu nền item*/\r\n}\r\n/* post item*/\r\n.post-menu ul li a{\r\n      background: #f1f1f1;/* màu nền  post*/\r\n}\r\n/* product item*\r\n.product-menu ul li a{\r\n    background: #f1f1f1;/* màu nền item product*/\r\n}    \r\n</style>','Chỉnh style của trang web',3);

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(1) DEFAULT NULL,
  `failed_login_attempt` int(11) DEFAULT '0',
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_user_root` int(11) DEFAULT '0',
  `reset_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `score` int(11) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `user` */

insert  into `user`(`user_id`,`user_name`,`first_name`,`last_name`,`password`,`email`,`role_id`,`created_at`,`updated_at`,`created_by`,`updated_by`,`status`,`failed_login_attempt`,`salt`,`is_user_root`,`reset_token`,`score`) values (1,'admin','admin','trumso','e10adc3949ba59abbe56e057f20f883e','admin@local.com',1,'2016-08-23 17:40:04','2016-08-23 17:40:07','admin','admin',1,0,NULL,1,'',0),(17,'phuong2','nguyen','phuong','$2y$10$eFU1WXkyNkQrK0dvVEFCeOBGLnQh92G1/BUAmKg9oI3eEpNp.WcBy','nguyenhoangphuong1991@gmail2.com',4,'2016-11-10 23:05:50','2016-11-10 23:05:50','phuong2','phuong2',1,0,'xU5Yy26D++GoTAByUCYuNQ9YadBaEQ==',0,'',0),(16,'phuong','nguyen','phuong','e10adc3949ba59abbe56e057f20f883e','nguyenhoangphuong1991@gmail.com',4,'2016-11-10 23:03:25','2016-12-01 22:49:00','phuong','2016-12-01 22:49:00',1,0,NULL,0,'',0),(18,'mabu','nguyen','bu','fcea920f7412b5da7be0cf42b8c93759','mabu@gmail.com',4,'2016-12-01 23:04:54','2016-12-01 23:08:11','mabu','2016-12-01 23:08:11',1,0,NULL,0,'',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
