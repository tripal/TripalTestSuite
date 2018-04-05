<?php

namespace StatonLab\TripalTestSuite\Database;

class PublishRecords
{
    /**
     * Holds the ids to publish.
     *
     * @var array
     */
    protected $ids;

    /**
     * Data table to publish.
     *
     * @var string
     */
    protected $data_table;

    /**
     * Bundles corresponding to the publishable data.
     *
     * @var array
     */
    protected $bundles;

    /**
     * Published data to delete.
     *
     * @var array
     */
    protected $published = [];

    /**
     * PublishRecords constructor.
     *
     * @param string data_table Chado table name such as feature.
     * @param array $ids CURRENTLY UNSUPPORTED.
     * @param string $primary_key The primary key name such as feature_id. If not
     *                             provided, the key is obtained using the tripal API.
     */
    public function __construct($data_table, array $ids = [], $primary_key = '')
    {
        $this->ids = $ids;
        $this->data_table = $data_table;
        $this->primary_key = ! empty($primary_key) ? $primary_key : chado_get_schema($data_table)['primary key'][0];
    }

    /**
     * Perform the publishing.
     *
     * @throws \Exception
     */
    public function publish()
    {
        // Find the bundle
        $this->bundles = db_query('SELECT name, bundle_id FROM chado_bundle
                             JOIN tripal_bundle ON tripal_bundle.id = chado_bundle.bundle_id
                             WHERE data_table = :data_table', [
            ':data_table' => $this->data_table,
        ])->fetchAll();

        // Prep the filters
        $filters = [];
        if (! empty($this->ids)) {
            $filters["{$this->data_table}_id"] = [];
        }

        // Perform the publishing
        foreach ($this->bundles as $bundle) {
            // Get records that will get published
            $query = db_select("chado_{$bundle->name}", 'CB');
            $query->rightJoin("chado.{$this->data_table}", 'CT', "CB.record_id = CT.{$this->primary_key}");
            $query->addField('CT', $this->primary_key, 'chado_record_id');
            $query->isNull('CB.record_id');
            $record_ids = $query->execute()->fetchCol();

            // Suppress Output Messages
            ob_start();

            // Publish Records
            $published = tripal_chado_publish_records([
                'bundle_name' => $bundle->name,
            ]);

            // Clean the output buffer
            ob_end_clean();

            if (! $published) {
                $this->delete();
                throw new \Exception("Publishing $this->data_table failed!");
            }

            $this->published[] = [
                'bundle' => $bundle->name,
                'ids' => $record_ids,
            ];
        }
    }

    /**
     * Deletes the published entities.
     */
    public function delete()
    {
        // For each bundle get the published entities and delete them
        foreach ($this->published as $item) {
            if(empty($item->ids)) {
                continue;
            }

            $query = db_select("chado_{$item->bundle}", 'CB');
            $query->fields('CB', ['record_id']);
            $query->condition('record_id', $item->ids, 'IN');
            $entities = $query->execute()->fetchCol();
            entity_delete_multiple('TripalEntity', $entities);
        }
    }
}
