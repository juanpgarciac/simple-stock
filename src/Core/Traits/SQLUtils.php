<?php

namespace Core\Traits;

trait SQLUtils
{

    /**
     * Where query receives and array (from QueryBuilder Trait and implode it) or a direct raw SQL Query
     * @param array|string $conditions
     * 
     * @return string
     */
    private static function whereQuery(array|string $conditions): string
    {
        if(is_array($conditions)){
            if(count($conditions) > 0)
                return ' WHERE '.implode(' ', $conditions);
            return '';
        }            
        return $conditions;
    }

    /**
     * @param array<string> $fields
     * @param array<string> $conditions
     * @param string $table
     *
     * @return string
     */
    public static function selectQuery(array $fields, array|string $conditions, string $table): string
    {
        $whereString = self::whereQuery($conditions);

        $selectString = implode(', ', $fields);

        $query = "SELECT ".$selectString." FROM ".$table.$whereString.';';

        return $query;
    }

    /**
     * @param array<mixed> $record
     * @param string $table
     *
     * @return string
     */
    public static function insertQuery(array $record, string $table, string $suffix = ''): string
    {
        $fields = implode(', ', array_keys($record));
        $values = implode(', ',array_map(fn($item)=>(  is_null($item) ? 'NULL' : "'$item'"),array_values($record)));
        $query = "INSERT INTO $table ($fields) VALUES ($values) $suffix;";
        return $query;
    }

    /**
     * @param array<string,string|int|float|null> $record
     * @param array<string> $conditions
     * @param string $table
     *
     * @return string
     */
    public static function updateQuery(array $record, array|string $conditions, string $table): string
    {
        $whereString = self::whereQuery($conditions);

        $set = [];
        foreach ($record as $field => $value) {
            $set[] = "$field = " . (is_null($value) ? 'NULL' : "'$value'");
        }

        $setString = implode(", ", $set);

        $query = "UPDATE $table SET ".$setString.$whereString.';';

        return $query;
    }

    /**
     * @param array<string> $conditions
     * @param string $table
     *
     * @return string
     */
    public static function deleteQuery(array $conditions, string $table): string
    {
        $whereString = self::whereQuery($conditions);

        $query = "DELETE FROM ".$table.$whereString;

        return $query;
    }
}
