<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Livewire\Livewire::withoutLazyLoading();

        \Livewire\on('flush-state', function () {
            \Livewire\Livewire::withoutLazyLoading();
        });
    }
}
