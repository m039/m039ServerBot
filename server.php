<?php

require __DIR__ . '/vendor/autoload.php';

use SergiX44\Nutgram\Nutgram;

$bot = new Nutgram(getenv('TOKEN'));
$count = 0;

$bot->onCommand('start', function(Nutgram $bot) {
    global $count;
    $bot->sendMessage('Ciao! ' . $count);
    $count++;
});

$bot->onText('My name is {name}', function(Nutgram $bot, string $name) {
    $bot->sendMessage("Hi $name");
});

$bot->run();
