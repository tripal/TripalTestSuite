<?php


namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;


$protein_file_path = NULL;
// this is the file that does blah...

$organism  = factory('chado.organism')->create([
       		'common_name' => $this->organism_name,
       		'genus' => 'Fraxinus',
       		'species' => 'excelsior',
       		'abbreviation' => 'F. excelsor',
       		'comment' => 'The Tripal Dev Seed miniature dataset.'
       	])->organism_id;