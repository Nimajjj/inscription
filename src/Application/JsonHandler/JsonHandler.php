<?php

namespace App\Application\JsonHandler;
final class JsonHandler
{

    /**
     * Loads a JSON file and returns its contents as an associative array.
     *
     * @param string $filename The path to the JSON file.
     * @return array The decoded JSON data.
     * @throws \RuntimeException If the file does not exist or JSON decoding fails.
     */
    public static function loadJsonFile(string $filename): array
    {
        if (!file_exists($filename))
        {
            throw new \RuntimeException("File does not exist: {$filename}");
        }

        $jsonContent = file_get_contents($filename);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new \RuntimeException("Error decoding JSON: " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Verifies that the provided data contains all required keys.
     *
     * @param array $data The associative array to verify.
     * @param array $requiredKeys An array of keys that must be present.
     * @return bool Returns true if all required keys exist.
     * @throws \InvalidArgumentException If any required key is missing.
     */
    public static function verifyKeys(array $data, array $requiredKeys): bool
    {
        foreach ($requiredKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                return false;
            }
        }
        return true;
    }
}