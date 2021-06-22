<?php

include ('../../../inc/includes.php');

Session::checkRight('config', UPDATE);
$notificationwebsocket = new PluginTelegrambotNotificationWebsocketSetting();

// TODO
if (!empty($_POST['test_webhook_send'])) {
   PluginTelegrambotNotificationWebsocket::testNotification();
   Html::back();
} else if (!empty($_POST['update'])) {
   PluginTelegrambotBot::setConfig('token', $_POST['token']);
   PluginTelegrambotBot::setConfig('bot_username', $_POST['bot_username']);
PluginTelegrambotBot::setConfig('messagecount', $_POST['errorcount_limit']);
PluginTelegrambotBot::setConfig('minutes_to_store_mess', $_POST['store_limit']);
PluginTelegrambotBot::setConfig('base_uri', $_POST['base_uri']);
PluginTelegrambotBot::setConfig('http_delay', $_POST['http_delay']);
   Html::back();
}

Html::header(
   Notification::getTypeName(Session::getPluralNumber()),
   $_SERVER['PHP_SELF'],
   'config',
   'notification', 'config'
);

$notificationwebsocket->display(array('id' => 1));

Html::footer();
