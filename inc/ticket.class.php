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

class PluginTelegrambotTicket extends CommonDBTM 
{

   /**
    * Add a new Ticket via Telegrambot.
    * 
    * @param string $text
    * @return boolean|string
    */
   static public function newTicket($chat_id, $user_chat, $text) 
   {
       if ($text === '') {
           return false;
       }

       $new_ticket_data = json_decode($text);

       if (json_last_error() !== JSON_ERROR_NONE) {
           return <<<TXT
String format invalid.
Correct format:
/newticket {"title": "<tickettitle>", "description": "<ticketdescription>"}
TXT;
       }

       $glpi_user = PluginTelegrambotUser::getGlpiUserByTelegramUsername($user_chat);
       $glpi_user_id = $glpi_user['id'];
       if (!$glpi_user_id) {
           return 'Your user was not found. You can\'t create a new ticket.';
       }
       
       global $DB;

       try {

           $DB->beginTransaction();

           $table = 'glpi.glpi_tickets';

           $params = [
               'name' => $new_ticket_data->title,
               'date' => date("Y-m-d H:i:s"),
               'date_mod' => date("Y-m-d H:i:s"),
               'users_id_lastupdater' => $glpi_user_id,
               'status' => 2,
               'users_id_recipient' => $glpi_user_id,
               'requesttypes_id' => 1,
               'content' => $new_ticket_data->description,
               'urgency' => 3,
               'impact' => 3,
               'priority' => 3,
               'date_creation' => date("Y-m-d H:i:s"),
           ];

           $result = $DB->insert($table, $params);

           if (!$result) {
               throw new Exception("Your Ticket could not be saved to the database. Error to insert Ticket data.");
           }

           $ticket_id = $DB->insert_id();

           $table = 'glpi.glpi_tickets_users';

           $params = [
               'tickets_id' => $ticket_id,
               'users_id' => $glpi_user_id,
               'type' => 1
           ];

           $result = $DB->insert($table, $params);
           if (!$result) {
               throw new Exception("Your Ticket could not be saved to the database. Error to insert Ticket user data.");
           }

           $params = [
               'tickets_id' => $ticket_id,
               'users_id' => $glpi_user_id,
               'type' => 2
           ];

           $result = $DB->insert($table, $params);
           if (!$result) {
               throw new Exception("Your Ticket could not be saved to the database. Error to insert Ticket user data.");
           }

           $DB->commit();

           return "Your Ticket was successfully saved. Ticket ID: $ticket_id.";
       } catch (Exception $ex) {
           $DB->rollBack();
           return $ex->getMessage();
       }
   }

   /**
    * Search for a ticket via Telegrambot.
    * 
    * @param string $text
    * @return boolean|string
    */
   static public function searchTicket($text) 
   {
       $ticket_id = (int) $text;

       if ($text === '' || $ticket_id == 0) {
           return false;
       }

       $response = 'Ticket not found.';

       $ticket_data = self::getTicketData($ticket_id);

       if ($ticket_data) {
           $ticket_id = $ticket_data['id'];
           $ticket_title = $ticket_data['name'];
           $ticket_description = strip_tags(html_entity_decode($ticket_data['content']));

           $response = <<<RESPONSE
Ticket search result:
ID: {$ticket_id}
Title: {$ticket_title}
Description: {$ticket_description}
RESPONSE;
       }

       return $response;
   }

   /**
    * Add a new followup to a Ticket via Telegrambot.
    * 
    * @global object $DB
    * @param int $chat_id
    * @param string $user_chat
    * @param string $text
    * @return boolean|string
    */
   static public function newFollowup($chat_id, $user_chat, $text) 
   {

       if ($text === '' || strpos($text, '**') === false) {
           return false;
       }

       $text_parts = explode('**', $text);
       $ticket_id = (int) trim($text_parts[0]);
       $followup_text = trim($text_parts[1]);

       if ($ticket_id == 0 || empty($followup_text)) {
           return false;
       }

       $ticket_data = self::getTicketData($ticket_id);
       if (!$ticket_data) {
           return "Ticket ID $ticket_id not found.";
       }

       $glpi_user = PluginTelegrambotUser::getGlpiUserByTelegramUsername($user_chat);
       $glpi_user_id = $glpi_user['id'];
       if (!$glpi_user_id) {
           return "Your user was not found. You can not create a new followup.";
       }

       global $DB;

       $followup_text = "<p>$followup_text</p>";

       $table = 'glpi.glpi_itilfollowups';

       $params = [
           'itemtype' => 'Ticket',
           'items_id' => $ticket_id,
           'date' => date("Y-m-d H:i:s"),
           'users_id' => $glpi_user_id,
           'content' => htmlentities($followup_text),
           'requesttypes_id' => 1,
           'date_mod' => date("Y-m-d H:i:s"),
           'date_creation' => date("Y-m-d H:i:s"),
           'timeline_position' => 1,
           'sourceitems_id' => 0,
           'sourceof_items_id' => 0
       ];

       $result = $DB->insert($table, $params);

       if (!$result) {
           return "Your followup could not be saved to the database.";
       }

       return "Your followup was successfully saved to the Ticket $ticket_id.";
   }

   /**
    * Return the ticket data.
    * 
    * @global object $DB
    * @param int $ticket_id
    * @return boolean|array
    */
   static private function getTicketData($ticket_id) 
   {
       global $DB;

       $iterator = $DB->request([
           'FROM' => 'glpi.glpi_tickets',
           'WHERE' => [
               'glpi.glpi_tickets.id' => $ticket_id
           ],
           'LIMIT' => 1
       ]);

       if ($ticket_data = $iterator->next()) {
           return $ticket_data;
       }

       return false;
   }

}
