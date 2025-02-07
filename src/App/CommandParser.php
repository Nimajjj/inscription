<?php

namespace App\App;

class CommandParser
{
    public static function parse(array $argv): array
    {
        if (count($argv) < 2) {
            return ['error' => 'Commande manquante.'];
        }

        $command = $argv[1];
        $input = json_decode(file_get_contents('php://stdin'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON invalide.'];
        }

        return ['command' => $command, 'input' => $input];
    }
}