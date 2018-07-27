<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\TripalTestCase;

class PublishingRecordsTest extends TripalTestCase
{
    use DBTransaction;

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

    public function testThatRecordsCanGetPublishedWhenIdsAreProvided()
    {
        $cv = db_query('SELECT cvterm_id FROM chado.cvterm WHERE name=:name LIMIT 1',
            [':name' => 'analysis'])->fetchObject();
        $analyses = factory('chado.analysis', 10)->create([
            'type_id' => $cv->cvterm_id,
        ]);

        $ids = array_map(function ($analysis) {
            return $analysis->feature_id;
        }, $analyses);

        $this->publish('analysis', $ids);

        $analysis = $analyses[0];
        $bundle = db_query("SELECT name FROM chado_bundle 
                            INNER JOIN tripal_bundle ON tripal_bundle.id = chado_bundle.bundle_id 
                            WHERE data_table='analysis' AND type_id=:type
                            LIMIT 1", [':type' => $analysis->type_id])->fetchObject();

        $records = db_query("SELECT record_id FROM chado_{$bundle->name} WHERE record_id IN (:ids)", [
            ':ids' => $ids,
        ])->fetchAll();

        $this->assertNotEmpty($records, 'Could not find published feature');
    }
}
