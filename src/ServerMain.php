<?php

require __DIR__ . '/../vendor/autoload.php';

use m039\DB\DBManager;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Configuration;

$bot = new Nutgram(getenv('TOKEN'), new Configuration(enableHttp2: false));
$db = DBManager::createInstance();
$count = 0;

$bot->middleware(function (Nutgram $bot, $next) {
    if ($bot->userId() == 74792267) {
        $next($bot);
    } else {
        $bot->sendMessage("Access is denied.");
    }
});

$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage('Используйте команды /subscribe или /unsubscribe, чтобы подписаться на уведомления о том доступен ли сейчас сайт m039.site. Еще есть команда /clicker для проверки работает ли бот.');
});

$bot->onCommand("clicker", function (Nutgram $bot) {
    global $count;
    $bot->sendMessage('Клик: ' . $count);
    $count++;
});

$bot->onCommand("subscribe", function (Nutgram $bot) {
    global $db;

    if ($bot->chatId() && $bot->userId()) {
        $db->register($bot->userId(), $bot->chatId());
        $bot->sendMessage("Вы подписались на уведомления.");
    } else {
        $bot->sendMessage("Ошибка!");
    }
});

$bot->onCommand("unsubscribe", function (Nutgram $bot) {
    global $db;

    if ($bot->chatId() && $bot->userId()) {
        $db->unregister($bot->userId(), $bot->chatId());
        $bot->sendMessage("Вы отписались от уведомлений.");
    } else {
        $bot->sendMessage("Ошибка!");
    }
});

$bot->run();
