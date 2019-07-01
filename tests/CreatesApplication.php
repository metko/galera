<?php

namespace Tests;

use Hash;

trait CreatesApplication
{
    protected function getPackageProviders($app)
    {
        return ['Metko\Galera\Providers\ServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'galera' => 'Metko\Galera\galera',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'sqlite']);
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
        $this->withFactories(__DIR__.'/factories');

        $this->generateModel();
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        Hash::setRounds(4);

        return $app;
    }

    /**
     * Generate the model entity for testing.
     */
    public function generateModel()
    {
        //$this->user = factory(User::class)->create();
    }
}
