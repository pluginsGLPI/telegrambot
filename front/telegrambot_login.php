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
 * 
 */

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

include ('../../../inc/includes.php');

$token_login_data = PluginTelegrambotUser::validateTemporaryAccessToken();

if(!is_array($token_login_data)){
   // Redirect to login page
   echo $token_login_data;
   $url = $protocol . $_SERVER['SERVER_NAME'] . '/index.php';
   header('Location: '.$url);
}

$auth = new Auth();

// now we can continue with the process...
if ($auth->login($token_login_data['user'], $token_login_data['pass'], false, false, 'local')) {
  Auth::redirectIfAuthenticated();
} else {
  // we have done at least a good login? No, we exit.
  Html::nullHeader("Login", $CFG_GLPI["root_doc"] . '/index.php');
  echo '<div class="center b">' . $auth->getErr() . '<br><br>';
  // Logout whit noAUto to manage auto_login with errors
  echo '<a href="' . $CFG_GLPI["root_doc"] . '/front/logout.php?noAUTO=1'.
        str_replace("?", "&", $REDIRECT).'">' .__('Log in again') . '</a></div>';
  Html::nullFooter();
  exit();
}