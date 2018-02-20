# Laravel Registry

[![Build Status](https://travis-ci.org/shov/laravel-registry.svg?branch=master)](https://travis-ci.org/shov/laravel-registry)
[![Codecov](https://img.shields.io/codecov/c/github/shov/laravel-registry.svg)]()
[![license](https://img.shields.io/github/license/shov/laravel-registry.svg)]()

## Usage

```php
$this->registry
    ->set('foo', 'x')
    ->values(['bar' => 'y']);
    
$xy = $this->registry->get('foo') 
      . $this->registry->get('bar');
      
assert('xy' === $xy);

$empty = $this->registry
            ->set('some', 'value')
            ->set('foo', 'bar')
            ->immutable('answer', 42)
            ->flush(true)
            ->all();
            
assert(empty($empty));
```

### Methods

 Method | Description |
---|---|
has(string $key): bool| Checks does the Registry has value for given key (including the immutable). |
get(string $key, $default = null): mixed | Tries to get value by given key, in a case there is no value for given key will be returned default value.|
all(array $defaults = []): array | Fetches all stored values (as key-value pairs array). Returns default array if no values. Includes immutable. |
set(string $key, $value): static | Tries to set/rewrite value by key. Be careful if key refers to immutable you will got an exception.|
values(array $pairs): static | Tries to set/rewrite values by key and do it with key-value pairs array. Be careful if one given of keys refers to immutable you will got an exception. |
immutable(string $key, $value): static | Tries to set immutable value for given key. Be careful if key already refers to immutable you will got an exception. Important thing is this condition: if you make immutable with key the same of existing the regular one, you can't to get the regular value because getting immutable values has the priority, but you still possible to reset regular value with this key using set() |
forget(string $key): static | Removes regular key-value pair. Be careful if key refers to immutable you will got an exception.|
flush(bool $force): static | Removes all regular key-value pairs. If you pass the true for $force all immutable pairs will removed as well.|
getImmutableKeys(): array | Returns one-dimension array of keys of immutable pairs stored in Registry.|
stopPersist(): static | After calling this function all changes will be holding just in memory and will be lost when script was terminated.|

## Overview

The goal of this project is implement this interface:
```text
,---------------------------------------------.
|RegistryInterface                            |
|---------------------------------------------|
|---------------------------------------------|
|+has(string $key): bool                      |
|                                             |
|+get(string $key, $default = null): mixed    |
|+all(array $defaults = []): array            |
|                                             |
|+set(string $key, $value): static            |
|+values(array $pairs): static                |
|                                             |
|+immutable(string $key, $value): static      |
|                                             |
|+forget(string $key): static                 |
|+flush(bool force): static                   |
|                                             |
|+getImmutableKeys(): array                   |
|                                             |                                             |
|+stopPersist(): static                       |
`---------------------------------------------'
```

And provide it via IoC container two ways:
1. DI via `public function __constructor(Registry $registry)`
2. As the Facade `Registry::has('key');`

The save and loading data are making with the members given by constructor 
according to interfaces SaverInterface, LoaderInterface. 
I would like to provide several implementation looks like 
DbSaver, StorageSaver, FakeSaver (which do nothing when saving). 
So let's going to interfaces:

```text
,---------------------.
|SaverInterface       |
|---------------------|
|---------------------|
|+save(array $pairs)  |
`---------------------'

,---------------------.
|LoaderInterface      |
|---------------------|
|---------------------|
|+load(): array       |
`---------------------'
```
Then it should be great to publish the config which set the implementations 
we will use when run

For a while I'll put the tasks to the Issues tab

#### Locking
Well now I explain my point about locking.

```text
,---------------------------------------------.
|RegistryInterface                            |
|---------------------------------------------|
|---------------------------------------------|
|                                             |
|+lock(string ...$keys): static               |
|+lockAll(): static                           |
|+getLockedKeys(): array                      |
|                                             |
`---------------------------------------------'
```

First, the locking isn't persisting, that means the pairs which you have to lock,
should be locked every time script run in code. 
Before you'll lock it the values can be changed.

Consequently as well as you desired make the pair locked 
you've got no method to unlock while script going on. 

However the important thing is keeping in you mind 
the regular Registry has a global storage 
and any other process can change the value of the locked pair. 

Global instances are dangerous. Actually I have to make think about it bit more.
May be there is the reason to persist locked pairs 
or otherwise don't persist them never

The case I put locking to another instance:
```text
,---------------------------------.
|LockerInterface                  |
|---------------------------------|
|---------------------------------|
|+lock(string ...$keys): array    |
|+unlock(string ...$keys): static |
|                                 |
|+isLocked(string $key): bool     |
|+getLockedKeys(): array          |
`---------------------------------'
```

Just now I put locking to the backlog for a while.   
