<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sastrawi\Dictionary\ArrayDictionary;
use Sastrawi\Stemmer\Cache\ArrayCache;
use Sastrawi\Stemmer\CachedStemmer;
use Sastrawi\Stemmer\Stemmer;
use Sastrawi\Stemmer\StemmerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StemmerInterface::class, function () {
            $dictionaryFile = base_path('vendor/sastrawi/sastrawi/data/kata-dasar.txt');
            $words = [];
            if (file_exists($dictionaryFile)) {
                $content = @file_get_contents($dictionaryFile);
                if ($content !== false) {
                    $words = explode("\n", $content);
                }
            }

            $dictionary = new ArrayDictionary($words);
            $stemmer = new Stemmer($dictionary);

            return new CachedStemmer(new ArrayCache(), $stemmer);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
