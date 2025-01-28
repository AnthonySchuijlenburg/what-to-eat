<?php

namespace App\Console\Commands\Ingredients;

use App\Models\Ingredient;
use Carbon\Carbon;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Console\Command;

class EnrichIngredients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingredients:enrich {limit=25}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ingredients = Ingredient::query()
            ->whereNull('enriched_at')
            ->oldest()
            ->limit($this->argument('limit'))
            ->get();

        foreach ($ingredients as $ingredient) {
            try {
                $response = Ollama::agent(
                    '
                        You are a backend service only allowed to respond in a structured JSON-format.
                        You will be provided with a string in Dutch and the result should also be in dutch.
                        Never use abbreviations, when provided with "el" or "g" use "eetlepel" or "gram".
                        Never use unicode, always writhe fractions out fully as "1/2" or "1/4".
                        This format consist of the following entries: {"name": "name", "amount": "amount", "amount_in_grams": amount_in_grams}.
                        An example; provided: "1 teentje knoflook", response: {"name": "knoflook", "amount": "1 teentje", "amount_in_grams": 5}
                        Another example; provided: "500 gram aardappelen", response: {"name": "aardappelen", "amount": "500 gram", "amount_in_grams": 500}
                        Another example; provided: "½ theelepel komijnzaad", response: {"name": "komijnzaad", "amount": "1/2 theelepel", "amount_in_grams": 2.5}
                        Make sure these values always have a default value, so if no meassurement is provided make an educated guess:
                        provided: "citroensap", response: {"name": "citroensap", "amount": "1 scheutje", "amount_in_grams": 5}
                    '
                )
                    ->options([
                        'num_thread' => 2,
                        'temperature' => 0.6,
                    ])
                    ->prompt($ingredient->source)
                    ->stream(false)
                    ->ask();
                $response = json_decode($response['response'], true);

                $ingredient->update([
                    ...$response,
                    'enriched_at' => now(),
                ]);

                $ingredient->save();
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
                $ingredient->enriched_at = new Carbon('01-01-2030');
                $ingredient->save();
            }
        }
    }
}
