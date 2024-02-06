<?php

require __DIR__ . '/../vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;
use m039\PollingPlus;

$bot = new Nutgram(getenv('TOKEN'));
$count = 0;
$runnginMode = new PollingPlus();
$timerMessageId = null;
$timerChatId = null;

$bot->setRunningMode($runnginMode);

$runnginMode->onUpdate(function (Nutgram $bot, int $delta) {
    global $timerMessageId;
    global $timerChatId;
    
    if ($timerMessageId && $timerChatId) {
        $bot->editMessageText("Timer: " . date("F j, Y, g:i:s a") . "\n", $timerChatId, $timerMessageId);
    }
});

$bot->onCommand('start', function(Nutgram $bot) {
    global $count;
    $bot->sendMessage('Ciao! ' . $count);
    $count++;
});

$bot->onCommand("start_timer", function(Nutgram $bot) {
    global $timerMessageId;
    global $timerChatId;

    $message = $bot->sendMessage("Timer: ");
    $timerMessageId = $message->message_id;
    $timerChatId = $message->chat->id;
});

$bot->onCommand("stop_timer", function (Nutgram $bot) {
    global $timerMessageId;
    global $timerChatId;

    $timerChatId = $timerMessageId = null;
});

$bot->run();