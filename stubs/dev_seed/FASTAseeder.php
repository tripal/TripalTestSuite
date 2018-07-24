<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

class FastaSeeder extends Seeder
{

  protected $organism = NULL;


  public function define($organism = NULL){
    $this->organism = $organism;

  }
  
    public function up()
    {
    	$importer = new \FASTAImporter();     
    //If organism exists, fetch.  Otherwise, create

       $organism_id = db_select('chado.organism', 'o')
       ->fields('o', ['organism_id']
       	->condition('common_name', 'European Ash miniature'))
       ->execute()
       ->fetchField();

       if (!$organism_id) {
       	$organism_id = factory('chado.organism')->create([
       		'common_name' => 'European Ash miniature',
       		'genus' => 'Fraxinus',
       		'species' => 'excelsior',
       		'abbreviation' => 'F. excelsor',
       		'comment' => 'The Tripal Dev Seed miniature dataset.'
       	])->organism_id;
       }

    $analysis = factory('chado.analysis')->create(['name' => 'F. excelsior annotation analysis.']);
    //Fix this
    $run_args = ['organism_id' => $organism_id, 'analysis_id' => $analysis->analysis_id];
   
   $file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/sequences/FexcelsiorCDS.fasta'];

    $importer->create($run_args, $file);
    $importer->run();
    }
    
}
