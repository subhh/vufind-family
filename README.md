# VuFind Record Family View Helper

Record Family View Helper is copyright (c) 2023 by Staats- und Universitätsbibliothek Hamburg and released under the terms of the GNU
General Public License v3.

## Usage

Add the View Helper to the Laminas module configuration.

```php
$config = [
    'view_helpers' => [
        'aliases' => [
            'family' => SUBHH\VuFind\Family\ViewHelper::class,
        ],
        'invokables' => [
            SUBHH\VuFind\Family\ViewHelper::class => SUBHH\VuFind\Family\ViewHelper::class
        ],
        ...
    ],
    ...
]
```

## Authors

David Maus &lt;david.maus@sub.uni-hamburg.de&gt;
