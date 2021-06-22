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

class PluginTelegrambotNotificationEventWebsocket
      extends NotificationEventAbstract
      implements NotificationEventInterface {

   static public function getTargetFieldName() {
      return 'users_id';
   }

   static public function getTargetField(&$data) {
      $field = self::getTargetFieldName();

      if (!isset($data[$field])) {
         $data[$field] = null;
      }

      return $field;
   }

   static public function canCron() {
      return true;
   }

   static public function getAdminData() {
      return false;
   }

   static public function getEntityAdminsData($entity) {
      return false;
   }

   static public function send(array $data) {
    global $CFG_GLPI, $DB;
    $processed = [];
    $errorcount=0;

//       $logfile = GLPI_LOG_DIR."/telegrambot.log";
//              if (!file_exists($logfile)) {
//                $newfile = fopen($logfile, 'w+');
//                 fclose($newfile);
//                 }
// error_log(date("Y-m-d H:i:s")."INFO: Starting sending messages to telegramm...\n", 3, $logfile);
       PluginTelegrambotBot::setTelegramBot();
      $errorcount_limit = PluginTelegrambotBot::getConfig('messagecount');
      $store_limit = PluginTelegrambotBot::getConfig('minutes_to_store_mess');
    foreach ($data as $row) {
       $current = new QueuedNotification();
       $current->getFromResultSet($row);
       $to = $current->getField('recipient');
       $content = $current->getField('body_text');
       $temp = $current->getField('name');
       try {
           PluginTelegrambotBot::sendMessage($to, $content);
          $processed[] = $current->getID();
          $current->update(['id'        => $current->fields['id'],
          'sent_time' => $_SESSION['glpi_currenttime']]);
          $current->delete(['id'        => $current->fields['id']]);
       } catch (Exception $e) {
                    $create_time= strtotime($current->getField('create_time'));
                    $current_time= strtotime($_SESSION['glpi_currenttime']);
                    $minutes = round(abs($current_time - $create_time)/60,0);
                    if ($minutes>$store_limit) {
                        $current->update(['id'        => $current->fields['id']]);
                        $current->delete(['id'        => $current->fields['id']]);
                    }
                    $errorcount+=1;
                    if ($errorcount>$errorcount_limit){
                        $logfile = GLPI_LOG_DIR."/telegrambot.log";
                        if (!file_exists($logfile)) {
                           $newfile = fopen($logfile, 'w+');
                           fclose($newfile); 
                        }
                        error_log(date("Y-m-d H:i:s")." - ERROR: ".$e->getMessage()."\n", 3, $logfile);
                        return 0;
         }
      }
 //     error_log(date("Y-m-d H:i:s")."INFO: End sending messages to telegramm.\n", 3, $logfile);
   }
   return count($processed);
  }
}
