<?php
namespace StatonLab\TripalTestSuite\Mocks;

class CallableMock
{
    public function __invoke()
    {
        $args = func_get_args();
    }
}
