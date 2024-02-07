<?php

require __DIR__ . '/../vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Configuration;
use m039\PollingPlus;

$bot = new Nutgram(getenv('TOKEN'), new Configuration(enableHttp2: false));
$count = 0;
$runnginMode = new PollingPlus();
$timerMessageId = null;
$timerChatId = null;
$remindTimer = null;
$remindChatId = null;

$bot->setRunningMode($runnginMode);

$bot->middleware(function (Nutgram $bot, $next) {
    if ($bot->userId() == 74792267) {
        $next($bot);
    } else {
        $bot->sendMessage("Access is denied.");
    }
});

$runnginMode->onUpdate(function (Nutgram $bot, int $delta) {
    global $timerMessageId;
    global $timerChatId;
    global $remindChatId;
    global $remindTimer;
    
    if ($timerMessageId && $timerChatId) {
        $bot->editMessageText("Timer: " . date("F j, Y, g:i:s a") . "\n", $timerChatId, $timerMessageId);
    }

    if (is_numeric($remindTimer)) {
        $remindTimer -= $delta;

        if ($remindTimer < 0) {
            $bot->sendMessage("You got a reminder.", $remindChatId);
            $remindTimer = $remindChatId = null;
        }
    }
});

$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage('Ciao!');
});

$bot->onCommand("clicker", function (Nutgram $bot) {
    global $count;
    $bot->sendMessage('Click: ' . $count);
    $count++;
});

$bot->onCommand("start_timer", function(Nutgram $bot) {
    global $timerMessageId;
    global $timerChatId;

    $message = $bot->sendMessage("Timer: ");
    $timerMessageId = $message->message_id;
    $timerChatId = $message->chat->id;
});

$bot->onCommand("remind", function (Nutgram $bot) {
    global $remindChatId;
    global $remindTimer;

    $remindTimer = 60;
    $remindChatId = $bot->chatId();
    $bot->sendMessage("The notification is set.");
});

$bot->onCommand("stop_timer", function (Nutgram $bot) {
    global $timerMessageId;
    global $timerChatId;

    $timerChatId = $timerMessageId = null;
});

$bot->run();
