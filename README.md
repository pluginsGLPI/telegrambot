# Telegram Bot for GLPI

![GLPI Banner](https://user-images.githubusercontent.com/29282308/31666160-8ad74b1a-b34b-11e7-839b-043255af4f58.png)

[![License GPL 3.0](https://img.shields.io/badge/License-GPL%203.0-blue.svg)](./license)
[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](http://www.repostatus.org/badges/latest/active.svg)](http://www.repostatus.org/#active)
[![Telegram Group](https://img.shields.io/badge/Telegram-Group-blue.svg)](https://telegram.me/tgbotglpi)
[![Github All Releases](https://img.shields.io/github/downloads/pluginsGLPI/telegrambot/total.svg)](http://plugins.glpi-project.org/#/plugin/telegrambot)
[![Follow Twitter](https://img.shields.io/badge/Twitter-GLPI%20Project-26A2FA.svg)](https://twitter.com/GLPI_PROJECT)
[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg)](https://conventionalcommits.org)

## Table of Contents

* [Synopsis](#synopsis)
* [Build Status](#build-status)
* [Updates Roadmap for version 3.0](#updates-roadmap-for-version-3)
* [New TelegramBot Commands](#new-telegramBot-commands)
* [Installation](#installation)
* [Installation and Configuration step by step](#installation-and-configuration-step-by-step)
* [Documentation](#documentation)
* [Versioning](#versioning)
* [Contact](#contact)
* [Professional Services](#professional-services)
* [Contribute](#contribute)
* [Copying](#copying)

## Synopsis

This Bot for GLPI allows you to get notifications on Telegram when a ticket is created on GLPI, keeping you up to date with what's happening in your IT infrastructure..

## Build Status

|**Release channel**|Beta Channel|
|:---:|:---:|
|[![Travis CI build](https://api.travis-ci.org/pluginsGLPI/telegrambot.svg?branch=master)](https://travis-ci.org/pluginsGLPI/telegrambot/)|[![Travis CI build](https://api.travis-ci.org/pluginsGLPI/telegrambot.svg?branch=develop)](https://travis-ci.org/pluginsGLPI/telegrambot/)|

## Updates Roadmap for version 3.0

 - [x] New Ticket.
 - [x] Search ticket.
 - [x] New followup.
 - [x] Support group notification.
 - [x] Login.

## New TelegramBot Commands:
**Commands List:**
```
/help - Show bot commands help.
/login - Login via Telegrambot for GLPI. The login command will only work on private chat. 
Group chat Login Command is forbidden.
/newfollowup - Add a new followup to a ticket on GLPI via TelegramBot.
/newticket - Add a new ticket on GLPI via TelegramBot.
/searchticket - Search for a ticket on GLPI via TelegramBot.

For exact command help type: /help <command>
Or just type the command name: /<command>
```

## Installation

Click on the image to view the video preview.

[![Everything Is AWESOME](http://img.youtube.com/vi/TKqIpIaAIAE/0.jpg)](https://youtu.be/TKqIpIaAIAE)

## Installation and Configuration step by step

Here is a step by step of the video above:

- Create your Telegram BOT on Telegram App:
  - Search for BotFather on Telegram.
  - Start a new chat with it.
  - Type /newbot and send.
  - BotFather will reply:
    > Alright, a new bot. How are we going to call it? Please choose a name for your bot.
  - Type the name of your bot and send. Ex.: GLPI Telegrambot Demo.
  - BotFather will reply again:
    > Good. Now let's choose a username for your bot. It must end in `bot`. Like this, for example: TetrisBot or tetris_bot.
  - Type the username of your bot and send. Ex.: glpi_telegrambot_demo_bot.
  - Finally BotFather replies with:
    > Done! Congratulations on your new bot. You will find it at t.me/glpi_telegrambot_demo_bot. 
    > You can now add a description, about section and profile picture for your bot, see /help for a 
    > list of commands. By the way, when you've finished creating your cool bot, ping our Bot Support 
    > if you want a better username for it. Just make sure the bot is fully operational before you do this.
    > 
    > Use this token to access the HTTP API:
    > 872970295:AAFsALgD0WH9BOp-ZU282JVN_lW3l56GZEA
    > Keep your token secure and store it safely, it can be used by anyone to control your bot.
    > 
    > For a description of the Bot API, see this page: https://core.telegram.org/bots/api
  - Save the token to access and the bot username to use it late.

- Config your bot to group messages:
  - Now type /setprivacy to BotFather. This command can be executed any time.
  - BotFather replies with:
    > Choose a bot to change group messages settings.
  - Type (or select) @<your_bot_username> 
    (change to the username you set at step 5 above, but start it with @) Ex.: glpi_telegrambot_demo_bot.
  - BotFather replies with:
    > 'Enable' - your bot will only receive messages that either start with the '/' symbol or mention the bot by username.
    > 'Disable' - your bot will receive all messages that people send to groups.
    > Current status is: ENABLED
  - Type (or select) Disable to let your bot receive all messages sent to a group. This step is up to you actually.
  - BotFather replies with:
    > Success! The new status is: DISABLED. /help

- Download:
  - Go to the [download page](https://plugins.glpi-project.org/#/plugin/telegrambot) and get the latest version (3.0) of our plugin.
  - Unpack the file and put the folder /telegrambot under: /<your_server_path>/glpi/plugins
  - The final path must be: /<your_server_path>/glpi/plugins/telegrambot

- Installation:
  - On your GLPI application open the menu: Setup -> Plugins.
  - You will see the catalog of plugins.
  - Our TelegramBot for GLPI will be there on the list.
  - On the column Action, click on the Install icon.
  - After the installation click on the Enable icon.
  - If your plugin installation didn't work, we have to make some workarounds:
  **Unfortunately we have to make some changes on GLPI 9.4.x core to Telegrambot work properly:**
  **Attention:** If all your Plugins folder name starts with lowercase letter, this modification has no impact at all on your GLPI Aplication.
  - Go to file: **/glpi/inc/plugin.class.php**

  **Plugin::load() - line 161 (GLPI 9.4.2)**
  ```php
    static function load($name, $withhook = false) {
      global $LOADED_PLUGINS;
      $name = strtolower($name); //-> Add this line
      ...
    } 
  ```

  **Plugin::loadLang() - line 193 (GLPI 9.4.2)**
  ```php
    static function loadLang($name, $forcelang = '', $coretrytoload = '') {
        // $LANG needed : used when include lang file
        global $CFG_GLPI, $LANG, $TRANSLATE;
        $name = strtolower($name); //-> Add this line
        ...
    } 
  ```

  **Plugin::doHook() - line 1105 and 1124 (GLPI 9.4.2)**
  ```php
    static function doHook ($name, $param = null) {
        ...
        if (isset($PLUGIN_HOOKS[$name]) && is_array($PLUGIN_HOOKS[$name])) {
                foreach ($PLUGIN_HOOKS[$name] as $plug => $tab) {
                   $plug = strtolower($plug); //-> Add this line
                   ...
        } else { // Standard hook call
            if (isset($PLUGIN_HOOKS[$name]) && is_array($PLUGIN_HOOKS[$name])) {
               foreach ($PLUGIN_HOOKS[$name] as $plug => $function) {
                  $plug = strtolower($plug); //-> Add this line
                  ...
    }
  ``` 

  **Plugin::doHookFunction() - line 1158 (GLPI 9.4.2)**
  ```php
    static function doHookFunction($name, $parm = null) {
      global $PLUGIN_HOOKS;

      $ret = $parm;
      if (isset($PLUGIN_HOOKS[$name]) && is_array($PLUGIN_HOOKS[$name])) {
         foreach ($PLUGIN_HOOKS[$name] as $plug => $function) {
            $plug = strtolower($plug); //-> Add this line
            ...
    } 
  ```

- Setup the Notifications:
  - On your GLPI application open the menu: Setup -> Notifications.
  - A 'Notifications configuration' table will be displayed.
  - You will see a 'Enable followup' option, change to Yes.
  - You will see a 'Enable followups via Telegram' option, change to Yes.
  - Click on Save button.
  - A new table with the title 'Notifications' will appear.
  - Click on 'Telegram followups configuration'.
  - On 'Bot token' field PASTE the token to access the HTTP API given by BotFather. Ex.: 872970295:AAFsALgD0WH9BOp-ZU282JVN_lW3l56GZEA
  - On 'Bot username' field PASTE the username of your bot on Telegram. Ex.: glpi_telegrambot_demo_bot.
  - Click on 'Save' button.

- Setup the Automatic actions:
  - Open the menu: Setup -> Automatic actions.
  - A list with the 'Automatic actions' will be displayed.
  - Search for 'messagelistener' and click on it.
  - A log page will be displayed. Click the menu button 'Automatic actions' on the left.

- Configure the GLPI user:
  - On your GLPI application open the menu: Administration -> Users.
  - Select the user. Ex.: glpi.
  - On the bottom of the form displayed, you will see a 'Telegram username' field.
  - On this field put the Telegram username and save.

- Setup the notification:
  - On your GLPI application open the menu: Setup -> Notifications.
  - On the Notification table, click on 'Notification templates'.
  - Click on 'Add' icon and the 'New item - Notification template' form will be displayed.
  - Choose a name for your template and the 'Type' choose 'ticket'.
  - Click on 'Add' button.
  - A 'New item - Template translation' form will be displayed.
  - On 'Subject' field put the message that will be send to your Bot.
  - On 'Email text body' field put the text:
  ```
  New Ticket
  ID: ##ticket.id##
  Title: ##ticket.title##
  ```
  - Click on 'Add' button.
  - Go back to Setup -> Notifications -> Notifications.
  - Click on 'Add' button.
  - A 'New item - Notification' form will be displayed.
  - Choose a name to your item notification.
  - Change 'Active' field to 'Yes'.
  - Change 'Type' field to 'Ticket'.
  - Change 'Event' field to 'New ticket'.
  - Click on 'Add' button.
  - The notification page will be displayed. Click the menu button 'Templates' on the left.
  - Click on 'Add a template' button.
  - Change the 'Mode' field to 'Telegram'.
  - Change the 'Notification template' to the name you choose before to your template.
  - Click on 'Add' button.
  - Go back to Setup -> Notifications -> Notifications.
  - Search for the Notification you just added and click on it.
  - The notification page will be displayed. Click the menu button 'Recipients' on the left.
  - On the blank field, choose the options 'Administrator' and 'Requester'
  - Click on 'Update' button.

- Test your Bot:
  - Go to your Telegram App and start a chat with your Bot. Ex.: glpi_telegrambot_demo_bot.
  - Send a message: Ex.: Hello
  - Go back to 'Automatic actions' page on GLPI and click on the button 'Execute'
  - Click the menu button 'Logs' on the left.
  - You will see a new entry. Thats our 'listener'.

- Setup the Ticket:
  - On your GLPI application open the menu: Assistance -> Create Ticket.
  - Choose a 'Title' and a 'Description' to your Ticket.
  - Click on 'Add' button.

- Test your Bot result:
  - After you Setup the Ticket, the user will receive a Notification Ticket on Telegram.

## Documentation

We maintain a detailed documentation of the project on the website, see our [How-tos](https://pluginsGLPI.github.io/telegrambot/howtos) and [Development](https://pluginsGLPI.github.io/telegrambot/) sections.

## Versioning

In order to provide transparency on our release cycle and to maintain backward compatibility, this project is maintained under [the Semantic Versioning guidelines](http://semver.org/). We are committed to following and complying with the rules, the best we can.

See [the tags section of our GitHub project](https://github.com/pluginsGLPI/telegrambot/tags) for changelogs for each release version.

## Contact

You can sen us a message through [Telegram](https://telegram.me/tgbotglpi).

## Professional Services

The GLPI Network services are available through our [Partner's Network](http://www.teclib-edition.com/en/partners/). We provide special training, bug fixes with editor subscription, contributions for new features, and more.

Obtain a personalized service experience, associated with benefits and opportunities.

## Contribute

Want to file a bug, contribute some code, or improve documentation? Excellent! Read up on our
guidelines for [contributing](./CONTRIBUTING.md) and then check out one of our issues in the [Issues Dashboard](https://github.com/pluginsGLPI/telegrambot/issues).

## Copying

* **Name**: [GLPI](http://glpi-project.org/) is a registered trademark of [Teclib'](http://www.teclib-edition.com/en/).
* **Code**: you can redistribute it and/or modifyit under the terms of the GNU General Public License ([GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html)).
* **Documentation**: released under Attribution 4.0 International ([CC BY 4.0](https://creativecommons.org/licenses/by/4.0/)).