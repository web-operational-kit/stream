# Stream

This library is a Stream handler.

**Diclaimer :** This component is part of the [WOK](https://github.com/web-operational-kit/) (Web Operational Kit) framework. It however can be used as a standalone library.


## Install

It is recommanded to install that component as a dependency using [Composer](https://getcomposer.org/) :

    composer require wok/stream


You're also free to get it with [git](https://git-scm.com/) or by [direct download](https://github.com/web-operational-kit/stream/archive/master.zip) while this package has no dependencies.

    git clone https://github.com/web-operational-kit/stream.git


## Usage

``` php
use \WOK\Stream\Stream;

/**
 * Instanciation
 * ---
**/

// Instanciate handler from a resource (default)
$resource = fopen('php://temp', 'w+');
$stream = new Stream($resource);

// Instanciate handler from a file
$stream = Stream::createFromFile('/path/to/my/file.txt');

// Instanciate handler from a string
$stream = Stream::createFromString('here is my string');
