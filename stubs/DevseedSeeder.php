<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

class DevSeedSeeder extends Seeder {

  /**
   * Files.
   * Each importer will take a file argument.  This argument should be an array
   * with one of the following two keys: file_remote => url where the file is
   * located file_local => server path where the file is located
   */

  //protected $protein_file = ['file_local' => '/path/to/local/file'];
  protected $landmark_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/sequences/empty_landmarks.fasta'];

  protected $landmark_type = 'scaffold';

  protected $mRNA_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/sequences/mrna_mini.fasta'];

  protected $protein_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/sequences/polypeptide_mini.fasta'];

  protected $gff_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/gff/filtered.gff'];

  protected $blast_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/gff/filtered.gff'];

  protected $biomaterial_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/biomaterials/biomaterials.xml'];

  protected $expression_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/Fexcel_mini/expression/expression.tsv'];

  //coming soon
  protected $interpro_file = ['file_remote' => NULL];


  //Regular expression that will link the protein name to the mRNA parent feature name.
  // protected $prot_regexp = '/(FRA.*?)(?=:)/';

  protected $prot_regexp = NULL;

  protected $organism_name = 'F. excelsior miniature';

  //Don't change these variables here!  Instead, change the values in the factory within the constructor below.
  protected $organism = NULL;

  protected $sequence_analysis = NULL;

  protected $blastdb = NULL;

  protected $expression_analysis = NULL;

  public function __construct() {

    $organism = db_select('chado.organism', 'o')
      ->fields('o', [
        'common_name',
        'genus',
        'species',
        'organism_id',
        'abbreviation',
        'comment',
      ])
      ->condition('common_name', $this->organism_name)
      ->execute()->FetchObject();

    if (!$organism) {
      $organism = factory('chado.organism')->create([
        'common_name' => $this->organism_name,
        'genus' => 'Fraxinus',
        'species' => 'excelsior',
        'abbreviation' => 'F. excelsor',
        'comment' => 'Loaded with TripalDev Seed.',
      ]);
    }

    $this->organism = $organism;
    $sequence_analysis = factory('chado.analysis')->create([
      'name' => 'Fraxinus exclesior miniature dataset',
      'description' => 'Tripal Dev Seed',
    ]);

    $this->sequence_analysis = $sequence_analysis;

    $expression_analysis = factory('chado.analysis')->create([
      'name' => 'Fraxinus exclesior miniature dataset Expression Analysis',
      'description' => 'Tripal Dev Seed',
    ]);

    $this->expression_analysis = $expression_analysis;

    $this->blastdb = factory('chado.db')->create()->db_id;
  }

  /**
   * Uncomment loaders you would like to run.
   */
  public function up() {

    //first load landmarks.


    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'seqtype' => $this->landmark_type,
      'method' => 2, //default insert and update
      'match_type' => 1, //unique name default
      //optional
      're_name' => NULL,
      're_uname' => NULL,
      're_accession' => NULL,
      'db_id' => NULL,
      'rel_type' => NULL,
      're_subject' => NULL,
      'parent_type' => NULL,

    ];
    //  $this->load_landmarks($run_args, $this->landmark_file);


    $run_args = [
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'organism_id' => $this->organism->organism_id,

      'use_transaction' => 1,
      'add_only' => 0,
      'update' => 1,
      'create_organism' => 0,
      'create_target' => 0,

      ///regexps for mRNA and protein.
      're_mrna' => NULL,
      're_protein' => $this->prot_regexp,
      //optional
      'target_organism_id' => NULL,
      'target_type' => NULL,
      'start_line' => NULL,
      'landmark_type' => NULL,
      'alt_id_attr' => NULL,

    ];

    //  $this->load_GFF($run_args, $this->gff_file);


    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'seqtype' => 'mRNA',
      'method' => 2, //default insert and update
      'match_type' => 1, //unique name default
      //optional
      're_name' => NULL,
      're_uname' => NULL,
      're_accession' => NULL,
      'db_id' => NULL,
      'rel_type' => NULL,
      're_subject' => NULL,
      'parent_type' => NULL,

    ];

   // $this->load_mRNA_FASTA($run_args, $this->mRNA_file);

    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'seqtype' => 'polypeptide',
      'method' => 2,
      'match_type' => 1,


      //optional
      're_name' => NULL,
      're_uname' => NULL,
      're_accession' => NULL,
      'db_id' => NULL,

    ];

    if ($this->prot_regexp) {
      //links polypeptide to mRNA
      $run_args['rel_type'] = 'derives_from';
      $run_args['re_subject'] = $this->prot_regexp;
      $run_args['parent_type'] = 'mRNA';
    }


    // $this->load_polypeptide_FASTA($run_args, $this->protein_file);

    $run_args = [
      'analysis_id' => $this->sequence_analysis->analysis_id,
      //optional
      'query_type' => mRNA,
      'query_re' => $this->prot_regexp,
      'query_uniquename' => NULL,
      'parsego' => TRUE,
    ];

    //$this->load_interpro_annotations($run_args, $this->interpro_file);

    $run_args = [
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'no_parsed' => 25,//number results to parse
      'query_type' => 'mRNA',
      //optional
      'blastdb' => $this->blastdb->db_id,
      'blastfile_ext' => NULL,
      'is_concat' => 0,
      'query_re' => NULL,
      'query_uniquename' => 0,
    ];

    // $this->load_blast_annotations($run_args, $this->blast_file);

    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
    ];
    //optional: specifies specific CVterms for properties/property values.  Not used here.
    //'cvterm_configuration' => NULL,
    //'cvalue_configuration' => NULL];

    //	$this->load_biomaterials($run_args, $this->biomaterial_file);


    $run_args = [
      'filetype' => 'mat', //matrix file type
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
      //optional
      'fileext' => NULL,
      'feature_uniquenames' => 'uniq',
      're_start' => NULL,
      're_stop' => NULL,
      'feature_uniquenames' => NULL,
      'quantificationunits' => NULL,
    ];

    //$this->load_expression($run_args, $this->expression_file);
  }

  private function load_landmarks($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

    $importer = new \FASTAImporter();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_mRNA_FASTA($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');
    $importer = new \FASTAImporter();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_polypeptide_FASTA($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

    $importer = new \FASTAImporter();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_interpro_annotations($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_interpro', 'includes/TripalImporter/InterProImporter');

    $importer = new \InterProImporter();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_GFF($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/GFF3Importer');
    $importer = new \GFF3Importer();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_blast_annotations($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_blast', 'includes/TripalImporter/BlastImporter');
    $importer = new \BlastImporter();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_biomaterials($run_args, $file) {
    module_load_include('inc', 'tripal_biomaterial', 'includes/TripalImporter/tripal_biomaterial_loader_v3');
    $importer = new \tripal_biomaterial_loader_v3();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }

  private function load_expression($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_expression', 'includes/TripalImporter/tripal_expression_data_loader');
    $importer = new \tripal_expression_data_loader();
    $importer->create($run_args, $file);
    $importer->prepareFiles();
    $importer->run();
  }
}
