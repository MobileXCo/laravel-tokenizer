<?php

namespace Mobilexco\Tokenizer\Tests;

use Illuminate\Database\Schema\Blueprint;
use Mobilexco\Tokenizer\TokenizerServiceProvider;
use Orchestra\Testbench\TestCase;
use Mobilexco\Tokenizer\Token;

class LaravelTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TokenizerServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/../database/migrations/create_tokens_table.php.stub';

        (new \CreateTokensTable)->up();

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('body')->nullable();
        });
    }

    /** @test */
    public function it_can_access_the_database()
    {
        $token = Token::create([
            'owner_id' => 1,
            'key' => 'my_token',
            'content' => 'Foobar',
        ]);

        $newToken = Token::find($token->id);

        $this->assertSame($newToken->key, 'my_token');
    }

    /** @test */
    public function model_can_add_token_model()
    {
        $testModel = TestModel::create();

        $testModel->addToken(new Token([
            'key' => 'my_token',
            'content' => 'This is my token'
        ]));

        $this->assertEquals(1, $testModel->tokens()->count());
    }

    /** @test */
    public function model_can_add_token_array()
    {
        $testModel = TestModel::create();

        $testModel->addToken([
            'key' => 'my_token',
            'content' => 'This is my token'
        ]);

        $this->assertEquals(1, $testModel->tokens()->count());
    }

    /** @test */
    public function model_can_add_token_key_and_content()
    {
        $testModel = TestModel::create();

        $testModel->addToken('my_token', 'This is my token');

        $this->assertEquals(1, $testModel->tokens()->count());
    }

    /** @test */
    public function model_replace_set_tokens()
    {
        $testModel = TestModel::create(['body' => ':my_token:, :foo:']);
        $testModel->tokenize = ['body'];
        $testModel->addToken('my_token', 'Hello');
        $testModel->addToken('foo', 'these are my tokens');

        $testModel->replaceTokens();

        $this->assertEquals('Hello, these are my tokens', $testModel->body);
    }

    /** @test */
    public function model_replace_string()
    {
        $testModel = TestModel::create();
        $testModel->tokenize = ['body'];
        $testModel->addToken('my_token', 'Hello');
        $testModel->addToken('foo', 'these are my tokens');

        $replacedString = $testModel->replaceTokensInString(':my_token:, :foo:');

        $this->assertEquals('Hello, these are my tokens', $replacedString);
    }
}
