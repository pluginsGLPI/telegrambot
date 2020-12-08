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

require GLPI_ROOT . '/plugins/telegrambot/vendor/autoload.php';
use Longman\TelegramBot\Request;

class PluginTelegrambotBot {

   static public function getConfig($key) {
      return Config::getConfigurationValues('plugin:telegrambot')[$key];
   }

   static public function setConfig($key, $value) {
      Config::setConfigurationValues('plugin:telegrambot', [$key => $value]);
   }

   static public function sendMessage($to, $content) {
      $test_conn = self::isSiteAvailable("https://api.telegram.org/", 2) ? true : false;
      if (!$test_conn) {
         $logfile="/var/www/glpi/files/_log/telegrambot.log";
         if (!file_exists($logfile)) {
            $newfile = fopen($logfile, 'w+');
            fclose($newfile); 
         }
         error_log(date("Y-m-d H:i:s")." - ERROR: Telegram API is unavailable now!\n", 3, $logfile);
         return;
      }
      $chat_id = self::getChatID($to);
      $telegram = self::getTelegramInstance();
      $result = Request::sendMessage(['chat_id' => $chat_id, 'text' => $content]);
   }

   static public function getUpdates() {
      $response = 'ok';

      try {
         $telegram = self::getTelegramInstance();
         $telegram->enableMySql(self::getDBCredentials(), 'glpi_plugin_telegrambot_');
         $telegram->handleGetUpdates();
      } catch (Longman\TelegramBot\Exception\TelegramException $e) {
         $response = $e->getMessage();
      }

      return $response;
   }

   static public function getChatID($user_id) {
      global $DB;

      $chat_id = null;

      $result = $DB->request([
         'FROM' => 'glpi_plugin_telegrambot_users',
         'INNER JOIN' => [
            'glpi_plugin_telegrambot_user' => [
               'FKEY' => [
                  'glpi_plugin_telegrambot_users' => 'username',
                  'glpi_plugin_telegrambot_user' => 'username'
               ]
            ]
         ],
         'WHERE' => ['glpi_plugin_telegrambot_users.id' => $user_id]
      ]);

      if ($row = $result->next()) {
         $chat_id = $row['id'];
      }

      return $chat_id;
   }

   static private function getTelegramInstance() {
      $bot_api_key  = self::getConfig('token');
      $bot_username = self::getConfig('bot_username');

      return new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
   }

   static private function getDBCredentials() {
      global $DB;

      return array(
         'host'     => $DB->dbhost,
         'user'     => $DB->dbuser,
         'password' => $DB->dbpassword,
         'database' => $DB->dbdefault,
      );
   }
   
   /**
     * URL availability check 
     *
     * @param string $url URL to check
     * @param int $timeout connection timeout in seconds
     *
     * @return bool true - URL available, false - not available
     */
   static private function isSiteAvailable($url, $timeout) {
      if(!filter_var($url, FILTER_VALIDATE_URL)){
         return false;
      }
  
      $curlInit = curl_init($url);
      curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,$timeout);
      curl_setopt($curlInit,CURLOPT_HEADER,true);
      curl_setopt($curlInit,CURLOPT_NOBODY,true);
      curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
      $response = curl_exec($curlInit);
      curl_close($curlInit);
      return $response ? true : false;
    }

}
