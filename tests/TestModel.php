<?php

namespace Mobilexco\Tokenizer\Tests;

use Illuminate\Database\Eloquent\Model;
    use Mobilexco\Tokenizer\HasTokens;

class TestModel extends Model
{
    use HasTokens;

    public $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
