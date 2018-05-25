<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\TripalTestCase;

class TestSilentMethod extends TripalTestCase
{
    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testExampleUsageFromDocs()
    {
        $output = silent(function () {
            drupal_json_output(['key' => 'value']);

            return true;
        });

        $output->assertSee('value')->assertJsonStructure(['key'])->assertReturnEquals(true);
    }
}
