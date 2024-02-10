<?php

namespace m039;

require __DIR__ . '/../vendor/autoload.php';

use m039\Utils\ServerChecker;
use m039\DB\DBManager;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

$db = DBManager::createInstance();
$entries = $db->getAllEntries();

if (count($entries) == 0) {
    exit("No users.\n");
}

$subscribed = [];
foreach ($entries as $entry) {
    if ($entry->subscribed) {
        $subscribed[] = $entry;
    }
}

if (count($subscribed) == 0) {
    exit("No subscribed users.\n");
}

$checker = new ServerChecker();
$server_is_online = $checker->check();

$bot = new Nutgram(getenv('TOKEN'), new Configuration(enableHttp2: false));

foreach ($subscribed as $subscriber) {
    if ($subscriber->server_is_online == $server_is_online) {
        continue;
    }

    try {
        if ($server_is_online) {
            $bot->sendMessage("Сервер в сети", $subscriber->chat_id);
        } else {
            $bot->sendMessage("Не удается достучаться до сервера", $subscriber->chat_id);
        }
    } catch (TelegramException $e) {
        if ($e->getCode() == 403) {
            $subscriber->subscribed = false;
        }
    }

    $subscriber->server_is_online = $server_is_online;
    $db->updateEntry($subscriber);
}