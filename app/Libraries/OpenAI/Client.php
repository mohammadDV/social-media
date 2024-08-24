<?php

namespace App\Libraries\OpenAI;

use Exception;
use OpenAI;
use OpenAI\Client as OpenAIClient;
use OpenAI\Responses\StreamResponse;

class Client
{
    private string $model = 'gpt-3.5-turbo-16k-0613';

    private float $temperature = 0.6;

    private OpenAIClient $client;

    /**
     * Set client
     */
    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }

    /**
     *  Send a request to OpenAI and get suggestions from it.
     *
     * @param  array  $opts  prompt , mode , temperature, ...
     *
     * @throws Exception
     */
    public function completion(array $opts): mixed
    {
        // Set model
        $opts['model'] = $opts['model'] ?? $this->model;

        // Set model
        $opts['temperature'] = $opts['temperature'] ?? $this->temperature;

        // Send request and get response from openAI
        $response = $this->client->completions()->create($opts);

        // Prepare response data
        return $this->processResponse($response);
    }

    /**
     *  Send a request to OpenAI and get suggestions from it.
     *
     * @param  array  $opts  prompt , mode , temperature, ...
     *
     * @throws Exception
     */
    public function chat(array $opts): mixed
    {
        // Set model
        $opts['model'] = $opts['model'] ?? $this->model;

        // Set model
        $opts['temperature'] = $opts['temperature'] ?? $this->temperature;

        // Send request and get response from openAI
        return $this->client->chat()->create($opts);
    }

    /**
     *  Send a request to OpenAI and get suggestions from it.
     *
     * @param  array  $opts  prompt , mode , temperature, ...
     *
     * @throws Exception
     */
    public function chatStreamed(array $opts): StreamResponse
    {
        // Set model
        $opts['model'] = $opts['model'] ?? $this->model;

        // Set model
        $opts['temperature'] = $opts['temperature'] ?? $this->temperature;

        // Send request and get response from openAI
        return $this->client->chat()->createStreamed($opts);
    }

    /**
     *  Prepare response data
     *
     * @return array the response
     */
    private function processResponse(object $data): string
    {
        return $data['choices'][0]['text'] ?? '';
    }
}
