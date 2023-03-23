<?php

namespace App\Service;

class CheckPassword
{
    public function check(string $pswd): bool
    {
        return
            strlen($pswd) >= 6 &&
            $pswd != 'azerty' &&
            $pswd != 'qwerty' &&
            $pswd != '123456';
    }
}