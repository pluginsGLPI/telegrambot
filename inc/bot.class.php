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

class PluginTelegrambotBot 
{

   /**
    * @var array Commands list
    */
   static $commands_list = [
       '/newticket',
       '/searchticket',
       '/newfollowup',
       '/login',
       '/help',
       '/echo',
   ];
   
   /**
    * @var array Path to Commands classes folder
    */
   static $commands_paths = [
       __DIR__ . '/Commands/',
   ];

   static public function getConfig($key) 
   {
       return Config::getConfigurationValues('plugin:telegrambot')[$key];
   }

   static public function setConfig($key, $value) 
   {
       Config::setConfigurationValues('plugin:telegrambot', [$key => $value]);
   }

   static public function sendMessage($to, $content) 
   {
       $chat_id = self::getChatID($to);
       $telegram = self::getTelegramInstance();
       $result = Request::sendMessage(['chat_id' => $chat_id, 'text' => $content]);
   }

   static public function getUpdates() 
   {

       try {
           $telegram = self::getTelegramInstance();
           $telegram->enableMySql(self::getDBCredentials(), 'glpi_plugin_telegrambot_');
           $response = $telegram->handleGetUpdates();

           $response = 'ok';
       } catch (Longman\TelegramBot\Exception\TelegramException $e) {
           $response = $e->getMessage();
           // self::printException($e);
       }

       return $response;
   }

   static public function getChatID($user_id) 
   {
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

   static private function getTelegramInstance() 
   {

       $bot_api_key = self::getConfig('token');
       $bot_username = self::getConfig('bot_username');
       
       try {

           // Create Telegram API object
           $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

           // Add commands paths containing our custom commands
           $telegram->addCommandsPaths(self::$commands_paths);
           
           self::setTelegrambotWebhook($telegram);

           return $telegram;
       } catch (Longman\TelegramBot\Exception\TelegramException $e) {
           // Log telegram errors
           Longman\TelegramBot\TelegramLog::error($e);
           // self::printException($e);
       } catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
           // Silence is golden!
           // self::printException($e);
       }
   }

   /**
    * Configure webhook to respond to the user without having to go through CronTask.
    * @todo Set local server to work with HTTPS and SSL.
    * @see: 
    * Simpler solution: https://gist.github.com/jfloff/5138826
    * Harder solution: https://really-simple-ssl.com/knowledge-base/how-to-install-an-ssl-certificate-on-mamp/
    */
   static private function setTelegrambotWebhook(&$telegram) 
   {
       
       $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

       if($protocol == 'http://'){
           //'ATTENTION: For this webhook work properly you must use HTTPS protocol.'
           return false;
       }
       
       $hook_url = 'https://' . $_SERVER['SERVER_NAME'] . '/plugins/telegrambot/front/telegrambot_hook.php';

       try {
           
           $result = $telegram->setWebhook($hook_url);
           if ($result->isOk()) {
               //echo $result->getDescription();
               return true;
           }
       } catch (Longman\TelegramBot\Exception\TelegramException $e) {
           // Silence is golden!
           // log telegram errors
           Longman\TelegramBot\TelegramLog::error($e);
           return false;
           // self::printException($e);
           
       }

   }

   static private function getDBCredentials() 
   {
       global $DB;

       return [
           'host' => $DB->dbhost,
           'user' => $DB->dbuser,
           'password' => $DB->dbpassword,
           'database' => $DB->dbdefault,
       ];
   }
   
   /**
    * Just print the Exception.
    * Only for Developer use!!!
    * 
    * @param object $e Exception
    */
   static private function printException($e) 
   {
        echo '<pre>';
        echo 'Error code: '.$e->getCode();
        echo '<br>';
        echo 'Error Message: '.$e->getMessage();
        echo '<br>';
        var_dump($e);
        die;
       
   }

}