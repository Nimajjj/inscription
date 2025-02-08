<?php

namespace App\Application\CommandParser;

use App\Application\Enum\ApplicationCommand;

final class CommandParser
{
    private array $commands;
    public function __construct()
    {

    }

    public function addCommand(string $commandName, ApplicationCommand $command): CommandParser
    {
        $this->commands[$commandName] = $command;
        return $this;
    }

    public function parseCommand(array $argv): ApplicationCommand
    {
        if (!isset($argv[1]))
        {
            throw new \InvalidArgumentException("Missing command argument.");
        }

        if (array_key_exists($argv[1], $this->commands))
        {
            return $this->commands[$argv[1]];
        }

        return ApplicationCommand::UNKNOWN;
    }

    public function parseFilename(array $argv): string
    {
        // Check for the existence of the second command argument.
        if (!isset($argv[2]))
        {
            throw new \InvalidArgumentException("Missing filename argument.");
        }

        $filename = $argv[2];

        // Convert to absolute path if the given filename is not already absolute.
        if (!$this->isAbsolutePath($filename))
        {
            // Prepend the current working directory
            $filename = getcwd() . DIRECTORY_SEPARATOR . $filename;
        }

        // Optionally, you can try to resolve the canonicalized absolute pathname.
        // Note: realpath returns false if the file does not exist.
        $realPath = realpath($filename);
        return $realPath !== false ? $realPath : $filename;
    }


    /**
     * Checks if a given path is absolute.
     *
     * @param string $path The path to check.
     * @return bool True if the path is absolute, false otherwise.
     */
    private function isAbsolutePath(string $path): bool {
        // For Unix-like systems, an absolute path starts with a '/'
        if (DIRECTORY_SEPARATOR === '/')
        {
            return strpos($path, '/') === 0;
        }
        // For Windows systems, an absolute path starts with a drive letter and colon (e.g., C:\)
        return preg_match('/^[A-Z]:[\/\\\\]/i', $path) === 1;
    }
}
