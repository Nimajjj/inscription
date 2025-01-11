<?php
namespace App\Adapter;

use App\Query\Query;
use App\Query\QueryAction;
use PDO;
use PDOException;
use RuntimeException;

final class MySQLAdapter implements IAdapter
{
    private ?PDO $database = null;

    public function executeQuery(Query $query, array &$outResult): bool
    {
        $rawQuery = $query->toRawSql();
        $statement = $this->getDatabase()->prepare($rawQuery);
        assert($statement, "Error while preparing the query: '$rawQuery'");

        $error = $statement->execute();

        if ($query->action === QueryAction::SELECT)
        {
            $outResult = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $error && !empty($outResult);
        }

        $outResult = [];
        return $error;
    }

    private function getDatabase(): PDO
    {
        if ($this->database === null)
        {
            $file = __DIR__ . '/../../credentials.json';
            error_log("Chemin complet tenté pour le fichier credentials.json : " . realpath($file) ?: "Chemin non valide");
            if (!file_exists($file)) {
                throw new RuntimeException("Database credentials file not found: $file realpath($file)");
            }

            $data = file_get_contents($file);
            if ($data === false) {
                throw new RuntimeException("Failed to read the database credentials file: $file");
            }

            $obj = json_decode($data);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException("Invalid JSON format in credentials file: $file");
            }

            $host = $obj->host ?? null;
            $dbname = $obj->dbname ?? null;
            $username = $obj->username ?? null;
            $password = $obj->password ?? null;

            if (!$host || !$dbname || !$username || !$password) {
                throw new RuntimeException("Incomplete database credentials provided in: $file");
            }

            try {
                $this->database = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            } catch (PDOException $e) {
                throw new RuntimeException("Failed to connect to database: " . $e->getMessage());
            }
        }

        assert($this->database, "Failed to connect to the database.");
        return $this->database;
    }
}