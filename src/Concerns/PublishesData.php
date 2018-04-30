<?php
namespace StatonLab\TripalTestSuite\Concerns;

use StatonLab\TripalTestSuite\Database\PublishRecords;

trait PublishesData
{
    /**
     * Publish records from chado to entities.
     *
     * @param string data_table Chado table name such as feature.
     * @param array $ids CURRENTLY UNSUPPORTED.
     * @param string $primary_key The primary key name such as feature_id. If not
     *                             provided, the key is obtained using the tripal API.
     * @throws \Exception
     * @return \StatonLab\TripalTestSuite\Database\PublishRecords
     */
    public function publish($data_table, array $ids = [], $primary_key = '')
    {
        $publisher = new PublishRecords($data_table, $ids, $primary_key);
        $publisher->publish();

        return $publisher;
    }
}
