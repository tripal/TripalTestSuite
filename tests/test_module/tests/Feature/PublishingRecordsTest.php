<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\TripalTestCase;

class PublishingRecordsTest extends TripalTestCase
{
    use DBTransaction;

    /**
     * @throws \Exception
     */
    public function testThatRecordsCanGetPublished()
    {
        $cv = db_query('SELECT cvterm_id FROM chado.cvterm WHERE name=:name LIMIT 1',
            [':name' => 'mRNA'])->fetchObject();
        $feature = factory('chado.feature')->create([
            'type_id' => $cv->cvterm_id,
        ]);

        $this->publish('feature');

        $bundle = db_query("SELECT name FROM chado_bundle 
                            INNER JOIN tripal_bundle ON tripal_bundle.id = chado_bundle.bundle_id 
                            WHERE data_table='feature' AND type_id=:type
                            LIMIT 1", [':type' => $feature->type_id])->fetchObject();

        $records = db_query("SELECT record_id FROM chado_{$bundle->name} WHERE record_id = :id", [
            ':id' => $feature->feature_id,
        ])->fetchAll();

        $this->assertNotEmpty($records, 'Could not find published feature');
    }

    /**
     * @throws \Exception
     */
    public function testThatRecordsCanGetPublishedWhenIdsAreProvided()
    {
        $num_to_publish = 10;
        $cv = db_query('SELECT cvterm_id FROM chado.cvterm WHERE name=:name LIMIT 1',
            [':name' => 'mRNA'])->fetchObject();

        $features = factory('chado.feature', $num_to_publish)->create([
            'type_id' => $cv->cvterm_id,
        ]);

        $ids = array_map(function ($f) {
            return $f->feature_id;
        }, $features);

        $count = (int)db_query('SELECT COUNT(*) FROM tripal_entity')->fetchField();

        $this->publish('feature', $ids);

        $count_after_addition = (int)db_query('SELECT COUNT(*) FROM tripal_entity')->fetchField();
        $this->assertNotEquals($count, $count_after_addition);

        $bundle = db_query("SELECT name FROM chado_bundle 
                            INNER JOIN tripal_bundle ON tripal_bundle.id = chado_bundle.bundle_id 
                            WHERE data_table='feature' AND type_id=:type_id
                            LIMIT 1", [':type_id' => $cv->cvterm_id])->fetchObject();

        $records = db_query("SELECT record_id FROM chado_{$bundle->name} WHERE record_id IN (:ids)", [
            ':ids' => $ids,
        ])->fetchAll();

        $this->assertNotEmpty($records, 'Could not find published feature');
        $this->assertEquals($num_to_publish, count($records));
    }
}
