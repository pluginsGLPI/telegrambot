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

class PluginTelegrambotCron {

   static function getTypeName($nb=0) {
      return 'TelegramBot';
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'messagelistener':
            return array('description' => __('Handles incoming bot messages', 'telegrambot'));
      }

      return array();
   }

   static function cronMessageListener($task) {
      $response = PluginTelegrambotBot::getUpdates();

      if ($response == 'ok') {
         $success = 1;
         $message = 'Action successfully completed';
      } else {
         $success = 0;
         $message = "Error. $response";
      }

      $task->log($message);
      return $success;
   }

}
