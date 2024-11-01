<?php
namespace SFR;

class App {

    private $providers = [
        \SFR\Providers\MigrationsProvider::class,
        \SFR\Providers\JobsProvider::class
    ];

    public function run() {

        // Run the providers
        foreach( $this->providers as $provider ) {
            (new $provider)->run();
        }
    }
}