<?php

return [
    /**
     * DataTables search options.
     */
    'search'          => [
        /**
         * Smart search will enclose search keyword with wildcard string "%keyword%".
         * SQL: column LIKE "%keyword%"
         */
        'smart'            => true,

        /**
         * Case insensitive will search the keyword in lower case format.
         * SQL: LOWER(column) LIKE LOWER(keyword)
         */
        'case_insensitive' => false,

        /**
         * Wild card will add "%" in between every characters of the keyword.
         * SQL: column LIKE "%k%e%y%w%o%r%d%"
         */
        'use_wildcards'    => false,
    ],

    /**
     * DataTables fractal configurations.
     */
    'fractal'         => [
        /**
         * Request key name to parse includes on fractal.
         */
        'includes'   => 'include',

        /**
         * Default fractal serializer.
         */
        'serializer' => 'League\Fractal\Serializer\DataArraySerializer',
    ],

    /**
     * DataTables script view template.
     */
    'script_template' => 'datatables::script',

    /**
     * DataTables internal index id response column name.
     */
    'index_column'    => 'DT_Row_Index',

    /*
     * List of available builders for DataTables.
     * This is where you can register your custom dataTables builder.
     */
    'engines' => [
        'eloquent'                    => \Tobuli\Lookups\EloquentDataTable::class,
        'query'                       => \Yajra\DataTables\QueryDataTable::class,
        'collection'                  => \Yajra\DataTables\CollectionDataTable::class,
        'resource'                    => \Yajra\DataTables\ApiResourceDataTable::class,
    ],

    /**
     * Namespaces used by the generator.
     */
    'namespace'       => [
        /**
         * Base namespace/directory to create the new file.
         * This is appended on default Laravel namespace.
         * Usage: php artisan datatables:make User
         * Output: App\DataTables\UserDataTable
         * With Model: App\User (default model)
         * Export filename: users_timestamp
         */
        'base'  => 'DataTables',

        /**
         * Base namespace/directory where your model's are located.
         * This is appended on default Laravel namespace.
         * Usage: php artisan datatables:make Post --model
         * Output: App\DataTables\PostDataTable
         * With Model: App\Post
         * Export filename: posts_timestamp
         */
        'model' => '',
    ],

    /**
     * PDF generator to be used when converting the table to pdf.
     * Available generators: excel, snappy
     * Snappy package: barryvdh/laravel-snappy
     * Excel package: maatwebsite/excel
     */
    'pdf_generator'   => 'snappy',

    /**
     * Snappy PDF options.
     */
    'snappy'          => [
        'options'     => [
            'no-outline'    => true,
            'margin-left'   => '0',
            'margin-right'  => '0',
            'margin-top'    => '10mm',
            'margin-bottom' => '10mm',
        ],
        'orientation' => 'landscape',
    ],

    /**
     * JsonResponse header and options config.
     */
    'json'           => [
        'header'  => [],
        'options' => 0,
    ],
];
