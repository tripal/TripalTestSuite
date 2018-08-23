Publishing Tripal Entities
**************************

We provide an easy way to convert your chado records into entities. This is the equivalent of
publishing Tripal content using the GUI.

Publishing records is possible in both database seeders and directly in the test class.

The following publishes all features in ``chado.feature`` if they have not been published yet.

.. code-block:: php

	// Get the cvterm id of mRNA
	$cvterm = chado_select_record('cvterm', ['cvterm_id'], ['name' => 'mRNA'])[0];

	// Create 100 mRNA records
	$features = factory('feature', 100)->create(['type_id' => $cvterm->cvterm_id]);

	// Publish all features in chado.feature
	$this->publish('feature');


The following publishes only the given feature ids:

.. code-block:: php

	// Get the cvterm id of mRNA
	$cvterm = chado_select_record('cvterm', ['cvterm_id'], ['name' => 'mRNA'])[0];

	// Create 100 mRNA records
	$features = factory('feature', 100)->create(['type_id' => $cvterm->cvterm_id]);

	// Get the ids of our new features
	$feature_ids = [];
	foreach ($features as $feature) {
		$feature_ids[] = $feature->feature_id;
	}

	// Publish only the given features
	$this->publish('feature', $feature_ids);


The previous examples create mRNA entities.

**NOTE** that an mRNA bundle must already be available before running this script.
