<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

class FeatureTableSeeder extends Seeder
{
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
        $gname = uniqid();
        chado_query("INSERT INTO {organism} (genus, species) VALUES (:genus, :species)", [
            ':genus' =>$gname,
            ':species' => 'Some Species',
        ]);
        $this->organism_id = chado_query('SELECT organism_id FROM {organism} ORDER BY organism_id ASC LIMIT 1')->fetchField();

        $cv_id = chado_query('SELECT cvterm_id from {cvterm} WHERE LOWER(name)=:name',
            [':name' => 'mrna'])->fetchField();

        if (! $cv_id) {
            throw new \Exception('CV Term Not Found');
        }

        $fname = uniqid();
        chado_query('INSERT INTO {feature} (name, uniquename, type_id, organism_id) VALUES(:name, :uniquename, :cv_id, :oid)',
            [
                ':name' => $fname,
                ':uniquename' => $fname,
                ':cv_id' => $cv_id,
                ':oid' => $this->organism_id,
            ]);

        $this->features = chado_query('SELECT * FROM {feature} WHERE name = :name AND uniquename=:uniquename AND type_id=:cv_id',
            [
                ':name' => $fname,
                ':uniquename' => $fname,
                ':cv_id' => $cv_id,
            ])->fetchAll();

        $this->publisher = $this->publish('feature');
    }
}
