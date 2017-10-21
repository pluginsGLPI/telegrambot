<?php
/*
 -------------------------------------------------------------------------
 TelegramBot plugin for GLPI
 Copyright (C) 2017 by the TelegramBot Development Team.

 https://github.com/pluginsGLPI/telegrambot
 -------------------------------------------------------------------------

 LICENSE

 This file is part of TelegramBot.

 TelegramBot is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 TelegramBot is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with TelegramBot. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_telegrambot_install() {
   global $DB;

   $DB->runFile(GLPI_ROOT . '/plugins/telegrambot/db/install.sql');

   Config::setConfigurationValues('core', ['notifications_websocket' => 0]);
   Config::setConfigurationValues('plugin:telegrambot', ['token' => '', 'bot_username' => '']);

   CronTask::register(
      'PluginTelegrambotCron',
      'messagelistener',
      5 * MINUTE_TIMESTAMP,
      array('comment' => '', 'mode' => CronTask::MODE_EXTERNAL)
   );

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_telegrambot_uninstall() {
   global $DB;
   $DB->runFile(GLPI_ROOT . '/plugins/telegrambot/db/uninstall.sql');

   $config = new Config();
   $config->deleteConfigurationValues('core', ['notifications_websocket']);
   $config->deleteConfigurationValues('plugin:telegrambot', ['token', 'bot_username']);

   return true;
}

function add_username_field(array $params) {
   $item = $params['item'];

   if ($item->getType() == 'User') {
      PluginTelegrambotUser::showUsernameField($item);
   }
}
