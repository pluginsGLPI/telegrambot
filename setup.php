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

define('PLUGIN_TELEGRAMBOT_VERSION', '3.0.0');

/**
 * Init the hooks of the plugins.
 *
 * @return void
 */
function plugin_init_telegrambot()
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['post_item_form']['telegrambot'] = 'add_username_field';
   $PLUGIN_HOOKS['item_add']['telegrambot'] = [
       'User' => ['PluginTelegrambotUser', 'item_add_user']
   ];
   $PLUGIN_HOOKS['pre_item_update']['telegrambot'] = [
       'User' => ['PluginTelegrambotUser', 'item_update_user']
   ];

   $plugin = new Plugin();

   if ($plugin->isActivated('telegrambot')) {
       Notification_NotificationTemplate::registerMode(
           Notification_NotificationTemplate::MODE_WEBSOCKET,
           __('telegram', 'plugin_telegrambot'),
           'telegrambot'
       );
   }
}

/**
 * Get the name and the version of the plugin.
 *
 * @return array
 */
function plugin_version_telegrambot()
{
   return [
       'name' => 'TelegramBot',
       'version' => PLUGIN_TELEGRAMBOT_VERSION,
       'author' => '<a href="http://trulymanager.com" target="_blank">Truly Systems</a>',
       'license' => 'GLPv3',
       'homepage' => 'https://github.com/pluginsGLPI/telegrambot',
       'requirements' => [
           'glpi' => [
               'min' => '9.4'
               // params: an array of GLPI parameters names that must be set (not empty, not null, not false),
               // 'params' => []
           ],
           'php' => [
               'min' => '7.0',
               'exts' => [
                   'mysqli' => [
                       'required' => true
                   ],
                   'fileinfo' => [
                       'required' => true,
                       'class' => 'finfo'
                   ],
                   'json' => [
                       'required' => true,
                       'function' => 'json_encode'
                   ]
               ]
               // params: an array of parameters name that must be set (retrieved from ini_get())
               //'params' => []
           ],
       ]
   ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_telegrambot_check_prerequisites()
{
   if (version_compare(GLPI_VERSION, '9.4', 'lt')) {
       if (method_exists('Plugin', 'messageIncompatible')) {
           echo Plugin::messageIncompatible('core', '9.4');
       } else {
           echo "This plugin requires GLPI >= 9.4";
       }
       return false;
   }
   return true;
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 * 
 * @param boolean $verbose
 * @return boolean
 */
function plugin_telegrambot_check_config($verbose = false)
{
   if (true) { // Your configuration check
       return true;
   }

   if ($verbose) {
       echo "Installed, but not configured";
   }
   return false;
}