# Laravel Registry

[![Build Status](https://travis-ci.org/shov/laravel-registry.svg?branch=master)](https://travis-ci.org/shov/laravel-registry)
[![Codecov](https://img.shields.io/codecov/c/github/shov/laravel-registry.svg)]()
[![license](https://img.shields.io/github/license/shov/laravel-registry.svg)]()


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