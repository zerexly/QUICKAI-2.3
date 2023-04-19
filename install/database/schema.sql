-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 18, 2023 at 07:56 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quickai`
--

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>admins`
--

DROP TABLE IF EXISTS `<<prefix>>admins`;
CREATE TABLE IF NOT EXISTS `<<prefix>>admins`
(
    `id`            int(11) UNSIGNED                          NOT NULL AUTO_INCREMENT,
    `username`      varchar(40) COLLATE utf8mb4_unicode_ci             DEFAULT NULL,
    `password_hash` varchar(200) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `name`          varchar(255) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `email`         varchar(255) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `image`         varchar(255) COLLATE utf8mb4_unicode_ci   NOT NULL DEFAULT 'default_user.png',
    `permission`    enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ai_documents`
--

DROP TABLE IF EXISTS `<<prefix>>ai_documents`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_documents`
(
    `id`         int(11) NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)                                 DEFAULT NULL,
    `title`      varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content`    longtext COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    `template`   varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` datetime                                DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ai_images`
--

DROP TABLE IF EXISTS `<<prefix>>ai_images`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_images`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `user_id`     int(11)                                 DEFAULT NULL,
    `title`       varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci         DEFAULT NULL,
    `resolution`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `image`       varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at`  datetime                                DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>balance`
--

DROP TABLE IF EXISTS `<<prefix>>balance`;
CREATE TABLE IF NOT EXISTS `<<prefix>>balance`
(
    `id`               int(10) NOT NULL AUTO_INCREMENT,
    `current_balance`  double(9, 2) DEFAULT NULL,
    `total_earning`    double(9, 2) DEFAULT NULL,
    `total_withdrawal` double(9, 2) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>blog`
--

DROP TABLE IF EXISTS `<<prefix>>blog`;
CREATE TABLE IF NOT EXISTS `<<prefix>>blog`
(
    `id`          int(10)                                               NOT NULL AUTO_INCREMENT,
    `author`      int(10)                                                        DEFAULT NULL,
    `title`       varchar(255) COLLATE utf8mb4_unicode_ci                        DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci                                DEFAULT NULL,
    `image`       varchar(255) COLLATE utf8mb4_unicode_ci                        DEFAULT NULL,
    `tags`        text CHARACTER SET utf32 COLLATE utf32_unicode_ci              DEFAULT NULL,
    `status`      enum ('publish','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
    `created_at`  datetime                                                       DEFAULT NULL,
    `updated_at`  datetime                                                       DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>blog_categories`
--

DROP TABLE IF EXISTS `<<prefix>>blog_categories`;
CREATE TABLE IF NOT EXISTS `<<prefix>>blog_categories`
(
    `id`       int(10)                                   NOT NULL AUTO_INCREMENT,
    `title`    varchar(50) COLLATE utf8mb4_unicode_ci             DEFAULT NULL,
    `slug`     varchar(50) COLLATE utf8mb4_unicode_ci             DEFAULT NULL,
    `position` int(10)                                   NOT NULL DEFAULT 0,
    `active`   enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>blog_cat_relation`
--

DROP TABLE IF EXISTS `<<prefix>>blog_cat_relation`;
CREATE TABLE IF NOT EXISTS `<<prefix>>blog_cat_relation`
(
    `id`          int(10) NOT NULL AUTO_INCREMENT,
    `blog_id`     int(10) DEFAULT NULL,
    `category_id` int(10) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>blog_comment`
--

DROP TABLE IF EXISTS `<<prefix>>blog_comment`;
CREATE TABLE IF NOT EXISTS `<<prefix>>blog_comment`
(
    `id`         int(10)                                   NOT NULL AUTO_INCREMENT,
    `blog_id`    int(10)                                            DEFAULT NULL,
    `user_id`    int(10)                                            DEFAULT NULL,
    `is_admin`   enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    `name`       tinytext COLLATE utf8mb4_unicode_ci                DEFAULT NULL,
    `email`      varchar(100) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `comment`    text COLLATE utf8mb4_unicode_ci           NOT NULL,
    `created_at` datetime                                           DEFAULT NULL,
    `active`     enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
    `parent`     int(10)                                   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>countries`
--

DROP TABLE IF EXISTS `<<prefix>>countries`;
CREATE TABLE IF NOT EXISTS `<<prefix>>countries`
(
    `id`                   int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code`                 char(2) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `latitude`             varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `longitude`            varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `iso3`                 char(3) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `iso_numeric`          int(10) UNSIGNED                        DEFAULT NULL,
    `fips`                 char(2) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `name`                 varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `asciiname`            varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `capital`              varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `area`                 int(10) UNSIGNED                        DEFAULT NULL,
    `population`           int(10) UNSIGNED                        DEFAULT NULL,
    `continent_code`       char(4) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `tld`                  char(4) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `currency_code`        varchar(3) COLLATE utf8mb4_unicode_ci   DEFAULT NULL,
    `phone`                varchar(20) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `postal_code_format`   varchar(50) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `postal_code_regex`    varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `languages`            varchar(50) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `neighbours`           varchar(50) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `equivalent_fips_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `active`               tinyint(1)                              DEFAULT 1,
    `created_at`           timestamp        NULL                   DEFAULT NULL,
    `updated_at`           timestamp        NULL                   DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>currencies`
--

DROP TABLE IF EXISTS `<<prefix>>currencies`;
CREATE TABLE IF NOT EXISTS `<<prefix>>currencies`
(
    `id`                 int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code`               varchar(3) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `name`               varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `html_entity`        varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From Github : An array of currency symbols as HTML entities',
    `font_arial`         varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `font_code2000`      varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `unicode_decimal`    varchar(5) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `unicode_hex`        varchar(5) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `in_left`            tinyint(1)                             DEFAULT 0,
    `decimal_places`     int(10) UNSIGNED                       DEFAULT 2 COMMENT 'Currency Decimal Places - ISO 4217',
    `decimal_separator`  varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '.',
    `thousand_separator` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT ',',
    `created_at`         timestamp        NULL                  DEFAULT NULL,
    `updated_at`         timestamp        NULL                  DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>faq_entries`
--

DROP TABLE IF EXISTS `<<prefix>>faq_entries`;
CREATE TABLE IF NOT EXISTS `<<prefix>>faq_entries`
(
    `faq_id`           mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `translation_lang` varchar(10) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `translation_of`   int(10) UNSIGNED                        DEFAULT NULL,
    `parent_id`        int(10) UNSIGNED                        DEFAULT NULL,
    `faq_pid`          smallint(4)           NOT NULL          DEFAULT 0,
    `faq_weight`       mediumint(6)          NOT NULL          DEFAULT 0,
    `faq_title`        varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `faq_content`      mediumtext COLLATE utf8mb4_unicode_ci   DEFAULT NULL,
    `active`           tinyint(1)                              DEFAULT 1,
    PRIMARY KEY (`faq_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>image_used`
--

DROP TABLE IF EXISTS `<<prefix>>image_used`;
CREATE TABLE IF NOT EXISTS `<<prefix>>image_used`
(
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11)  DEFAULT NULL,
    `images`  int(11)  DEFAULT NULL,
    `date`    datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>languages`
--

DROP TABLE IF EXISTS `<<prefix>>languages`;
CREATE TABLE IF NOT EXISTS `<<prefix>>languages`
(
    `id`        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code`      varchar(10) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `direction` varchar(3) COLLATE utf8mb4_unicode_ci   DEFAULT NULL,
    `name`      varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_name` varchar(20) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `active`    tinyint(1)       NOT NULL               DEFAULT 1,
    `default`   tinyint(1)       NOT NULL               DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>logs`
--

DROP TABLE IF EXISTS `<<prefix>>logs`;
CREATE TABLE IF NOT EXISTS `<<prefix>>logs`
(
    `log_id`      int(11) UNSIGNED                        NOT NULL AUTO_INCREMENT,
    `log_date`    int(11) UNSIGNED                        NOT NULL DEFAULT 0,
    `log_summary` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `log_details` longtext COLLATE utf8mb4_unicode_ci     NOT NULL,
    PRIMARY KEY (`log_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>options`
--

DROP TABLE IF EXISTS `<<prefix>>options`;
CREATE TABLE IF NOT EXISTS `<<prefix>>options`
(
    `option_id`    bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `option_name`  varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `option_value` longtext COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    PRIMARY KEY (`option_id`),
    UNIQUE KEY `option_name` (`option_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>pages`
--

DROP TABLE IF EXISTS `<<prefix>>pages`;
CREATE TABLE IF NOT EXISTS `<<prefix>>pages`
(
    `id`               int(10) UNSIGNED                          NOT NULL AUTO_INCREMENT,
    `translation_lang` varchar(10) COLLATE utf8mb4_unicode_ci             DEFAULT NULL,
    `translation_of`   int(10) UNSIGNED                                   DEFAULT NULL,
    `parent_id`        int(10) UNSIGNED                                   DEFAULT NULL,
    `type`             enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    `name`             varchar(100) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `slug`             varchar(100) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `title`            varchar(200) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `content`          text COLLATE utf8mb4_unicode_ci                    DEFAULT NULL,
    `active`           tinyint(1)                                         DEFAULT 1,
    `created_at`       timestamp                                 NULL     DEFAULT NULL,
    `updated_at`       timestamp                                 NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>payments`
--

DROP TABLE IF EXISTS `<<prefix>>payments`;
CREATE TABLE IF NOT EXISTS `<<prefix>>payments`
(
    `payment_id`      mediumint(8) UNSIGNED                     NOT NULL AUTO_INCREMENT,
    `payment_install` enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    `payment_title`   varchar(255) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    `payment_folder`  varchar(30) COLLATE utf8mb4_unicode_ci             DEFAULT NULL,
    `payment_desc`    varchar(255) COLLATE utf8mb4_unicode_ci            DEFAULT NULL,
    PRIMARY KEY (`payment_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>plans`
--

DROP TABLE IF EXISTS `<<prefix>>plans`;
CREATE TABLE IF NOT EXISTS `<<prefix>>plans`
(
    `id`             int(11)                                      NOT NULL AUTO_INCREMENT,
    `name`           varchar(255) COLLATE utf8mb4_unicode_ci      NOT NULL DEFAULT '',
    `badge`          text COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `monthly_price`  float                                                 DEFAULT NULL,
    `annual_price`   float                                                 DEFAULT NULL,
    `lifetime_price` float                                                 DEFAULT NULL,
    `recommended`    enum ('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
    `settings`       text COLLATE utf8mb4_unicode_ci              NOT NULL,
    `taxes_ids`      text COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `status`         tinyint(4)                                   NOT NULL,
    `date`           datetime                                     NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>plan_options`
--

DROP TABLE IF EXISTS `<<prefix>>plan_options`;
CREATE TABLE IF NOT EXISTS `<<prefix>>plan_options`
(
    `id`               int(11)    NOT NULL AUTO_INCREMENT,
    `title`            varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `translation_lang` longtext COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    `translation_name` longtext COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    `position`         int(10)                                 DEFAULT NULL,
    `active`           tinyint(1) NOT NULL                     DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>subscriber`
--

DROP TABLE IF EXISTS `<<prefix>>subscriber`;
CREATE TABLE IF NOT EXISTS `<<prefix>>subscriber`
(
    `id`     int(11) NOT NULL AUTO_INCREMENT,
    `email`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `joined` date                                    DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>taxes`
--

DROP TABLE IF EXISTS `<<prefix>>taxes`;
CREATE TABLE IF NOT EXISTS `<<prefix>>taxes`
(
    `id`            int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `internal_name` varchar(64) COLLATE utf8mb4_unicode_ci                         DEFAULT NULL,
    `name`          varchar(64) COLLATE utf8mb4_unicode_ci                         DEFAULT NULL,
    `description`   varchar(256) COLLATE utf8mb4_unicode_ci                        DEFAULT NULL,
    `value`         decimal(10, 2)                                                 DEFAULT NULL,
    `value_type`    enum ('percentage','fixed') COLLATE utf8mb4_unicode_ci         DEFAULT NULL,
    `type`          enum ('inclusive','exclusive') COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `billing_type`  enum ('personal','business','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `countries`     text COLLATE utf8mb4_unicode_ci                                DEFAULT NULL,
    `datetime`      datetime                                                       DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>testimonials`
--

DROP TABLE IF EXISTS `<<prefix>>testimonials`;
CREATE TABLE IF NOT EXISTS `<<prefix>>testimonials`
(
    `id`          int(10)                         NOT NULL AUTO_INCREMENT,
    `name`        varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `designation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content`     text COLLATE utf8mb4_unicode_ci NOT NULL,
    `image`       varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>time_zones`
--

DROP TABLE IF EXISTS `<<prefix>>time_zones`;
CREATE TABLE IF NOT EXISTS `<<prefix>>time_zones`
(
    `id`           int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `country_code` varchar(2) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `time_zone_id` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `gmt`          float                                  DEFAULT NULL,
    `dst`          float                                  DEFAULT NULL,
    `raw`          float                                  DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `time_zone_id` (`time_zone_id`),
    KEY `country_code` (`country_code`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>transaction`
--

DROP TABLE IF EXISTS `<<prefix>>transaction`;
CREATE TABLE IF NOT EXISTS `<<prefix>>transaction`
(
    `id`                      int(11) NOT NULL AUTO_INCREMENT,
    `product_name`            varchar(225) COLLATE utf8mb4_unicode_ci                                 DEFAULT NULL,
    `product_id`              int(11)                                                                 DEFAULT NULL,
    `seller_id`               int(11)                                                                 DEFAULT NULL,
    `amount`                  double(9, 2)                                                            DEFAULT NULL,
    `base_amount`             double(9, 2)                                                            DEFAULT NULL,
    `featured`                enum ('0','1') COLLATE utf8mb4_unicode_ci                               DEFAULT '0',
    `urgent`                  enum ('0','1') COLLATE utf8mb4_unicode_ci                               DEFAULT '0',
    `highlight`               enum ('0','1') COLLATE utf8mb4_unicode_ci                               DEFAULT '0',
    `transaction_time`        int(11)                                                                 DEFAULT NULL,
    `status`                  enum ('pending','success','failed','cancel') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `payment_id`              varchar(255) COLLATE utf8mb4_unicode_ci                                 DEFAULT NULL,
    `transaction_gatway`      varchar(255) COLLATE utf8mb4_unicode_ci                                 DEFAULT NULL,
    `transaction_ip`          varchar(15) COLLATE utf8mb4_unicode_ci                                  DEFAULT NULL,
    `transaction_description` varchar(255) COLLATE utf8mb4_unicode_ci                                 DEFAULT NULL,
    `transaction_method`      varchar(20) COLLATE utf8mb4_unicode_ci                                  DEFAULT NULL,
    `frequency`               enum ('MONTHLY','YEARLY','LIFETIME') COLLATE utf8mb4_unicode_ci         DEFAULT NULL,
    `billing`                 text COLLATE utf8mb4_unicode_ci                                         DEFAULT NULL,
    `taxes_ids`               text COLLATE utf8mb4_unicode_ci                                         DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>upgrades`
--

DROP TABLE IF EXISTS `<<prefix>>upgrades`;
CREATE TABLE IF NOT EXISTS `<<prefix>>upgrades`
(
    `upgrade_id`                   int(11) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `sub_id`                       varchar(16) COLLATE utf8mb4_unicode_ci                   NOT NULL DEFAULT '0',
    `user_id`                      int(11) UNSIGNED                                         NOT NULL DEFAULT 0,
    `pay_mode`                     enum ('one_time','recurring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'one_time',
    `upgrade_lasttime`             bigint(20) UNSIGNED                                      NOT NULL DEFAULT 0,
    `upgrade_expires`              bigint(20) UNSIGNED                                      NOT NULL DEFAULT 0,
    `unique_id`                    varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `invoice_id`                   varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `paypal_subscription_id`       varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `paypal_profile_id`            varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `stripe_customer_id`           varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `stripe_subscription_id`       varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `authorizenet_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `billing_day`                  int(2)                                                            DEFAULT NULL,
    `length`                       int(4)                                                            DEFAULT NULL,
    `interval`                     int(4)                                                            DEFAULT NULL,
    `trial_days`                   int(4)                                                            DEFAULT NULL,
    `status`                       varchar(255) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `date_trial_ends`              date                                                              DEFAULT NULL,
    `date_canceled`                datetime                                                          DEFAULT NULL,
    `date_created`                 timestamp                                                NULL     DEFAULT current_timestamp(),
    PRIMARY KEY (`upgrade_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>user`
--

DROP TABLE IF EXISTS `<<prefix>>user`;
CREATE TABLE IF NOT EXISTS `<<prefix>>user`
(
    `id`             int(11)                                   NOT NULL AUTO_INCREMENT,
    `group_id`       varchar(16) COLLATE utf8mb4_unicode_ci    NOT NULL                 DEFAULT 'free',
    `username`       varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `user_type`      enum ('user','employer') COLLATE utf8mb4_unicode_ci                DEFAULT NULL,
    `balance`        float(10, 2)                              NOT NULL                 DEFAULT 0.00,
    `password_hash`  varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `forgot`         varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `confirm`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `email`          varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `status`         enum ('0','1','2') COLLATE utf8mb4_unicode_ci                      DEFAULT NULL,
    `view`           int(11)                                                            DEFAULT NULL,
    `created_at`     datetime                                                           DEFAULT NULL,
    `updated_at`     datetime                                                           DEFAULT NULL,
    `name`           varchar(225) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `tagline`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `description`    text COLLATE utf8mb4_unicode_ci                                    DEFAULT NULL,
    `dob`            date                                                               DEFAULT NULL,
    `salary_min`     bigint(20)                                NOT NULL                 DEFAULT 0,
    `salary_max`     bigint(20)                                NOT NULL                 DEFAULT 0,
    `category`       int(11)                                                            DEFAULT NULL,
    `subcategory`    int(11)                                                            DEFAULT NULL,
    `website`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `sex`            enum ('Male','Female','Other') COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `phone`          varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `postcode`       varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `address`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `country`        varchar(50) COLLATE utf8mb4_unicode_ci                             DEFAULT NULL,
    `city`           varchar(225) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `city_code`      char(50) COLLATE utf8mb4_unicode_ci                                DEFAULT NULL,
    `state_code`     char(50) COLLATE utf8mb4_unicode_ci                                DEFAULT NULL,
    `country_code`   char(50) COLLATE utf8mb4_unicode_ci                                DEFAULT NULL,
    `image`          varchar(225) COLLATE utf8mb4_unicode_ci   NOT NULL                 DEFAULT 'default_user.png',
    `lastactive`     datetime                                                           DEFAULT NULL,
    `facebook`       varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `twitter`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `googleplus`     varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `instagram`      varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `linkedin`       varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `youtube`        varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `oauth_provider` enum ('','facebook','google','twitter') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `oauth_uid`      varchar(100) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `oauth_link`     varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `online`         enum ('0','1') COLLATE utf8mb4_unicode_ci NOT NULL                 DEFAULT '0',
    `notify`         enum ('0','1') COLLATE utf8mb4_unicode_ci                          DEFAULT '0',
    `notify_cat`     varchar(255) COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `currency`       varchar(10) COLLATE utf8mb4_unicode_ci                             DEFAULT NULL,
    `referral_key`   VARCHAR(255)                              NULL                     DEFAULT NULL,
    `referred_by`    INT(11)                                   NULL                     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>user_options`
--

DROP TABLE IF EXISTS `<<prefix>>user_options`;
CREATE TABLE IF NOT EXISTS `<<prefix>>user_options`
(
    `id`           int(11) NOT NULL AUTO_INCREMENT,
    `user_id`      int(11)                                 DEFAULT NULL,
    `option_name`  varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `option_value` longtext COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>word_used`
--

DROP TABLE IF EXISTS `<<prefix>>word_used`;
CREATE TABLE IF NOT EXISTS `<<prefix>>word_used`
(
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11)  DEFAULT NULL,
    `words`   int(11)  DEFAULT NULL,
    `date`    datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ai_custom_templates`
--

DROP TABLE IF EXISTS `<<prefix>>ai_custom_templates`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_custom_templates`
(
    `id`          int(11)    NOT NULL AUTO_INCREMENT,
    `category_id` int(11)             DEFAULT NULL,
    `title`       varchar(255)        DEFAULT NULL,
    `slug`        varchar(255)        DEFAULT NULL,
    `icon`        varchar(255)        DEFAULT NULL,
    `description` text                DEFAULT NULL,
    `prompt`      longtext            DEFAULT NULL,
    `parameters`  longtext            DEFAULT NULL,
    `position`    int(11)             DEFAULT NULL,
    `active`      tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ai_templates`
--

DROP TABLE IF EXISTS `<<prefix>>ai_templates`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_templates`
(
    `id`          int(11)    NOT NULL AUTO_INCREMENT,
    `category_id` int(11)             DEFAULT NULL,
    `title`       varchar(255)        DEFAULT NULL,
    `slug`        varchar(255)        DEFAULT NULL,
    `icon`        varchar(255)        DEFAULT NULL,
    `description` text                DEFAULT NULL,
    `position`    int(11)             DEFAULT NULL,
    `active`      tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ai_template_categories`
--

DROP TABLE IF EXISTS `<<prefix>>ai_template_categories`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_template_categories`
(
    `id`               int(11)    NOT NULL AUTO_INCREMENT,
    `title`            varchar(255)        DEFAULT NULL,
    `translation_lang` longtext            DEFAULT NULL,
    `translation_name` longtext            DEFAULT NULL,
    `position`         int(11)             DEFAULT NULL,
    `active`           tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>ai_chat`
--

DROP TABLE IF EXISTS `<<prefix>>ai_chat`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ai_chat`
(
    `id`           int(11) NOT NULL AUTO_INCREMENT,
    `user_id`      int(11)  DEFAULT NULL,
    `user_message` text     DEFAULT NULL,
    `ai_message`   text     DEFAULT NULL,
    `date`         datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>api_keys`
--

DROP TABLE IF EXISTS `<<prefix>>api_keys`;
CREATE TABLE IF NOT EXISTS `<<prefix>>api_keys`
(
    `id`      int(11)      NOT NULL AUTO_INCREMENT,
    `title`   varchar(255)          DEFAULT NULL,
    `api_key` varchar(255)          DEFAULT NULL,
    `type`    VARCHAR(255) NULL     DEFAULT NULL,
    `active`  tinyint(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>speech_to_text_used`
--

DROP TABLE IF EXISTS `<<prefix>>speech_to_text_used`;
CREATE TABLE IF NOT EXISTS `<<prefix>>speech_to_text_used`
(
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11)  DEFAULT NULL,
    `date`    datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>affiliates`
--

DROP TABLE IF EXISTS `<<prefix>>affiliates`;
CREATE TABLE IF NOT EXISTS `<<prefix>>affiliates`
(
    `id`             int(11) NOT NULL AUTO_INCREMENT,
    `referrer_id`    int(11)      DEFAULT NULL,
    `referred_id`    int(11)      DEFAULT NULL,
    `transaction_id` int(11)      DEFAULT NULL,
    `payment`        float(11, 2) DEFAULT NULL,
    `commission`     float(11, 2) DEFAULT NULL,
    `rate`           float(11, 2) DEFAULT NULL,
    `gateway`        varchar(255) DEFAULT NULL,
    `date`           datetime     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>withdrawal`
--

DROP TABLE IF EXISTS `<<prefix>>withdrawal`;
CREATE TABLE IF NOT EXISTS `<<prefix>>withdrawal`
(
    `id`                int(11)                             NOT NULL AUTO_INCREMENT,
    `user_id`           int(11)                                      DEFAULT NULL,
    `status`            enum ('success','pending','reject') NOT NULL DEFAULT 'pending',
    `amount`            int(11)                                      DEFAULT NULL,
    `payment_method_id` int(11)                                      DEFAULT NULL,
    `account_details`   text                                         DEFAULT NULL,
    `reject_reason`     text                                         DEFAULT NULL,
    `created_at`        datetime                                     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Table structure for table `<<prefix>>adsense`
--

DROP TABLE IF EXISTS `<<prefix>>adsense`;
CREATE TABLE IF NOT EXISTS `<<prefix>>adsense`
(
    `id`                int(11)    NOT NULL AUTO_INCREMENT,
    `slug`              text                DEFAULT NULL,
    `size`              text                DEFAULT NULL,
    `provider_name`     varchar(255)        DEFAULT NULL,
    `large_track_code`  text                DEFAULT NULL,
    `tablet_track_code` text                DEFAULT NULL,
    `phone_track_code`  text                DEFAULT NULL,
    `status`            tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
