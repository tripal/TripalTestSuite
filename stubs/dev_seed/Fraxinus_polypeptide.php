

<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

class PolypeptideFASTASeeder extends Seeder
{

  protected $organism_name = NULL;
  protected $analysis_id = NULL;
  protected $remote_file = 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/sequences/FexcelsiorAA.minoas.fasta';
  protected $local_file = NULL;


  public function define($organism = NULL, $local_file = NULL){
    $this->organism = $organism;

  }
  
    public function up()
    {
    	$importer = new \FASTAImporter();     
    //If organism exists, fetch.  Otherwise, create

       $organism_id = db_select('chado.organism', 'o')
       ->fields('o', ['organism_id']
       	->condition('common_name', $this->organism_name))
       ->execute()
       ->fetchField();

       if (!$organism_id) {
       
       }

       $analysis_id = $this->analysis_id;

if (!$analysis_id){
    $analysis = factory('chado.analysis')->create(['name' => 'F. excelsior protein annotation analysis.']);
    $analysis_id = $analysis->analysis_id;
}
    $run_args = ['organism_id' => $organism_id, 'analysis_id' => $analysis_id];
   
   $file = ['file_remote' => $this->remote_file];

   if ($this->local_file){
  $file = ['file_local' => $this->local_file];
   }

    $importer->create($run_args, $file);
    $importer->run();
    }
    
}



