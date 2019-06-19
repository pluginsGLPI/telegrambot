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

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/newfollowup" command
 */
class NewfollowupCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'newfollowup';

    /**
     * @var string
     */
    protected $description = 'Add a new followup to a ticket on GLPI via TelegramBot.';

    /**
     * @var string
     */
    protected $usage = '/newfollowup <ticket_id>**<text>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();
        $user_chat = $message->getFrom()->getUsername();
        $text      = trim($message->getText(true));
        
        $response = \PluginTelegrambotTicket::newFollowup($chat_id, $user_chat, $text);
        
        if(!$response){
            $response = 'Command usage: ' . $this->getUsage();
        }
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $response,
        ];

        return Request::sendMessage($data);
    }
}