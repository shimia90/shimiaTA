<?php

declare(strict_types = 1);

include __DIR__.'/basics.php';

use React\EventLoop\Factory;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Types\User;
use unreal4u\TelegramAPI\TgLog;

$loop = Factory::create();
$tgLog = new TgLog(BOT_TOKEN, new HttpClientRequestHandler($loop));

$sendMessage = new SendMessage();
switch ($text) {
  case '/start':
      require_once __DIR__.'/types/send-message-with-keyboard-options.php';
    break;
  case '/test':
      require_once __DIR__.'/types/send-message-with-inlinekeyboard.php';
    break;
  case $nutYeuCau[0]: // 📋 Xem Danh Sách Plan
  		
    break;
  case $nutYeuCau[1]: // 💰 Yêu Cầu Rút Coin
  	
    break;
  case $nutYeuCau[2]: // 📤 Yêu Cầu Cuối Tháng
  	
    break;
  default:
    break;
}

$promise = $tgLog->performApiRequest($sendMessage);

$loop->run();