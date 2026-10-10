<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesGymResources;

abstract class TestCase extends BaseTestCase
{
    use CreatesGymResources;
}
