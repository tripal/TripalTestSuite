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

  protected $mRNA_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/sequences/FexcelsiorCDS.fasta'];

  protected $protein_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/sequences/FexcelsiorAA.minoas.fasta'];

  protected $gff_file = ['file_remote' => 'https://raw.githubusercontent.com/statonlab/tripal_dev_seed/master/gff/Fexcelsior_filtered.gff3'];

//coming soon
  protected $blast_file = ['file_remote' => NULL];

  protected $interpro_file = ['file_remote' => NULL];

  protected $biomaterial_file = ['file_remote' => NULL];

  protected $expression_file = ['file_remote' => NULL];

//Regular expression that will link the protein name to the mRNA parent feature name.
  protected $prot_regexp = '/(FRA.*?)(?=:)/';

//Don't change these variables here!  Instead, change the values in the factory within the constructor below.
  protected $organism = NULL;

  protected $sequence_analysis = NULL;

  protected $blastdb = NULL;

  public function constructor() {

    $organism = factory('chado.organism')->create([
      'common_name' => $this->organism_name,
      'genus' => 'Fraxinus',
      'species' => 'excelsior',
      'abbreviation' => 'F. excelsor',
      'comment' => 'The Tripal Dev Seed miniature dataset.',
    ]);

    $this->organism_id = $organism;
    $sequence_analysis = factory('chado.analysis')->create([
      'common_name' => $this->organism_name,
      'genus' => 'Fraxinus',
      'species' => 'excelsior',
      'abbreviation' => 'F. excelsor',
      'comment' => 'The Tripal Dev Seed miniature dataset.',
    ]);

    $this->sequence_analysis = $sequence_analysis;

    $this->blastdb = factory('chado.db')->create()->db_id;
  }

  /**
   * Uncomment loaders you would like to run.
   */
  public function up() {

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

    //$this->load_mRNA_FASTA($run_args, $mrna_file);

    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->sequence_analysis->analysis_id,
      'seqtype' => 'polypeptide',
      'method' => 2,
      'match_type' => 1,

      //links polypeptide to mRNA
      'rel_type' => 'derives_from',
      're_subject' => $this->prot_regexp,
      'parent_type' => 'mRNA',

      //optional
      're_name' => NULL,
      're_uname' => NULL,
      're_accession' => NULL,
      'db_id' => NULL,

    ];

    //$this->load_polypeptide_FASTA($run_args, $protein_file);

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

    //$this->load_GFF($run_args, $gff_file);

    $run_args = [
      'analysis_id' => $this->sequence_analysis->analysis_id,
      //optional
      'query_type' => mRNA,
      'query_re' => $this->prot_regexp,
      'query_uniquename' => NULL,
      'parsego' => TRUE,
    ];

    //$this->load_interpro_annotations($run_args, $interpro_file);

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

    //$this->load_blast_annotations($run_args, $blast_annotation_file);

    $run_args = [
      'organism_id' => $this->organism->organism_id,
      'analysis_id' => $this->analysis->analysis_id,
    ];
//optional: specifies specific CVterms for properties/property values.  Not used here.
//'cvterm_configuration' => NULL,
//'cvalue_configuration' => NULL];

//	$this->load_biomaterials($run_args, $biomaterial_file);

    $run_args = [
      'filetype' => 'mat', //matrix file type
      'organism_id' => $this->organism_organism_id,
      //optional
      'fileext' => NULL,
      'feature_uniquenames' => 'uniq',
      're_start' => NULL,
      're_stop' => NULL,
      'feature_uniquenames' => NULL,
      'quantificationunits' => NULL,
    ];
    // $this->load_expression($run_args, $expression_file);
  }

  private function load_mRNA_FASTA($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

    $importer = new FASTAImporter();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_polypeptide_FASTA($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/FASTAImporter');

    $importer = new FASTAImporter();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_interpro_annotations($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_interpro', 'includes/TripalImporter/InterProImporter');

    $importer = new InterProImporter();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_GFF($run_args, $file) {
    module_load_include('inc', 'tripal_chado', 'includes/TripalImporter/GFFImporter');
    $importer = new GFFImporter();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_blast_annotations($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_blast', 'includes/TripalImporter/BlastImporter');
    $importer = new BlastImporter();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_biomaterials($run_args, $file) {
    module_load_include('inc', 'tripal_biomaterial', 'includes/TripalImporter/tripal_biomaterial_loader_v3');
    $importer = new tripal_biomaterial_loader_v3();

    $importer->create($run_args, $file);
    $importer->run();
  }

  private function load_expression($run_args, $file) {
    module_load_include('inc', 'tripal_analysis_expression', 'includes/TripalImporter/tripal_expression_data_loader');
    $importer = new tripal_expression_data_loader();

    $importer->create($run_args, $file);
    $importer->run();
  }
}
