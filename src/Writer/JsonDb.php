<?php

declare(strict_types=1);

namespace Webinertia\Log\Writer;

use Laminas\Db\Adapter\Adapter;
use Laminas\Log\Writer\AbstractWriter;
use Laminas\Log\Exception;
use Laminas\Log\Formatter\Json;
use Traversable;

use function array_keys;
use function array_map;
use function implode;
use function is_array;
use function is_scalar;
use function iterator_to_array;
use function var_export;

class JsonDb extends AbstractWriter
{
    /**
     * Db adapter instance
     *
     * @var Adapter
     */
    protected $db;

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    protected $columnMap;

    /**
     * Field separator for sub-elements
     *
     * @var string
     */
    protected $separator = '_';

    /**
     * Constructor
     *
     * We used the Adapter instead of Laminas\Db for a performance reason.
     *
     * @param Adapter|array|Traversable $db
     * @param string $tableName
     * @param array $columnMap
     * @param string $separator
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($db, $tableName = null, ?array $columnMap = null, $separator = null)
    {
        if ($db instanceof Traversable) {
            $db = iterator_to_array($db);
        }

        if (is_array($db)) {
            parent::__construct($db);
            $separator = $db['separator'] ?? null;
            $columnMap = $db['column'] ?? null;
            $tableName = $db['table'] ?? null;
            $db        = $db['db'] ?? null;
        }

        if (! $db instanceof Adapter) {
            throw new Exception\InvalidArgumentException('You must pass a valid Laminas\Db\Adapter\Adapter');
        }

        $tableName = (string) $tableName;
        if ('' === $tableName) {
            throw new Exception\InvalidArgumentException(
                'You must specify a table name. Either directly in the constructor, or via options'
            );
        }

        $this->db        = $db;
        $this->tableName = $tableName;
        $this->columnMap = $columnMap;

        if (! empty($separator)) {
            $this->separator = $separator;
        }

        if (! $this->hasFormatter()) {
            $this->setFormatter(new Json());
        }
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->db = null;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        if (null === $this->db) {
            throw new Exception\RuntimeException('Database adapter is null');
        }

        $event = $this->formatter->format($event);
        $dataToInsert = ['id' => null, 'data' => $event];

        $statement = $this->db->query($this->prepareInsert($dataToInsert));
        $statement->execute($dataToInsert);
    }

    /**
     * Prepare the INSERT SQL statement
     *
     * @param  array $fields
     * @return string
     */
    protected function prepareInsert(array $fields)
    {
        $keys = array_keys($fields);
        return 'INSERT INTO ' . $this->db->platform->quoteIdentifier($this->tableName) . ' ('
            . implode(",", array_map([$this->db->platform, 'quoteIdentifier'], $keys)) . ') VALUES ('
            . implode(",", array_map([$this->db->driver, 'formatParameterName'], $keys)) . ')';
    }

    /**
     * Map event into column using the $columnMap array
     *
     * @param  array $event
     * @param  array $columnMap
     * @return array
     */
    protected function mapEventIntoColumn(array $event, ?array $columnMap = null)
    {
        if (empty($event)) {
            return [];
        }

        $data = [];
        foreach ($event as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $subvalue) {
                    if (isset($columnMap[$name][$key])) {
                        if (is_scalar($subvalue)) {
                            $data[$columnMap[$name][$key]] = $subvalue;
                            continue;
                        }

                        $data[$columnMap[$name][$key]] = var_export($subvalue, true);
                    }
                }
            } elseif (isset($columnMap[$name])) {
                $data[$columnMap[$name]] = $value;
            }
        }
        return $data;
    }

    /**
     * Transform event into column for the db table
     *
     * @param  array $event
     * @return array
     */
    protected function eventIntoColumn(array $event)
    {
        if (empty($event)) {
            return [];
        }

        $data = [];
        foreach ($event as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $subvalue) {
                    if (is_scalar($subvalue)) {
                        $data[$name . $this->separator . $key] = $subvalue;
                        continue;
                    }

                    $data[$name . $this->separator . $key] = var_export($subvalue, true);
                }
            } else {
                $data[$name] = $value;
            }
        }
        return $data;
    }
}
