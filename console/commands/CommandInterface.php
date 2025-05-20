<?php

namespace console\commands;
interface CommandInterface
{

    /**
     * Run command with console args
     */
    public function runCommand(array $args): void;
}