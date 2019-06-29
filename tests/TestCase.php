<?php

namespace Tests;

use Tests\Models\User;
use Metko\Galera\Facades\Galera;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration;

    public function generateModel()
    {
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
        $this->user4 = factory(User::class)->create();

        $this->conversation = Galera::addParticipants([$this->user, 2])->create();
    }
}
