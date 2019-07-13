<?php

namespace Mobilexco\Tokenizer;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

trait HasTokens
{
    public function tokens() : HasMany
    {
        return $this->hasMany(Token::class, 'owner_id');
    }

    /**
     * @param $token string|array|\Illuminate\Database\Eloquent\Model|Token
     * @param  null|string  $content
     * @return false|\Illuminate\Database\Eloquent\Model|Token
     */
    public function addToken($token, $content = null)
    {
        if (is_array($token)) {
            return $this->tokens()->create($token);
        }

        if (is_scalar($token) && is_scalar($content)) {
            return $this->tokens()->create([
                'key' => $token,
                'content' => $content
            ]);
        }

        return $this->tokens()->save($token);
    }

    public function replaceTokens() : void
    {
        $tokensKeyedByKey = $this->getPreparedTokens();
        $this->tokenize = array_filter(Arr::wrap($this->tokenize));
        foreach ($this->tokenize as $toTokenize) {
            if (is_string($this->attributes[$toTokenize])) {
                $this->attributes[$toTokenize] = call_user_func(
                    $this->getReplacementFunction(),
                    array_keys($tokensKeyedByKey),
                    array_values($tokensKeyedByKey),
                    $this->attributes[$toTokenize]
                );
            }
        }
    }

    public function replaceTokensInString(string $string) : string
    {
        $tokensKeyedByKey = $this->getPreparedTokens();

        return call_user_func(
            $this->getReplacementFunction(),
            array_keys($tokensKeyedByKey),
            array_values($tokensKeyedByKey),
            $string
        );
    }

    protected function getPreparedTokens() : array
    {
        $tokenDelimiter = config('tokenizer.token_delimiter', ':');

        return (array) $this->tokens->reduce(function ($carry, $token) use ($tokenDelimiter) {
            $carry[$tokenDelimiter.$token->key.$tokenDelimiter] = $token->content;
            return $carry;
        }, []);
    }

    protected function getReplacementFunction() : string
    {
        return config('tokenizer.is_case_sensitive', true) ? 'str_replace' : 'str_ireplace';
    }
}
