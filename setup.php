<?php
/*
* @version $Id: HEADER 15930 2011-10-25 10:47:55Z jmd $
-------------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
Copyright (C) 2003-2011 by the INDEPNET Development Team.

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

// Init the hooks of the plugin
function plugin_init_telegrambot() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   Plugin::registerClass('PluginTelegram');
   Plugin::registerClass('PluginTelegramNotification');
}

// Get the name and the version of the plugin
function plugin_version_telegrambot() {
   return array(
      'name'            => 'Telegram Bot',
      'version'         => '1.0.0',
      'author'          => "<a href='http://trulymanager.com'>Truly Systems</a>",
      'license'         => 'GPLv2+',
      'homepage'        => 'https://github.com/pluginsGLPI/telegrambot',
      'minGlpiVersion'  => '0.90'
   );
}

// Check prerequisites before install the plugin
function plugin_telegrambot_check_prerequisites() {
   if(version_compare(GLPI_VERSION, '0.90', 'lt')) {
      echo "This plugin requires GLPI >= 0.90";
      return false;
   }

   return true;
}

// Check configuration process for plugin
function plugin_telegrambot_check_config($verbose=false) {
   // TODO
   return true;
}

?>
