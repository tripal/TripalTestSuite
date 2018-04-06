<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

class FeatureTableSeeder extends Seeder
{
    /**
     * Whether to run the seeder automatically before
     * starting our tests.
     *
     * @var bool
     */
    public static $auto_run = true;

    /**
     * The publisher.
     *
     * @var \StatonLab\TripalTestSuite\Database\PublishRecords
     */
    protected $publisher;

    /**
     * The features to delete.
     *
     * @var array
     */
    protected $features = [];

    /**
     * The inserted organism.
     *
     * @var int
     */
    protected $organism_id;

    /**
     * Seeds the database with features and publishes them.
     *
     * @throws \Exception
     */
    public function up()
    {
        chado_query("INSERT INTO {organism} (genus, species) VALUES (:genus, :species)", [
            ':genus' => 'Some Genus',
            ':species' => 'Some Species',
        ]);
        $this->organism_id = chado_query('SELECT organism_id FROM {organism} ORDER BY organism_id ASC LIMIT 1')->fetchField();

        $cv_id = chado_query('SELECT cvterm_id from {cvterm} WHERE LOWER(name)=:name',
            [':name' => 'mrna'])->fetchField();

        if (! $cv_id) {
            throw new \Exception('CV Term Not Found');
        }

        chado_query('INSERT INTO {feature} (name, uniquename, type_id, organism_id) VALUES(:name, :uniquename, :cv_id, :oid)',
            [
                ':name' => 'tripal feature test',
                ':uniquename' => 'tripal feature unique',
                ':cv_id' => $cv_id,
                ':oid' => $this->organism_id,
            ]);

        $this->features = chado_query('SELECT * FROM {feature} WHERE name = :name AND uniquename=:uniquename AND type_id=:cv_id',
            [
                ':name' => 'tripal feature test',
                ':uniquename' => 'tripal feature unique',
                ':cv_id' => $cv_id,
            ])->fetchAll();

        $this->publisher = $this->publish('feature');
    }

    /**
     * Cleans up the database from the created entities.
     *
     * @throws \Exception
     */
    public function down()
    {
        if ($this->publisher) {
            $this->publisher->delete();
        }

        $feature_ids = [];
        foreach ($this->features as $feature) {
            $feature_ids = $feature->feature_id;
        }

        if (! empty($feature_ids)) {
            chado_query('DELETE FROM {feature} WHERE feature_id IN (:ids)', [':ids' => $feature_ids]);
        }

        if ($this->organism_id) {
            chado_query('DELETE FROM {organism} WHERE organism_id = :oid', [':oid' => $this->organism_id]);
        }
    }
}
