<?php

namespace StatonLab\TripalTestSuite\Database;

class Factory
{
    /**
     * An array of all defined factories.
     *
     * @var array[Factory]
     */
    public static $factories = [];

    /**
     * Table to use.
     *
     * @var string
     */
    protected $table;

    /**
     * Number of times to run the create function.
     *
     * @var int
     */
    protected $times;

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primary_key;

    /**
     * Faker locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * Create a new Factory instance.
     *
     * @param string $table Table name preceded by the schema if not public.
     *                       Example: "node", "public.node" or "chado.feature"
     * @param int $times The number of times to repeat the operation.
     * @return void
     * @throws \Exception
     */
    public function __construct($table, $times = 1)
    {
        if (! isset(static::$factories[$table])) {
            throw new \Exception("Attempt to use an undefined factory of type $table.");
        }

        $this->table = $table;
        $this->times = $times;
        $this->primary_key = $this->extractPrimaryKey();
        $this->locale = getenv('FAKER_LOCALE');
        if ($this->locale === false) {
            $this->locale = 'en_US';
        }
    }

    /**
     * Define and register a new factory.
     *
     * @param string $table Table name preceded by the schema if not public.
     *                       Example: "node", "public.node" or "chado.feature"
     * @param callable $callback
     * @param string $primary_key The primary key in the table. If provided,
     *                            will be used to retrieve the inserted record.
     *                            For Chado tables, chado_get_schema will be used
     *                            to retrieve the primary key if non provided.
     */
    public static function define($table, callable $callback, $primary_key = null)
    {
        static::$factories[$table] = [
            'primary_key' => $primary_key,
            'callback' => $callback,
        ];
    }

    /**
     * Create the data.
     *
     * @param array $overrides
     * @throws \Exception
     * @return \stdClass|array If only one item was inserted, an object will be returned.
     *                         Otherwise, an array of objects will be returned.
     */
    public function create(array $overrides = [])
    {
        $factory = static::$factories[$this->table]['callback'];
        $data = [];

        for ($i = 0; $i < $this->times; $i++) {
            $values = $this->override($factory(\Faker\Factory::create($this->locale)), $overrides);

            $data[] = $this->insert($values);
        }

        if (count($data) === 1) {
            return $data[0];
        }

        return $data;
    }

    /**
     * Override array.
     *
     * @param array $values
     * @param array $overrides
     * @return array
     */
    protected function override(array $values, array $overrides)
    {
        if (count($overrides) === 0) {
            return $values;
        }

        foreach ($overrides as $key => $value) {
            $values[$key] = $overrides[$key];
        }

        return $values;
    }

    /**
     * Insert data into the db and obtain the inserted records.
     *
     * @param $values
     * @throws \Exception
     * @return \stdClass
     */
    protected function insert($values)
    {
        $id = db_insert($this->table)->fields($values)->execute();

        $query = db_select($this->table, 'T');
        $query->fields('T');
        $query->condition($this->primary_key, $id);

        return $query->execute()->fetchObject();
    }

    /**
     * Get the primary key of a table.
     *
     * @throws \Exception
     * @return string
     */
    protected function extractPrimaryKey()
    {
        if (static::$factories[$this->table]['primary_key'] !== null) {
            return static::$factories[$this->table]['primary_key'];
        }

        if (preg_match('/^chado\./', $this->table)) {
            $schema = chado_get_schema(str_replace('chado.', '', $this->table));
            if ($schema) {
                if (isset($schema['primary key'])) {
                    return $schema['primary key'][0];
                } elseif (isset($schema['primary keys'])) {
                    return $schema['primary keys'][0];
                }
            }
        }

        throw new \Exception("Unable to determine primary key for $this->table factory. Please provide the primary key such as `nid` as a third argument in Factory::define().");
    }

    /**
     * Get the primary key.
     *
     * @return string
     */
    public function primaryKey()
    {
        return $this->primary_key;
    }
}
