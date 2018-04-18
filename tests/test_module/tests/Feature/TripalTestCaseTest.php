<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\TripalTestCase;

class TripalTestCaseTest extends TripalTestCase
{
    use DBTransaction;

    protected static $ids = [];

    /** @test */
    public function shouldAddDataToTheDatabase()
    {
        $cvs = factory('chado.cv', 100)->create();

        self::$ids = array_map(function ($cv) {
            return $cv->cv_id;
        }, $cvs);

        $count = (int)db_query('SELECT COUNT(*) FROM chado.cv WHERE cv_id IN (:cv_id)', [
            ':cv_id' => self::$ids,
        ])->fetchField();

        $this->assertEquals($count, 100);
    }

    /**
     * @depends shouldAddDataToTheDatabase
     * @test
     */
    public function shouldNotFindTheDataAddedPreviouslyInTheDatabase()
    {
        $count = (int)db_query('SELECT COUNT(*) FROM chado.cv WHERE cv_id IN (:cv_id)', [
            ':cv_id' => self::$ids,
        ])->fetchField();

        $this->assertEquals($count, 0);
    }
}
