<?php

namespace App\Core;

use PDOStatement;

abstract class DbModel extends Model
{
    public ?int $id = null;

    abstract public static function tableName(): string;
    abstract public function attributes(): array;
    abstract public static function primaryKey(): string;

    public function save(): bool
    {
    $primaryKey = static::primaryKey();
    if (!empty($this->{$primaryKey})) {
        $fields = [];
        foreach ($this->attributes() as $attribute) {
            $fields[$attribute] = $this->{$attribute};
        }
        return $this->update($fields);
    }

        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $statement = self::prepare(
            "INSERT INTO $tableName (" . implode(',', $attributes) . ") 
            VALUES (" . implode(',', $params) . ")"
        );

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        $this->{static::primaryKey()} = (int)App::$app->db->pdo->lastInsertId();
        return true;
    }

    public static function findOne(array|int $where): ?static
    {
        $tableName = static::tableName();

        if (is_int($where)) {
            $where = [static::primaryKey() => $where];
        }

        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql LIMIT 1");

        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        
        // DEBUG: Check what the database actually returned
        // error_log('Database result: ' . print_r($data, true));
        // error_log('Available properties: ' . print_r(get_class_vars(static::class), true));

        if (!$data) {
            return null;
        }

        $instance = new static();
        foreach ($data as $key => $value) {
            // error_log("Setting property: '$key' => '$value'");
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
                // error_log("Property '$key' set successfully");
            } else {
                // error_log("Property '$key' does not exist in model");
            }
        }
        
        return $instance;
    }

    public function update(array $fields): bool
    {
        $tableName = static::tableName();
        $primaryKey = static::primaryKey();
        $setClauses = [];

        foreach ($fields as $field => $value) {
            if (!in_array($field, $this->attributes(), true)) {
                continue;
            }
            $setClauses[] = "$field = :$field";
            $this->{$field} = $value;
        }

        if (empty($setClauses)) {
            return true;
        }

        $sql = "UPDATE $tableName SET " . implode(', ', $setClauses) . " WHERE $primaryKey = :pk";
        $stmt = self::prepare($sql);

        foreach ($fields as $field => $value) {
            if (in_array($field, $this->attributes(), true)) {
                $stmt->bindValue(":$field", $value);
            }
        }
        $stmt->bindValue(':pk', $this->{$primaryKey});

        return $stmt->execute();
    }

    public function delete(): bool
    {
        $tableName = static::tableName();
        $primaryKey = static::primaryKey();

        $stmt = self::prepare("DELETE FROM $tableName WHERE $primaryKey = :pk");
        $stmt->bindValue(':pk', $this->{$primaryKey});
        return $stmt->execute();
    }

    public static function prepare(string $sql): PDOStatement
    {
        return App::$app->db->pdo->prepare($sql);
    }
}
