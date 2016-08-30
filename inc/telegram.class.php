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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginTelegram extends CommonDBTM {
   private $bot_token;
   private $bot_url = 'https://api.telegram.org/bot';

   public function __construct($bot_token) {
      $this->bot_token  = $bot_token;
      $this->bot_url    .= $bot_token;
   }

   public function get_me() {
      return $this->send_api_request('getMe', array());
   }

   public function send_message($chat_id, $message) {
      $content = array('chat_id' => $chat_id, 'text' => $message);
      return $this->send_api_request('sendMessage', $content);
   }

   public function get_updates($offset=null, $limit=100, $timeout=0) {
      $content = array(
         'offset'    => $offset,
         'limit'     => $limit,
         'timeout'   => $timeout
      );

      return $this->send_api_request('getUpdates', $content);
   }

   public function handle_get_updates() {
      global $DB;

      $query = "SELECT MAX(`update_id`) AS `update_id` FROM `glpi_plugin_telegrambot_messages`";
      $result = $DB->query($query);
      $last_update_id = 1 + (int) $DB->result($result, 0, 'update_id');

      $messages = $this->get_updates($last_update_id);

      if($messages['ok']) {
         foreach ($messages['result'] as $key => $value) {
            $data = array(
               'update_id'  => $value['update_id'],
               'message_id' => $value['message']['message_id'],
               'user_id'    => $value['message']['from']['id'],
               'date'       => $value['message']['date'],
               'text'       => $value['message']['text'],
               'first_name' => $value['message']['from']['first_name'],
               'last_name'  => $value['message']['from']['last_name'],
               'username'   => $value['message']['from']['username']
            );

            $this->process_update($data);
         }
      }
   }

   private function send_api_request($action, array $content) {
      $url = "$this->bot_url/$action";

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response, true);
   }

   private function process_update($data) {
      $this->insert_user(
         $data['user_id'],
         $data['first_name'],
         $data['last_name'],
         $data['username']
      );

      $this->insert_message(
         $data['update_id'],
         $data['message_id'],
         $data['user_id'],
         date('Y-m-d H:i:s', $data['date']),
         $data['text']
      );

      print_r($data);
   }

   private function insert_user($user_id, $first_name, $last_name, $username) {
      global $DB;

      $result = $DB->query("SELECT * FROM `glpi_plugin_telegrambot_users` WHERE `id` = $user_id");
      $user_exists = $DB->numrows($result);

      if(!$user_exists) {
         $query = "INSERT INTO `glpi_plugin_telegrambot_users`
                  (`id`, `first_name`, `last_name`, `username`)
                  VALUES($user_id, '$first_name', '$last_name', '$username')";

         $DB->query($query);
      }
   }

   private function insert_message($update_id, $message_id, $user_id, $date, $text) {
      global $DB;

      $query = "INSERT INTO `glpi_plugin_telegrambot_messages`
      (`update_id`, `message_id`, `user_id`, `date`, `text`)
      VALUES($update_id, $message_id, $user_id, '$date', '$text')";

      $DB->query($query);
   }
}

?>
