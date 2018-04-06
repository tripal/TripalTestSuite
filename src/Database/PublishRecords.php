<?php

namespace StatonLab\TripalTestSuite\Database;

use Exception;

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
    public function __construct($data_table, array $ids = [], $primary_key = null)
    {
        $this->ids = $ids;
        $this->data_table = $data_table;
        $this->primary_key = $primary_key !== null ? $primary_key : chado_get_schema($data_table)['primary key'][0];
    }

    /**
     * Perform the publishing.
     *
     * @throws Exception
     */
    public function publish()
    {
        // Find the bundle
        $this->bundles = db_query('SELECT name, bundle_id FROM chado_bundle
                             JOIN tripal_bundle ON tripal_bundle.id = chado_bundle.bundle_id
                             WHERE data_table = :data_table', [':data_table' => $this->data_table])->fetchAll();

        if (empty($this->bundles)) {
            throw new Exception("Unable to find bundles for $this->data_table. Publishing failed!");
        }

        // Perform the publishing
        foreach ($this->bundles as $bundle) {
            // Publish Records
            try {
                $published = $this->tripalChadoPublishRecords($bundle->name, $this->ids);
            } catch (Exception $exception) {
                $this->delete();
                throw $exception;
            }

            $this->published[] = $published;
        }
    }

    /**
     * Deletes the published entities.
     */
    public function delete()
    {
        // For each bundle get the published entities and delete them
        foreach ($this->published as $entities) {
            entity_delete_multiple('TripalEntity', $entities);
        }
    }

    /**
     * Publishes content in Chado as a new TripalEntity entity.
     * This is a modified version of the publisher that accepts record ids.
     *
     * @param $values
     *   A key/value associative array that supports the following keys:
     *   - bundle_name:  The name of the the TripalBundle (e.g. bio_data_1).
     * @param array $ids Ids of chado data to publish.
     *
     * @return array
     *   Array of created entity ids.
     *
     *
     * @author https://github.com/tripal/tripal
     * @see tripal_chado_publish_records()
     * @throws Exception
     */
    protected function tripalChadoPublishRecords($bundle_name, array $ids = [])
    {
        // Load the bundle entity so we can get information about which Chado
        // table/field this entity belongs to.
        $bundle = tripal_load_bundle_entity(['name' => $bundle_name]);
        if (! $bundle) {
            throw new Exception("Could not publish record: The bundle name must be provided");
        }
        $chado_entity_table = chado_get_bundle_entity_table($bundle);

        // Get the mapping of the bio data type to the Chado table.
        $chado_bundle = db_select('chado_bundle', 'cb')
            ->fields('cb')
            ->condition('bundle_id', $bundle->id)
            ->execute()
            ->fetchObject();

        if (! $chado_bundle) {
            throw new Exception("Cannot find mapping of bundle to Chado tables. Could not publish record.");
        }

        $table = $chado_bundle->data_table;
        $type_column = $chado_bundle->type_column;
        $type_linker_table = $chado_bundle->type_linker_table;
        $cvterm_id = $chado_bundle->type_id;
        $type_value = $chado_bundle->type_value;

        // Get the table information for the Chado table.
        if (! $this->primary_key) {
            $table_schema = chado_get_schema($table);
            $pkey_field = $table_schema['primary key'][0];
        } else {
            $pkey_field = $this->primary_key;
        }

        // Construct the SQL for identifying which records should be published.
        $args = [];
        $select = "SELECT T.$pkey_field as record_id ";
        $from = "FROM {".$table."} T
                 LEFT JOIN [".$chado_entity_table."] CE on CE.record_id = T.$pkey_field";
        if (empty($ids)) {
            $where = " WHERE CE.record_id IS NULL ";
        } else {
            $where = " WHERE CE.record_id IN (:ids)";
            $args[':ids'] = $ids;
        }

        // Handle records that are mapped to property tables.
        if ($type_linker_table and $type_column and $type_value) {
            $propschema = chado_get_schema($type_linker_table);
            $fkeys = $propschema['foreign keys'][$table]['columns'];
            foreach ($fkeys as $leftkey => $rightkey) {
                if ($rightkey == $pkey_field) {
                    $from .= " INNER JOIN {".$type_linker_table."} LT ON T.$pkey_field = LT.$leftkey ";
                }
            }
            $where .= "AND LT.$type_column = :cvterm_id and LT.value = :prop_value";
            $args[':cvterm_id'] = $cvterm_id;
            $args[':prop_value'] = $type_value;
        }

        // Handle records that are mapped to cvterm linking tables.
        if ($type_linker_table and $type_column and ! $type_value) {
            $cvtschema = chado_get_schema($type_linker_table);
            $fkeys = $cvtschema['foreign keys'][$table]['columns'];
            foreach ($fkeys as $leftkey => $rightkey) {
                if ($rightkey == $pkey_field) {
                    $from .= " INNER JOIN {".$type_linker_table."} LT ON T.$pkey_field = LT.$leftkey ";
                }
            }
            $where .= "AND LT.$type_column = :cvterm_id";
            $args[':cvterm_id'] = $cvterm_id;
        }

        // Handle records that are mapped via a type_id column in the base table.
        if (! $type_linker_table and $type_column) {
            $where .= "AND T.$type_column = :cvterm_id";
            $args[':cvterm_id'] = $cvterm_id;
        }

        // Handle the case where records are in the cvterm table and mapped via a single
        // vocab.  Here we use the type_value for the cv_id.
        if ($table == 'cvterm' and $type_value) {
            $where .= "AND T.cv_id = :cv_id";
            $args[':cv_id'] = $type_value;
        }

        // Handle the case where records are in the cvterm table but we want to
        // use all of the child terms.
        if ($table == 'cvterm' and ! $type_value) {
            $where .= "AND T.cvterm_id IN (
                        SELECT CVTP.subject_id
                        FROM {cvtermpath} CVTP
                        WHERE CVTP.object_id = :cvterm_id)";
            $args[':cvterm_id'] = $cvterm_id;
        }

        // Perform the query.
        $sql = $select.$from.$where;
        $records = chado_query($sql, $args);
        $entities = [];

        while ($record = $records->fetchObject()) {
            // First save the tripal_entity record.
            $record_id = $record->record_id;
            /** @var \TripalEntityController $ec */
            $ec = entity_get_controller('TripalEntity');
            $entity = $ec->create([
                'bundle' => $bundle_name,
                'term_id' => $bundle->term_id,
                // Add in the Chado details for when the hook_entity_create()
                // is called and our tripal_chado_entity_create() implementation
                // can deal with it.
                'chado_record' => chado_generate_var($table, [$pkey_field => $record_id]),
                'chado_record_id' => $record_id,
            ]);
            $entity = $entity->save();
            if (! $entity) {
                throw new Exception('Could not create entity.');
            }

            // Next save the chado entity record.
            $entity_record = [
                'entity_id' => $entity->id,
                'record_id' => $record_id,
            ];

            $result = db_insert($chado_entity_table)->fields($entity_record)->execute();
            if (! $result) {
                throw new Exception('Could not create mapping of entity to Chado record.');
            }

            $entities[] = $entity->id;
        }

        return $entities;
    }
}
