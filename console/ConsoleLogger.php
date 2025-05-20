<?php

namespace console;

use common\LoggerInterface;

class ConsoleLogger implements LoggerInterface
{
    public function log($level, $message, array $context = [])
    {
        $output = "[" . strtoupper($level) . "] " . $this->interpolate($message, $context);
        echo $output . PHP_EOL;
    }

    private function interpolate($message, array $context = [])
    {
        foreach ($context as $key => $val) {
            $message = str_replace("{{$key}}", $val, $message);
        }
        return $message;
    }

    public function emergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }
}