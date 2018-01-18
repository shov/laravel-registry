# Laravel Registry

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
|+forget(string $key): static                 |
|+flush(bool force): static                   |
|                                             |
|+lock(string ...$keys): static               |
|+lockAll(): static                           |
|+getLockedKeys(): array                      |
|                                             |
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
|+save(array $values) |
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