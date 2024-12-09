<?php

namespace App\Logging;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class TelegramLogger
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = config('logging.channels.telegram.bot_token');
        $this->chatId = config('logging.channels.telegram.chat_id');
    }

    public function __invoke(array $config)
    {
        $logger = new Logger('telegram');
        $logger->pushHandler(new class($this->botToken, $this->chatId) extends AbstractProcessingHandler {
            private $botToken;
            private $chatId;
            private $client;

            public function __construct($botToken, $chatId, $level = Logger::DEBUG, bool $bubble = true)
            {
                parent::__construct($level, $bubble);

                $this->botToken = $botToken;
                $this->chatId = $chatId;
                $this->client = new Client();
            }

            protected function write(LogRecord $record): void
            {
                $message = "*Laravel Log:* " . PHP_EOL;
                $message .= "*Level:* " . $record->level->getName() . PHP_EOL; // Log level name
                $message .= "*Message:* " . $record->message . PHP_EOL;

                if (!empty($record->context)) {
                    $message .= "*Context:* " . json_encode($record->context, JSON_PRETTY_PRINT) . PHP_EOL;
                }

                $this->sendMessage($message);
            }

            

            private function escapeMarkdownV2(string $text): string
            {
                $specialCharacters = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!', '\\'];
                foreach ($specialCharacters as $char) {
                    $text = str_replace($char, '\\' . $char, $text);
                }
                return $text;
            }

            private function sendMessage(string $message)
            {
                $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
                // Escape the message for MarkdownV2
                $escapedMessage = $this->escapeMarkdownV2($message);

                $this->client->post($url, [
                    'form_params' => [
                        'chat_id' => $this->chatId,
                        'text' => $escapedMessage,
                        'parse_mode' => 'Markdown',
                    ],
                ]);
            }
        });

        return $logger;
    }
}
