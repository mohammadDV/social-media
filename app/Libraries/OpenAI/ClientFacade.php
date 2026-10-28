<?php

namespace App\Libraries\OpenAI;

use Illuminate\Support\Facades\Facade;
use OpenAI\Responses\StreamResponse;

/**
 * @method static mixed chat(array $opts)
 * @method static mixed completion(array $opts)
 * @method static StreamResponse chatStreamed(array $opts)
 *
 * @see Client
 */
class ClientFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'openaiClient';
    }
}
