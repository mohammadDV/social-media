<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use OpenAIClient;
use Goutte\Client;

class AddPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add posts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $client = new Client();
        $crawler = $client->request('GET', 'https://www.fourfourtwo.com/news');

        $articles = [];

        // Check if the node list is empty
        if ($crawler->filter('.listingResult.small')->count() > 0) {
            $crawler->filter('.listingResult.small')->each(function ($node) use (&$articles, $client) {
                $title = $node->filter('a.article-link')->attr('aria-label');
                $url = $node->filter('a.article-link')->attr('href');

                if (!in_array($url, [
                    'https://www.fourfourtwo.com/premier-league',
                    'https://www.fourfourtwo.com/championship',
                    'https://www.fourfourtwo.com/champions-league',
                    'https://www.fourfourtwo.com/la-liga',
                    'https://www.fourfourtwo.com/serie-a',
                    'https://www.fourfourtwo.com/bundesliga',
                    'https://www.fourfourtwo.com/europa-league',
                    'https://www.fourfourtwo.com/world-cup-2022',
                    'https://www.fourfourtwo.com/scottish-premiership',
                ])) {
                    // Visit each article URL to scrape the content
                    $articleCrawler = $client->request('GET', $url);

                    $articleCrawler->filter('#content p')->each(function ($pNode) use (&$content) {
                        $content .= '<p>' . $pNode->text() . '</p>';
                    });

                    // Extract <img> tags
                    $articleCrawler->filter('#content img')->each(function ($imgNode) use (&$content) {
                        $src = $imgNode->attr('src');
                        $alt = $imgNode->attr('alt');
                        $content .= '<img src="' . $src . '" alt="' . $alt . '">';
                    });

                    $translate = '';
                    if (empty($articles)) {
                        $translate = $this->getMessage($content);
                    }

                    $articles[] = [
                        'title' => $title,
                        'url' => $url,
                        'content' => $content,
                        'translate' => $translate,
                    ];
                }

                sleep(1);

            });
        } else {
            // return response()->json(['error' => 'No articles found.'], Response::HTTP_NOT_FOUND);
        }

        Log::info(var_export($articles[0], true));



        // return response()->json($articles);

        // $this->getMessage();
        $this->info(PHP_EOL.'Done');
        return Command::SUCCESS;
    }

    /**
     * Get the payload to send to the OpenAI API
     *
     * @param array $messages The messages to send
     * @return array The payload to send to the OpenAI API
     */
    private function apiPayload(array $messages): array
    {
        // get the base payload
        // $payload = config('openai.ai_summary_model_settings.situation');
        $payload = [
            // 'model' => 'gpt-3.5-turbo-0125',
            // 'model' => 'gpt-4o',
            'model' => 'gpt-4-turbo',
            'temperature' => 0.6,
            'presence_penalty' => 1,
            'n' => 1,
        ];

        // add the messages
        $payload['messages'] = $messages;
        $payload['response_format'] = ['type' => 'json_object'];

        // done, return the payload
        return $payload;
    }

    /**
     * Generate situations that are relevant to the poll using Open AI.
     * @param $product
     * @param int $attempt
     */
    private function getMessage($text)
    {
        // openai request message
        $messages = [
            // [
            //     'role' => 'system',
            //     'content' => '
            //     please give me a list that includes the analysis sport news todays from skysport.
            //     I need url and title and full description of this news and also I need translation of the description to persian.
            //     Give your response alist in JSON using this format:

            //     ```
            //     {
            //     "title": "",
            //     "url" : "",
            //     "content: "",
            //     "translation": " ... "
            //     },
            //     { ...
            //     }
            //     ```

            //     Only reply in JSON nothing else!'
            // ],
            [
                'role' => 'user',
                'content' => '
                 . لطفا این متن را بصورت سلیس و روان به فارسی ترجمه کن به طوری که در هیچ سایتی چنین مقاله ای وجود نداشته باشد.
                من میخواهم یک مقاله شامل ۱۰۰۰ لغات از این متن ایجاد کنی. با ساختاری درست و مفهومی.
                لطفا از تمامی تگ های p استفاده کن
                Start
                ' . $text . '

                End

                Give your response alist in JSON using this format:
                ```
                {
                "translation": " ... "
                },
                { ...
                }
                ```
                Only reply in JSON nothing else!'
            ],
        ];

        // The 'situations' that we will return
        $situations = [];

        // send the messages to OpenAI to get the summary and get the result
        $response = OpenAIClient::chat($this->apiPayload($messages));
        // get response content
        $stringResult = $response->choices[0]->message->content;


        Log::info('---------tttttt');
        Log::info(var_export($stringResult, true));
        return $stringResult;
        // dd($stringResult);

        $this->info(PHP_EOL.'Done');

        // Extract the JSON string
        // preg_match('/\{(?:[^{}]|(?R))*\}/', $stringResult, $matches);
        // $jsonString = $matches[0] ?? '';

        // Parse the JSON string
        // $data = json_decode($jsonString, true);

        // // Validate the data
        // // @todo: we should find a way to also fix typing for $data later.
        // Validator::make($data, [
        //     'situations' => 'required|array',
        //     'situations.*' => 'string',
        // ])->validate();

        // // set id manually
        // $i = 1;

        // // Numbering that we want to remove from the suggestion
        // $extraWord = ["1.", "2.", "3.", "4.", "5.", "1)", "2)", "3)", "4)", "5)", "Suggestion 1:", "Suggestion 2:", "Suggestion 3:"];

        // // Check if the 'situations' attribute exists and is an array
        // foreach ($data['situations'] as $situation) {
        //     // Append clear data to result
        //     $situations[] = [
        //         "id" => $i,
        //         "body" => Str::of($situation)->replace($extraWord, "")->trim(), // Clear the suggestion
        //         "selected" => false,
        //     ];
        //     $i++;
        // }

        // return $situations;
    }
}
