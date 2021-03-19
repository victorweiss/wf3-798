<?php

namespace App\Service;

class TestService
{
    public function sayHello(string $name): string
    {
        return "Bonjour $name";
    }

    public function sayGoodbye(string $name): string
    {
        return "Au revoir $name";
    }
}
