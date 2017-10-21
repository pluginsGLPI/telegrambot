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

class PluginTelegrambotUser extends CommonDBTM {

   static public function showUsernameField($item) {
      $username = null;

      if ($item->fields['id']) {
         $user = new self();
         $user->getFromDB($item->fields['id']);

         if (!$user->isNewItem()) {
            $username = $user->fields['username'];
         }
      }

      $out = "<tr class='tab_bg_1'>";
      $out .= "<td> " . __('Telegram username') . "</td>";
      $out .= "<td><input type='text' name='telegram_username' value='$username'></td>";
      $out .= "</tr>";

      echo $out;
   }

   static public function item_add_user(User $item) {
      if ($item->input['telegram_username']) {
         $user = new self;
         $user->fields['id'] = $item->fields['id'];
         $user->fields['username'] = $item->input['telegram_username'];
         $user->addToDB();
      }
   }

   static public function item_update_user(User $item) {
      $user = new self;
      $user->getFromDB($item->fields['id']);

      if ($user->isNewItem()) {
         self::item_add_user($item);
      } else {
         $user->fields['username'] = $item->input['telegram_username'];
         $user->updateInDB(array('username'));
      }
   }

}
