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

class PluginTelegrambotNotificationWebsocket implements NotificationInterface {

   static function check($value, $options = []) {
      return true;
   }

   static function testNotification() {
      // TODO
   }

   function sendNotification($options=array()) {
      
      $data = array();
      $data['itemtype']                             = $options['_itemtype'];
      $data['items_id']                             = $options['_items_id'];
      $data['notificationtemplates_id']             = $options['_notificationtemplates_id'];
      $data['entities_id']                          = $options['_entities_id'];
      $data['sendername']                           = $options['fromname'];
      $data['name']                                 = $options['subject'];
      $data['body_text']                            = $options['content_text'];
      $data['recipient']                            = $options['to'];
      if (!empty($options['content_html'])) {
         $data['body_html'] = $options['content_html'];
      }

      $data['mode'] = Notification_NotificationTemplate::MODE_WEBSOCKET;

      $mailqueue = new QueuedNotification();

      if (!$mailqueue->add(Toolbox::addslashes_deep($data))) {
         Session::addMessageAfterRedirect(__('Error inserting Telegram notification to queue', 'Telegrambot'), true, ERROR);
         return false;
      } else {
         //TRANS to be written in logs %1$s is the to email / %2$s is the subject of the mail
         Toolbox::logInFile("notification",
                           sprintf(__('%1$s: %2$s'),
                                    sprintf(__('An Telegram notification to %s was added to queue', 'Telegrambot'),
                                          $options['to']),
                                    $options['subject']."\n"));
      }

   }

}
