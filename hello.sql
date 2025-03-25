-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table dewanchemicalokr.actions
CREATE TABLE IF NOT EXISTS `actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `actionable_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actionable_id` int unsigned NOT NULL,
  `action_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.actions: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.adjustments
CREATE TABLE IF NOT EXISTS `adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int unsigned NOT NULL,
  `adjust_date` date DEFAULT NULL,
  `tracking_no` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.adjustments: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.adjustment_details
CREATE TABLE IF NOT EXISTS `adjustment_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `adjust_type` tinyint unsigned NOT NULL COMMENT '1=> Minus, 2=> Plus',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.adjustment_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 => Enable,\r\nDisabled => 0',
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.admins: ~1 rows (approximately)
INSERT INTO `admins` (`id`, `role_id`, `name`, `email`, `mobile`, `username`, `image`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 0, 'Super Admin', 'admin@site.com', NULL, 'admin', '668faec6ec6401720692422.png', '$2y$12$UUyNnwC9xYZNPWVXLZLcbOq2C5bRqMUtZq8YSnnG7TpBCkwlBJ9QK', 1, 'P26QVwk7z1x2uBYw9BLl2jWWHYicy7H0hIHZTqVQ3KNB1tBN6a3UUdHvLTW4', NULL, '2025-03-22 20:28:24');

-- Dumping structure for table dewanchemicalokr.admin_notifications
CREATE TABLE IF NOT EXISTS `admin_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `click_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.admin_notifications: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.admin_password_resets
CREATE TABLE IF NOT EXISTS `admin_password_resets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.admin_password_resets: ~1 rows (approximately)
INSERT INTO `admin_password_resets` (`id`, `email`, `token`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'admin@site.com', '774522', 0, '2024-07-11 04:22:15', NULL);

-- Dumping structure for table dewanchemicalokr.brands
CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.brands: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.categories: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'contains full address',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.customers: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.customer_payments
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `sale_id` int unsigned DEFAULT NULL,
  `sale_return_id` int unsigned DEFAULT NULL,
  `amount` decimal(28,8) unsigned NOT NULL DEFAULT '0.00000000',
  `trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.customer_payments: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_type_id` int unsigned NOT NULL,
  `date_of_expense` date DEFAULT NULL COMMENT 'Expense date',
  `amount` double(28,8) NOT NULL DEFAULT '0.00000000',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.expenses: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.expense_types
CREATE TABLE IF NOT EXISTS `expense_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.expense_types: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.extensions
CREATE TABLE IF NOT EXISTS `extensions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `script` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shortcode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'object',
  `support` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.extensions: ~2 rows (approximately)
INSERT INTO `extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
	(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\n<script src="https://www.google.com/recaptcha/api.js"></script>\n<div class="g-recaptcha" data-sitekey="{{site_key}}" data-callback="verifyCaptcha"></div>\n<div id="g-recaptcha-error"></div>', '{"site_key":{"title":"Site Key","value":"------------------"},"secret_key":{"title":"Secret Key","value":"------------"}}', 'recaptcha.png', 0, '2019-10-18 11:16:05', '2024-05-08 03:23:13'),
	(3, 'custom-captcha', 'Custom Captcha', 'Just put any random string', 'customcaptcha.png', NULL, '{"random_key":{"title":"Random String","value":"SecureString"}}', 'na', 0, '2019-10-18 11:16:05', '2024-07-11 00:25:55');

-- Dumping structure for table dewanchemicalokr.general_settings
CREATE TABLE IF NOT EXISTS `general_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cur_text` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_template` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `active_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sms_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'email configuration',
  `sms_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `global_shortcodes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `en` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'email notification, 0 - dont send, 1 - send',
  `sn` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'sms notification, 0 - dont send, 1 - send',
  `system_customized` tinyint(1) NOT NULL DEFAULT '0',
  `paginate_number` int NOT NULL DEFAULT '0',
  `currency_format` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>Both\r\n2=>Text Only\r\n3=>Symbol Only',
  `available_version` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.general_settings: ~1 rows (approximately)
INSERT INTO `general_settings` (`id`, `site_name`, `cur_text`, `cur_sym`, `email_from`, `email_from_name`, `email_template`, `active_template`, `sms_template`, `sms_from`, `mail_config`, `sms_config`, `global_shortcodes`, `en`, `sn`, `system_customized`, `paginate_number`, `currency_format`, `available_version`, `created_at`, `updated_at`) VALUES
	(1, 'Dewan', 'PKR', 'PKR', 'info@viserlab.com', 'viserlab', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv="X-UA-Compatible" content="IE=edge">\r\n  <!--<![endif]-->\r\n  <meta name="viewport" content="width=device-width, initial-scale=1.0">\r\n  <title></title>\r\n  <style type="text/css">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class="table-inner"] { width: 90% !important; text-align: center !important; }\r\n*[class="table-full"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class="img1"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor="#414a51" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">\r\n    <tbody><tr>\r\n      <td height="50"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align="center" style="text-align:center;vertical-align:top;font-size:0;">\r\n        <table align="center" border="0" cellpadding="0" cellspacing="0">\r\n          <tbody><tr>\r\n            <td align="center" width="600">\r\n              <!--header-->\r\n              <table class="table-inner" width="95%" border="0" align="center" cellpadding="0" cellspacing="0">\r\n                <tbody><tr>\r\n                  <td bgcolor="#0087ff" style="border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;" align="center">\r\n                    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">\r\n                      <tbody><tr>\r\n                        <td height="20"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height="20"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class="table-inner" width="95%" border="0" cellspacing="0" cellpadding="0">\r\n                <tbody><tr>\r\n                  <td bgcolor="#FFFFFF" align="center" style="text-align:center;vertical-align:top;font-size:0;">\r\n                    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">\r\n                      <tbody><tr>\r\n                        <td height="35"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align="center" style="vertical-align:top;font-size:0;">\r\n                          <a href="#">\r\n                            <img style="display:block; line-height:0px; font-size:0px; border:0px;" src="https://i.imgur.com/Z1qtvtV.png" alt="img">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      \r\n                      <!--headline-->\r\n                      <tr>\r\n                        \r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align="center" style="text-align:center;vertical-align:top;font-size:0;">\r\n                          <table width="40" border="0" align="center" cellpadding="0" cellspacing="0">\r\n                            <tbody>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height="20"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align="left" style="font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height="40"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height="45" align="center" bgcolor="#f4f4f4" style="border-bottom-left-radius:6px;border-bottom-right-radius:6px;">\r\n                    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">\r\n                      <tbody><tr>\r\n                        <td height="10"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class="preference-link" align="center" style="font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;">\r\n                          Â© 2024 <a href="#">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height="10"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height="60"></td>\r\n    </tr>\r\n  </tbody></table>', 'basic', '{{message}}', 'ViserAdmin', '{"name":"php"}', '{"name":"nexmo","clickatell":{"api_key":"----------------"},"infobip":{"username":"------------8888888","password":"-----------------"},"message_bird":{"api_key":"-------------------"},"nexmo":{"api_key":"----------------------","api_secret":"----------------------"},"sms_broadcast":{"username":"----------------------","password":"-----------------------------"},"twilio":{"account_sid":"-----------------------","auth_token":"---------------------------","from":"----------------------"},"text_magic":{"username":"-----------------------","apiv2_key":"-------------------------------"},"custom":{"method":"get","url":"https:\\/\\/hostname\\/demo-api-v1","headers":{"name":["api_key"],"value":["test_api 555"]},"body":{"name":["from_number"],"value":["5657545757"]}}}', '{\n    "site_name":"Name of your site",\n    "site_currency":"Currency of your site",\n    "currency_symbol":"Symbol of currency"\n}', 1, 1, 0, 20, 3, '2.0', NULL, '2025-03-23 06:44:32');

-- Dumping structure for table dewanchemicalokr.notification_logs
CREATE TABLE IF NOT EXISTS `notification_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL DEFAULT '0',
  `sender` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_to` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notification_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.notification_logs: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.notification_templates
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sms_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shortcodes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email_status` tinyint(1) NOT NULL DEFAULT '1',
  `email_sent_from_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_sent_from_address` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT '1',
  `sms_sent_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.notification_templates: ~4 rows (approximately)
INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `created_at`, `updated_at`) VALUES
	(7, 'PASS_RESET_CODE', 'Password - Reset - Code', 'Password Reset', '<div style="font-family: Montserrat, sans-serif;">We have received a request to reset the password for your account on&nbsp;<span style="font-weight: bolder;">{{time}} .<br></span></div><div style="font-family: Montserrat, sans-serif;">Requested From IP:&nbsp;<span style="font-weight: bolder;">{{ip}}</span>&nbsp;using&nbsp;<span style="font-weight: bolder;">{{browser}}</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{operating_system}}&nbsp;</span>.</div><div style="font-family: Montserrat, sans-serif;"><br></div><br style="font-family: Montserrat, sans-serif;"><div style="font-family: Montserrat, sans-serif;"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size="6"><span style="font-weight: bolder;">{{code}}</span></font></div><div><br></div></div><div style="font-family: Montserrat, sans-serif;"><br></div><div style="font-family: Montserrat, sans-serif;"><font size="4" color="#CC0000">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size="4" color="#CC0000"><br></font></div>', 'Your account recovery code is: {{code}}', '{"code":"Verification code for password reset","ip":"IP address of the user","browser":"Browser of the user","operating_system":"Operating system of the user","time":"Time of the request"}', 1, NULL, NULL, 0, NULL, '2021-11-03 12:00:00', '2022-03-20 20:47:05'),
	(8, 'PASS_RESET_DONE', 'Password - Reset - Confirmation', 'You have reset your password', '<p style="font-family: Montserrat, sans-serif;">You have successfully reset your password.</p><p style="font-family: Montserrat, sans-serif;">You changed from&nbsp; IP:&nbsp;<span style="font-weight: bolder;">{{ip}}</span>&nbsp;using&nbsp;<span style="font-weight: bolder;">{{browser}}</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style="font-weight: bolder;">{{time}}</span></p><p style="font-family: Montserrat, sans-serif;"><span style="font-weight: bolder;"><br></span></p><p style="font-family: Montserrat, sans-serif;"><span style="font-weight: bolder;"><font color="#ff0000">If you did not change that, please contact us as soon as possible.</font></span></p>', 'Your password has been changed successfully', '{"ip":"IP address of the user","browser":"Browser of the user","operating_system":"Operating system of the user","time":"Time of the request"}', 1, NULL, NULL, 1, NULL, '2021-11-03 12:00:00', '2022-04-05 03:46:35'),
	(15, 'DEFAULT', 'Default Template', '{{subject}}', '{{message}}', '{{message}}', '{"subject":"Subject","message":"Message"}', 1, NULL, NULL, 1, NULL, '2019-09-14 13:14:22', '2021-11-04 09:38:55'),
	(18, 'ADD_STAFF', 'Staff Add', 'Appointed as a staff', 'Hello,&nbsp;{{ name }},<br><br>Your {{ site_name }}\r\nlogin credential is username: {{username}} password: {{password}}', 'Your {{ site_name }} login credential  is  username: {{username}} password: {{password}}', '{"name":"full Name","username":"Access his/her guard username","password":"Access his/her guard password"}', 1, NULL, NULL, 1, NULL, '2019-09-14 13:14:22', '2022-10-17 10:31:39');

-- Dumping structure for table dewanchemicalokr.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.password_resets: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.permissions: ~198 rows (approximately)
INSERT INTO `permissions` (`id`, `name`, `group`, `code`) VALUES
	(2, 'Dashboard', 'AdminController', 'admin.dashboard'),
	(3, 'Report Request', 'AdminController', 'admin.request.report'),
	(5, 'Download Attachment', 'AdminController', 'admin.download.attachment'),
	(6, 'Roles Index', 'RolesController', 'admin.roles.index'),
	(7, 'Roles Add', 'RolesController', 'admin.roles.add'),
	(8, 'Roles Edit', 'RolesController', 'admin.roles.edit'),
	(9, 'Roles Save', 'RolesController', 'admin.roles.save'),
	(10, 'All Categorys', 'CategoryController', 'admin.product.category.index'),
	(11, 'Delete Category', 'CategoryController', 'admin.product.category.delete'),
	(12, 'Store Category', 'CategoryController', 'admin.product.category.store'),
	(13, 'Import Category', 'CategoryController', 'admin.product.category.import'),
	(14, 'All Brands', 'BrandController', 'admin.product.brand.index'),
	(15, 'Delete Brand', 'BrandController', 'admin.product.brand.delete'),
	(16, 'Store Brand', 'BrandController', 'admin.product.brand.store'),
	(17, 'Import Brand', 'BrandController', 'admin.product.brand.import'),
	(18, 'All Unit', 'UnitController', 'admin.product.unit.index'),
	(19, 'Delete Unit', 'UnitController', 'admin.product.unit.delete'),
	(20, 'Store Unit', 'UnitController', 'admin.product.unit.store'),
	(21, 'Import Unit', 'UnitController', 'admin.product.unit.import'),
	(22, 'All Product', 'ProductController', 'admin.product.index'),
	(23, 'Add Product', 'ProductController', 'admin.product.create'),
	(24, 'Product Edit', 'ProductController', 'admin.product.edit'),
	(25, 'Product Store', 'ProductController', 'admin.product.store'),
	(26, 'Product Alert', 'ProductController', 'admin.product.alert'),
	(27, 'Product Import', 'ProductController', 'admin.product.import'),
	(28, 'All Warehouses', 'WarehouseController', 'admin.warehouse.index'),
	(29, 'Store Warehouse', 'WarehouseController', 'admin.warehouse.store'),
	(30, 'Import Warehouses', 'WarehouseController', 'admin.warehouse.import'),
	(31, 'All Purchases', 'PurchaseController', 'admin.purchase.index'),
	(32, 'New Purchase', 'PurchaseController', 'admin.purchase.new'),
	(33, 'Edit Purchase', 'PurchaseController', 'admin.purchase.edit'),
	(34, 'Store Purchase', 'PurchaseController', 'admin.purchase.store'),
	(35, 'Download Purchase PDF', 'PurchaseController', 'admin.purchase.pdf'),
	(36, 'Purchase Update', 'PurchaseController', 'admin.purchase.update'),
	(37, 'Product Searching', 'PurchaseController', 'admin.purchase.product.search'),
	(38, 'Check Purchase Invoice', 'PurchaseController', 'admin.purchase.invoice.check'),
	(39, 'Purchase Return', 'PurchaseReturnController', 'admin.purchase.return.items'),
	(40, 'All Purchase Return', 'PurchaseReturnController', 'admin.purchase.return.index'),
	(41, 'Store Purchase Return', 'PurchaseReturnController', 'admin.purchase.return.store'),
	(42, 'Edit Purchase Return', 'PurchaseReturnController', 'admin.purchase.return.edit'),
	(43, 'Update Purchase Return', 'PurchaseReturnController', 'admin.purchase.return.update'),
	(44, 'Download Purchase Return PDF', 'PurchaseReturnController', 'admin.purchase.return.pdf'),
	(45, 'Purchase Return Search Product', 'PurchaseReturnController', 'admin.purchase.return.search.product'),
	(46, 'Purchase Return Check Invoice', 'PurchaseReturnController', 'admin.purchase.return.check.invoice'),
	(47, 'All Sales', 'SaleController', 'admin.sale.index'),
	(48, 'Create Sale', 'SaleController', 'admin.sale.create'),
	(49, 'Store Sale', 'SaleController', 'admin.sale.store'),
	(50, 'Edit Sale', 'SaleController', 'admin.sale.edit'),
	(51, 'Update Sale', 'SaleController', 'admin.sale.update'),
	(52, 'Download  Sales PDF', 'SaleController', 'admin.sale.pdf'),
	(53, 'Sale Search Product', 'SaleController', 'admin.sale.search.product'),
	(54, 'Customer Searching', 'SaleController', 'admin.sale.search.customer'),
	(55, 'Last  Sale  Invoice', 'SaleController', 'admin.sale.last.invoice'),
	(56, 'All Sales Return', 'SaleReturnController', 'admin.sale.return.index'),
	(57, 'Item Of Sale Return', 'SaleReturnController', 'admin.sale.return.items'),
	(58, 'Store Sale Return', 'SaleReturnController', 'admin.sale.return.store'),
	(59, 'Edit Sale Return', 'SaleReturnController', 'admin.sale.return.edit'),
	(60, 'Update Sale Return', 'SaleReturnController', 'admin.sale.return.update'),
	(61, 'Download Sale Return PDF', 'SaleReturnController', 'admin.sale.return.pdf'),
	(62, 'Sale Return Search Product', 'SaleReturnController', 'admin.sale.return.search.product'),
	(63, 'Sale Return Search Customer', 'SaleReturnController', 'admin.sale.return.search.customer'),
	(64, 'All Adjustments', 'AdjustmentController', 'admin.adjustment.index'),
	(65, 'Create Adjustment', 'AdjustmentController', 'admin.adjustment.create'),
	(66, 'Store Adjustment', 'AdjustmentController', 'admin.adjustment.store'),
	(67, 'Download Adjustment Details PDF', 'AdjustmentController', 'admin.adjustment.details.pdf'),
	(68, 'Edit Adjustment', 'AdjustmentController', 'admin.adjustment.edit'),
	(69, 'Update Adjustment', 'AdjustmentController', 'admin.adjustment.update'),
	(70, 'Adjustment Product Searching', 'AdjustmentController', 'admin.adjustment.search.product'),
	(71, 'All Suppliers', 'SupplierController', 'admin.supplier.index'),
	(72, 'Store Supplier', 'SupplierController', 'admin.supplier.store'),
	(73, 'Import Suppliers', 'SupplierController', 'admin.supplier.import'),
	(74, 'All Customers', 'CustomerController', 'admin.customer.index'),
	(75, 'Store Customer', 'CustomerController', 'admin.customer.store'),
	(76, 'Import Customers', 'CustomerController', 'admin.customer.import'),
	(77, 'Customer Notification Log', 'CustomerController', 'admin.customer.notification.log'),
	(78, 'Single Customer Notification', 'CustomerController', 'admin.customer.notification.single'),
	(79, 'Customer Notification Single', 'CustomerController', 'admin.customer.notification.single'),
	(80, 'Customer  All Notification', 'CustomerController', 'admin.customer.notification.all'),
	(81, 'Customer Notification  Send To All', 'CustomerController', 'admin.customer.notification.all.send'),
	(82, 'Customer Email Details', 'CustomerController', 'admin.customer.email.details'),
	(83, 'Supplier Payment Index', 'SupplierPaymentController', 'admin.supplier.payment.index'),
	(84, 'Supplier Payment Clear', 'SupplierPaymentController', 'admin.supplier.payment.clear'),
	(85, 'Store Supplier Payment', 'SupplierPaymentController', 'admin.supplier.payment.store'),
	(86, 'Store Supplier Payment Receive', 'SupplierPaymentController', 'admin.supplier.payment.receive.store'),
	(87, 'Clear Payment Of Customer', 'CustomerPaymentController', 'admin.customer.payment.clear'),
	(88, 'All Customer Payments', 'CustomerPaymentController', 'admin.customer.payment.index'),
	(89, 'Store Customer Payment', 'CustomerPaymentController', 'admin.customer.payment.store'),
	(90, 'Store  Payable Payment Of Customer', 'CustomerPaymentController', 'admin.customer.payment.payable.store'),
	(91, 'All Transfers', 'TransferController', 'admin.transfer.index'),
	(92, 'Create Transfer', 'TransferController', 'admin.transfer.create'),
	(93, 'Edit Transfer', 'TransferController', 'admin.transfer.edit'),
	(94, 'Store Transfer', 'TransferController', 'admin.transfer.store'),
	(95, 'Download Transfer Pdf', 'TransferController', 'admin.transfer.pdf'),
	(96, 'Update Transfer', 'TransferController', 'admin.transfer.update'),
	(97, 'Transfer Product Search', 'TransferController', 'admin.transfer.search.product'),
	(98, 'All Expense Types', 'ExpenseTypeController', 'admin.expense.type.index'),
	(99, 'Delete Expense Type', 'ExpenseTypeController', 'admin.expense.type.delete'),
	(100, 'Store Expense Type', 'ExpenseTypeController', 'admin.expense.type.store'),
	(101, 'Import  Expense Types', 'ExpenseTypeController', 'admin.expense.type.import'),
	(102, 'All Expenses', 'ExpenseController', 'admin.expense.index'),
	(103, 'Store Expense', 'ExpenseController', 'admin.expense.store'),
	(104, 'Import Expenses', 'ExpenseController', 'admin.expense.import'),
	(107, 'System Configuration Setting', 'GeneralSettingController', 'admin.setting.system.configuration'),
	(108, 'Logo Icon Setting', 'GeneralSettingController', 'admin.setting.logo.icon'),
	(109, 'Logo Icon Dark Setting', 'GeneralSettingController', 'admin.setting.logo.icon'),
	(110, 'Supplier  Payment Report', 'PaymentReportController', 'admin.report.payment.supplier'),
	(111, 'Customer Payment Report', 'PaymentReportController', 'admin.report.payment.customer'),
	(112, 'Product Searching', 'ProductController', 'admin.product.list'),
	(113, 'Product Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.product'),
	(114, 'Customer Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.customer'),
	(115, 'Supplier Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.supplier'),
	(116, 'Purchase Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.purchase'),
	(117, 'Purchase ReturnData Entry Report', 'DataEntryReportController', 'admin.report.data.entry.purchase.return'),
	(118, 'Sale Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.sale'),
	(119, 'Sale Return Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.sale.return'),
	(120, 'Report Data Entry Report  Adjustment', 'DataEntryReportController', 'admin.report.data.entry.adjustment'),
	(121, 'Transfer Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.transfer'),
	(122, 'Expense Data Entry  Report', 'DataEntryReportController', 'admin.report.data.entry.expense'),
	(123, 'Supplier  Payment Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.supplier.payment'),
	(124, 'Customer  Payment Data Entry Report', 'DataEntryReportController', 'admin.report.data.entry.customer.payment'),
	(127, 'Notification Templates', 'NotificationController', 'admin.setting.notification.templates'),
	(128, 'Notification Template Edit', 'NotificationController', 'admin.setting.notification.template.edit'),
	(129, 'Notification Template Update', 'NotificationController', 'admin.setting.notification.template.update'),
	(130, 'Email Notification', 'NotificationController', 'admin.setting.notification.email'),
	(131, 'Notification Email Test', 'NotificationController', 'admin.setting.notification.email.test'),
	(132, 'SMS Notification', 'NotificationController', 'admin.setting.notification.sms'),
	(133, 'Notification SMS Test', 'NotificationController', 'admin.setting.notification.sms.test'),
	(134, 'System Info', 'SystemController', 'admin.system.info'),
	(135, 'System Server Info', 'SystemController', 'admin.system.server.info'),
	(136, 'System Optimize', 'SystemController', 'admin.system.optimize'),
	(137, 'System Optimize Clear', 'SystemController', 'admin.system.optimize.clear'),
	(138, 'All Staffs', 'StaffController', 'admin.staff.index'),
	(139, 'Add Staff', 'StaffController', 'admin.staff.save'),
	(140, 'Staff Status Update', 'StaffController', 'admin.staff.status'),
	(141, 'Staff Login', 'StaffController', 'admin.staff.login'),
	(142, 'Download Product PDF', 'ProductController', 'admin.product.pdf'),
	(143, 'Download  Product CSV', 'ProductController', 'admin.product.csv'),
	(144, 'Download  Customer PDF', 'CustomerController', 'admin.customer.pdf'),
	(145, 'Download  Customer CSV', 'CustomerController', 'admin.customer.csv'),
	(146, 'Download Purchase CSV', 'PurchaseController', 'admin.purchase.csv'),
	(147, 'Download  Purchase Invoice PDF', 'PurchaseController', 'admin.purchase.invoice.pdf'),
	(148, 'Download Purchase Return CSV', 'PurchaseReturnController', 'admin.purchase.return.csv'),
	(149, 'Download Purchase Return Invoice PDF', 'PurchaseReturnController', 'admin.purchase.return.invoice.pdf'),
	(150, 'Download  Sales CSV', 'SaleController', 'admin.sale.csv'),
	(151, 'Download Sale Invoice PDF', 'SaleController', 'admin.sale.invoice.pdf'),
	(152, 'Download Sale Return CSV', 'SaleReturnController', 'admin.sale.return.csv'),
	(153, 'Download Sale Return Invoice PDF', 'SaleReturnController', 'admin.sale.return.invoice.pdf'),
	(154, 'Download Adjustment PDF', 'AdjustmentController', 'admin.adjustment.pdf'),
	(155, 'Download Adjustment CSV', 'AdjustmentController', 'admin.adjustment.csv'),
	(156, 'Download Supplier PDF', 'SupplierController', 'admin.supplier.pdf'),
	(157, 'Download Supplier CSV', 'SupplierController', 'admin.supplier.csv'),
	(158, 'Download  Supplier Payment PDF', 'SupplierPaymentController', 'admin.supplier.payment.pdf'),
	(159, 'Download Customer Payment Pdf', 'CustomerPaymentController', 'admin.customer.payment.pdf'),
	(160, 'Download Transfer CSV', 'TransferController', 'admin.transfer.csv'),
	(161, 'Download Transfer Details PDF', 'TransferController', 'admin.transfer.details.pdf'),
	(162, 'Download Expense PDF', 'ExpenseController', 'admin.expense.pdf'),
	(163, 'Download Expense CSV', 'ExpenseController', 'admin.expense.csv'),
	(164, 'Download Supplier Payment  Report PDF', 'PaymentReportController', 'admin.report.payment.supplier.pdf'),
	(165, 'Download Supplier Payment  Report CSV', 'PaymentReportController', 'admin.report.payment.supplier.csv'),
	(166, 'Download Customer  Payment Report  PDF', 'PaymentReportController', 'admin.report.payment.customer.pdf'),
	(167, 'Download Customer  Payment Report CSV', 'PaymentReportController', 'admin.report.payment.customer.csv'),
	(168, 'Stock Report', 'StockReportController', 'admin.report.stock.index'),
	(169, 'Stock Report  PDF', 'StockReportController', 'admin.report.stock.pdf'),
	(170, 'Stock Report  CSV', 'StockReportController', 'admin.report.stock.csv'),
	(177, 'Notifications', 'AdminController', 'admin.notifications'),
	(178, 'Notification Read', 'AdminController', 'admin.notification.read'),
	(179, 'Notifications Read All', 'AdminController', 'admin.notifications.read.all'),
	(180, 'Notifications Delete All', 'AdminController', 'admin.notifications.delete.all'),
	(181, 'Notifications Delete Single', 'AdminController', 'admin.notifications.delete.single'),
	(183, 'System Setting', 'GeneralSettingController', 'admin.setting.system'),
	(184, 'General Setting', 'GeneralSettingController', 'admin.setting.general'),
	(189, 'Sitemap Setting', 'GeneralSettingController', 'admin.setting.sitemap'),
	(191, 'Robot Setting', 'GeneralSettingController', 'admin.setting.robot'),
	(193, 'Notification Global Email', 'NotificationController', 'admin.setting.notification.global.email'),
	(194, 'Notification Global Email Update', 'NotificationController', 'admin.setting.notification.global.email.update'),
	(195, 'Notification Global SMS', 'NotificationController', 'admin.setting.notification.global.sms'),
	(196, 'Notification Global SMS Update', 'NotificationController', 'admin.setting.notification.global.sms.update'),
	(201, 'System Update', 'SystemController', 'admin.system.update'),
	(202, 'System Update Process', 'SystemController', 'admin.system.update.process'),
	(203, 'System Update Log', 'SystemController', 'admin.system.update.log'),
	(204, 'Banned', 'AdminController', 'admin.banned'),
	(205, 'Request Report Store', 'AdminController', 'admin.request.report.store'),
	(206, 'General Update Setting', 'GeneralSettingController', 'admin.setting.general.update'),
	(207, 'System Configuration Update Setting', 'GeneralSettingController', 'admin.setting.system.configuration.update'),
	(208, 'Notification Email Update', 'NotificationController', 'admin.setting.notification.email.update'),
	(209, 'Setting Notification SMS Update', 'NotificationController', 'admin.setting.notification.sms.update'),
	(210, 'Sitemap Update Setting', 'GeneralSettingController', 'admin.setting.sitemap.update'),
	(211, 'Robot Update Setting', 'GeneralSettingController', 'admin.setting.robot.update'),
	(212, 'Warehouse Status', 'WarehouseController', 'admin.warehouse.status'),
	(213, 'Customer List', 'CustomerController', 'admin.customer.list'),
	(214, 'Customer Segment Count', 'CustomerController', 'admin.customer.segment.count'),
	(215, 'Customer Notification History', 'CustomerController', 'admin.customer.notification.history'),
	(216, 'Extensions Index', 'ExtensionController', 'admin.extensions.index'),
	(217, 'Extensions Update', 'ExtensionController', 'admin.extensions.update'),
	(218, 'Extensions Status', 'ExtensionController', 'admin.extensions.status'),
	(219, 'Chart Purchase Sale', 'AdminController', 'admin.chart.purchase.sale'),
	(220, 'Chart Sales Return', 'AdminController', 'admin.chart.sales.return'),
	(221, 'Chart Purchases Return', 'AdminController', 'admin.chart.purchases.return');

-- Dumping structure for table dewanchemicalokr.permission_role
CREATE TABLE IF NOT EXISTS `permission_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.permission_role: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Name',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `sku` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stock-keeping-unit',
  `alert_quantity` int unsigned DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_sale` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.products: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.product_stocks
CREATE TABLE IF NOT EXISTS `product_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `warehouse_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.product_stocks: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.purchases
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned NOT NULL,
  `invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` int unsigned NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `total_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `payable_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `paid_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.purchases: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.purchase_details
CREATE TABLE IF NOT EXISTS `purchase_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` double(28,8) NOT NULL DEFAULT '0.00000000',
  `total` double(28,8) NOT NULL DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.purchase_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.purchase_returns
CREATE TABLE IF NOT EXISTS `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int unsigned NOT NULL,
  `supplier_id` int unsigned NOT NULL,
  `return_date` date NOT NULL,
  `total_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `receivable_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `received_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.purchase_returns: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.purchase_return_details
CREATE TABLE IF NOT EXISTS `purchase_return_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` double(28,8) NOT NULL DEFAULT '0.00000000',
  `total` double(28,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.purchase_return_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.roles: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.sales
CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` int unsigned NOT NULL,
  `sale_date` date NOT NULL,
  `total_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `receivable_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `received_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.sales: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.sale_details
CREATE TABLE IF NOT EXISTS `sale_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.sale_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.sale_returns
CREATE TABLE IF NOT EXISTS `sale_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `return_date` date NOT NULL,
  `total_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `payable_amount` decimal(28,8) NOT NULL,
  `paid_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.sale_returns: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.sale_return_details
CREATE TABLE IF NOT EXISTS `sale_return_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_return_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` double(28,8) NOT NULL DEFAULT '0.00000000',
  `total` double(28,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.sale_return_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `company_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_email_unique` (`email`),
  UNIQUE KEY `suppliers_mobile_unique` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.suppliers: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.supplier_payments
CREATE TABLE IF NOT EXISTS `supplier_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int unsigned NOT NULL DEFAULT '0',
  `purchase_id` int unsigned DEFAULT NULL,
  `purchase_return_id` int unsigned DEFAULT NULL,
  `amount` decimal(28,2) unsigned NOT NULL DEFAULT '0.00',
  `trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.supplier_payments: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.transfers
CREATE TABLE IF NOT EXISTS `transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tracking_no` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_warehouse_id` int unsigned NOT NULL,
  `to_warehouse_id` int unsigned NOT NULL,
  `transfer_date` date DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.transfers: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.transfer_details
CREATE TABLE IF NOT EXISTS `transfer_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.transfer_details: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.units
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.units: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.update_logs
CREATE TABLE IF NOT EXISTS `update_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `update_log` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dewanchemicalokr.update_logs: ~0 rows (approximately)

-- Dumping structure for table dewanchemicalokr.warehouses
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '''Active''=>1 and ''Deactive''=>0 ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dewanchemicalokr.warehouses: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
