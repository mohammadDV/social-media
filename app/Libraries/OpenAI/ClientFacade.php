<?php

namespace App\Libraries\OpenAI;

use Illuminate\Support\Facades\Facade;

class ClientFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'openaiClient';
    }
}
