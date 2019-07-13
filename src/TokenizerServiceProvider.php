<?php

namespace Mobilexco\Tokenizer;

use Illuminate\Support\ServiceProvider;

class TokenizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tokenizer');

        $this->publishes([
            __DIR__.'/../config/tokenizer.php' => base_path('config/tokenizer.php'),
        ], 'config');

        if (! class_exists('CreateTokensTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_tokens_table.php.stub' => database_path(
                    'migrations/'.date('Y_m_d_His', time()).'_create_tokens_table.php'
                ),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tokenizer.php', 'tokenizer');
    }
}
