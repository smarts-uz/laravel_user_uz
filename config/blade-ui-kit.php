<?php

use BladeUIKit\Components;

return [

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all components that should be loaded for your app.
    | By default all components from Blade UI Kit are loaded in. You can
    | disable or overwrite any component class or alias that you want.
    |
    */

    'components' => [
        'alert' => Components\Alerts\Alert::class,
        'avatar' => Components\Support\Avatar::class,
        'carbon' => Components\DateTime\Carbon::class,
        'checkbox' => Components\Forms\Inputs\Checkbox::class,
        'color-picker' => Components\Forms\Inputs\ColorPicker::class,
        'countdown' => Components\DateTime\Countdown::class,
        'cron' => Components\Support\Cron::class,
        'dropdown' => Components\Navigation\Dropdown::class,
        'easy-mde' => Components\Editors\EasyMDE::class,
        'email' => Components\Forms\Inputs\Email::class,
        'error' => Components\Forms\Error::class,
        'flat-pickr' => Components\Forms\Inputs\FlatPickr::class,
        'form' => Components\Forms\Form::class,
        'form-button' => Components\Buttons\FormButton::class,
        'html' => Components\Layouts\Html::class,
        'input' => Components\Forms\Inputs\Input::class,
        'label' => Components\Forms\Label::class,
        'logout' => Components\Buttons\Logout::class,
        'mapbox' => Components\Maps\Mapbox::class,
        'markdown' => Components\Markdown\Markdown::class,
        'password' => Components\Forms\Inputs\Password::class,
        'pikaday' => Components\Forms\Inputs\Pikaday::class,
        'social-meta' => Components\Layouts\SocialMeta::class,
        'textarea' => Components\Forms\Inputs\Textarea::class,
        'toc' => Components\Markdown\ToC::class,
        'trix' => Components\Editors\Trix::class,
        'unsplash' => Components\Support\Unsplash::class,
        'eimzo_login' => Components\Eimzo\EimzoLogin::class,
        'eimzo_login_update_button' => Components\Eimzo\EimzoUpdateButton::class,
        'eimzo_login_sign_button' => Components\Eimzo\EimzoSignButton::class,
        'laravelYajra' => Components\Yajra\laravelYajra::class,
        'laravelUppy' => Components\Uppy\laravelUppy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire Components
    |--------------------------------------------------------------------------
    |
    | Below you reference all the Livewire components that should be loaded
    | for your app. By default all components from Blade UI Kit are loaded in.
    |
    */

    'livewire' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Components Prefix
    |--------------------------------------------------------------------------
    |
    | This value will set a prefix for all Blade UI Kit components.
    | By default it's empty. This is useful if you want to avoid
    | collision with components from other libraries.
    |
    | If set with "buk", for example, you can reference components like:
    |
    | <x-buk-easy-mde />
    |
    */

    'prefix' => '',

    /*
    |--------------------------------------------------------------------------
    | Third Party Asset Libraries
    |--------------------------------------------------------------------------
    |
    | These settings hold reference to all third party libraries and their
    | asset files served through a CDN. Individual components can require
    | these asset files through their static `$assets` property.
    |
    */

    'assets' => [

        'alpine' => 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.3.5/dist/alpine.min.js',

        'easy-mde' => [
            'https://unpkg.com/easymde/dist/easymde.min.css',
            'https://unpkg.com/easymde/dist/easymde.min.js',
        ],

        'flat-pickr' => [
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.js',
        ],

        'mapbox' => [
            'https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.css',
            'https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.js',
        ],

        'moment' => [
            'https://cdn.jsdelivr.net/npm/moment@2.26.0/moment.min.js',
            'https://cdn.jsdelivr.net/npm/moment-timezone@0.5.31/builds/moment-timezone-with-data.min.js',
        ],

        'pickr' => [
            'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css',
            'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js',
        ],

        'pikaday' => [
            'https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css',
            'https://cdn.jsdelivr.net/npm/pikaday/pikaday.js',
        ],

        'trix' => [
            'https://unpkg.com/trix@1.2.3/dist/trix.css',
            'https://unpkg.com/trix@1.2.3/dist/trix.js',
        ],
        'eimzo_login' => [
            "vendor/eimzo/assets/js/eimzo/e-imzo.js",
            "vendor/eimzo/assets/js/eimzo/e-imzo-client.js",
            "vendor/eimzo/assets/js/eimzo/imzo.js",
            'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js',
            'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
            'https://code.jquery.com/jquery-3.5.1.js',
            'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js',
            'https://demos.creative-tim.com/notus-js/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css',
        ],
        'yajra' => [
            'https://cdn.jsdelivr.net/npm/datatables.net-dt@1.12.1/css/jquery.dataTables.min.css',
            'https://cdn.jsdelivr.net/npm/datatables.net-responsive-dt@2.3.0/css/responsive.dataTables.min.css',
            'https://cdn.jsdelivr.net/gh/DataTables/Dist-DataTables-SearchBuilder-DataTables@1.2.0/css/searchBuilder.dataTables.min.css',
            'https://cdn.jsdelivr.net/npm/datatables.net-datetime@1.2.0/dist/dataTables.dateTime.min.css',
            'https://cdn.jsdelivr.net/npm/datatables.net-fixedheader-dt@3.3.1/css/fixedHeader.dataTables.min.css',
            'https://cdn.jsdelivr.net/npm/datatables.net-fixedheader-dt@3.3.1/js/fixedHeader.dataTables.min.js',
            'https://cdn.jsdelivr.net/gh/DataTables/Dist-DataTables-Buttons-DataTables@2.2.2/css/buttons.dataTables.min.css',
            'https://cdn.jsdelivr.net/npm/datatables.net@1.12.1/js/jquery.dataTables.min.js',
            'https://cdn.jsdelivr.net/npm/datatables.net-searchbuilder@1.2.0/js/dataTables.searchBuilder.min.js',
            'https://cdn.jsdelivr.net/npm/datatables.net-datetime@1.2.0/dist/dataTables.dateTime.min.js',
            'https://cdn.jsdelivr.net/npm/datatables.net-responsive@2.3.0/js/dataTables.responsive.min.js',
            'https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js',
            'https://cdn.jsdelivr.net/gh/DataTables/Plugins@1.10.24/dataRender/datetime.js',
            'https://cdn.jsdelivr.net/gh/DataTables/Plugins@1.10.19/sorting/datetime-moment.js'
        ],
        'uppy' => [
            'https://releases.transloadit.com/uppy/v2.4.1/uppy.min.css',
            'https://releases.transloadit.com/uppy/v2.4.1/uppy.min.js',
        ]
    ],

];
