<?php

namespace Framework\Console;

class Input
{
    public static function capture(): CommandInput
    {
        $commands = app(CommandCollection::class); 
        global $argv;

        $args = $argv;
        array_shift($args); // php
        array_shift($args); // script name

        $signature = $args[0] ?? 'help';
        array_shift($args);

        $arguments = [];
        $options = [];

        if ($commands->has($signature)) {
            $commandObj = $commands->get($signature);
            $parser = new SignatureParser();
            $parsed = $parser->parseInput($commandObj->getSignature(), $args);

            $arguments = $parsed['arguments'];
            $options = $parsed['options'];
        }

        return new CommandInput($signature, $arguments, $options);
    }
}
