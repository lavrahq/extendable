<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Extra Property
    |--------------------------------------------------------------------------
    |
    | The property within the composer.json `extra` object where extension
    | metadata is stored.
    |
    */

    'extra' => 'extension',

    /*
    |--------------------------------------------------------------------------
    | Type Property
    |--------------------------------------------------------------------------
    |
    | The value within the composer.json `type` to consider an extension
    | when autoloading.
    |
    */

    'type' => 'lavra-extension',

    /*
    |--------------------------------------------------------------------------
    | Package Convention
    |--------------------------------------------------------------------------
    |
    | The prefix for the naming convention of this package. Can be a string
    | or an array of strings. This is used to sanitize the actual package name
    | where necessary.
    |
    | For example, if my convention is `myapp-ext-my-package` or `myapp-extension-my-package`
    | the prefix would be ['myapp-ext-', 'myapp-extension-'].
    |
    */

    'convention' => ['ext-', 'extension-'],

    /*
    |--------------------------------------------------------------------------
    | Entrypoint File
    |--------------------------------------------------------------------------
    |
    | The name of the expected entrypoint file excluding the `.php` extension.
    |
    */

    'entrypoint' => 'extension'

];
