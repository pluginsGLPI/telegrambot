<?php
/*
* @version $Id: HEADER 15930 2011-10-25 10:47:55Z jmd $
-------------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
Copyright (C) 2003-2016 by the INDEPNET Development Team.

http://indepnet.net/   http://glpi-project.org
-------------------------------------------------------------------------

LICENSE

This file is part of GLPI.

GLPI is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GLPI is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GLPI. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
 */

function plugin_telegrambot_install() {
   global $DB;

   if(!TableExists('glpi_plugin_telegrambot_configs')) {
      $query = "CREATE TABLE `glpi_plugin_telegrambot_configs` (
                   `id` INT(11) NOT NULL AUTO_INCREMENT,
                   `name` VARCHAR(100) NOT NULL,
                   `value` VARCHAR(100),
                   PRIMARY KEY(`id`)
               ) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";
      $DB->query($query) or die('error glpi_plugin_telegrambot_configs ' . $DB->error());

      $query = "INSERT INTO `glpi_plugin_telegrambot_configs`(`name`) VALUES('admin_username')";
      $DB->query($query) or die('error populate glpi_plugin_telegrambot_configs ' . $DB->error());
   }

   if(!TableExists('glpi_plugin_telegrambot_users')) {
      $query = "CREATE TABLE `glpi_plugin_telegrambot_users` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         `first_name` VARCHAR(255) NOT NULL,
         `last_name` VARCHAR(255),
         `username` VARCHAR(255),
         PRIMARY KEY(`id`)
      ) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";

      $DB->query($query) or die('error glpi_plugin_telegrambot_users ' . $DB->error());
   }

   if(!TableExists('glpi_plugin_telegrambot_messages')) {
      $query = "CREATE TABLE `glpi_plugin_telegrambot_messages` (
         `update_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
         `message_id` BIGINT(20) NOT NULL,
         `user_id` INT(11),
         `date` DATETIME NOT NULL,
         `text` TEXT,
         PRIMARY KEY(`update_id`),
         FOREIGN KEY(`user_id`) REFERENCES `glpi_plugin_telegrambot_users`(`id`)
      ) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";

      $DB->query($query) or die('error glpi_plugin_telegrambot_messages ' . $DB->error());
   }

   return true;
}

function plugin_telegrambot_uninstall() {
   global $DB;

   if(TableExists('glpi_plugin_telegrambot_configs')) {
      $query = "DROP TABLE `glpi_plugin_telegrambot_configs`";
      $DB->query($query) or die('error deleting glpi_plugin_telegrambot_configs');
   }

   if(TableExists('glpi_plugin_telegrambot_messages')) {
      $query = "DROP TABLE `glpi_plugin_telegrambot_messages`";
      $DB->query($query) or die('error deleting glpi_plugin_telegrambot_messages');
   }

   if(TableExists('glpi_plugin_telegrambot_users')) {
      $query = "DROP TABLE `glpi_plugin_telegrambot_users`";
      $DB->query($query) or die('error deleting glpi_plugin_telegrambot_users');
   }

   return true;
}

?>
