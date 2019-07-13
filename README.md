# Laravel Tokenizer

Laravel tokenizer replaces predefined attributes or a string passed to a method.

## Installation

Require the package using composer:

```bash
composer require mobilexco/tokenizer
```

## Usage

Add `HasTokens` trait

```php
<?php

namespace App;

use Mobilexco\Tokenizer\HasTokens;

class Post extends Model
{
    use HasTokens;
}
```

Optionally add model attributes to make token replacements

```php
protected $tokenize = ['title', 'body']; 
```

Call method to add token(s) and call method(s) to make replacements

```php
$post = Post::create(['body' => ':greeting:, welcome to Tokenizer!']);

// Option 1, addition via model
$post->addToken(new Token(['key' => 'greeting', 'content' => 'Hello']));

// Option 2, addition via key and content
$post->addToken('greeting', 'Hello');

// Option 3, addition via array
$post->addToken(['key' => 'greeting', 'content' => 'Hello']);

// Make replacements to predefined tokens
// on the model `protected $tokenize = ['title', 'body'];`
$post->replaceTokens();

echo $post->body; // Hello, welcome to tokenizer!

// Make replacements to passed string
$post->replaceTokensInString('What is another greeting besides ":greeting:"'); // What is another greeting besides "Hello"
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.MD)
