<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    'title' => 'SIGAL',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    */

    'logo' => '<b>SIGAL</b>',
    'logo_img' => 'vendor/adminlte/dist/img/liceo1.png',
    'logo_img_alt' => 'SIGAL Logo',
    'logo_img_class' => 'brand-image elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary shadow',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    'layout_topnav' => null,
    'layout_sidebar_fixed' => true,
    'layout_navbar_fixed' => true,
    'layout_footer_fixed' => null,

    /*
    |--------------------------------------------------------------------------
    | Navbar Items
    |--------------------------------------------------------------------------
    */

    'navbar_items' => [
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'type'         => 'logout-button',
            'text'         => 'Cerrar Sesión',
            'topnav_right' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu
    |--------------------------------------------------------------------------
    */

    'menu' => [
        [
            'text' => 'Gestión de Usuarios',
            'url'  => 'admin/usuarios',
            'icon' => 'fas fa-fw fa-users',
        ],
        [
            'text' => 'Config. Académica',
            'url'  => 'admin/academico',
            'icon' => 'fas fa-fw fa-graduation-cap',
        ],
        [
            'text' => 'Materias',
            'url'  => 'admin/materias',
            'icon' => 'fas fa-fw fa-book',
        ],
        [
            'text' => 'Asignación Docente',
            'url'  => 'admin/asignaciones',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
        ],
        [
            'text' => 'Inscripciones',
            'url'  => 'admin/inscripciones',
            'icon' => 'fas fa-fw fa-user-plus',
        ],
        [
            'text' => 'Calificaciones',
            'url'  => 'admin/calificaciones',
            'icon' => 'fas fa-fw fa-star',
        ],
        [
            'text' => 'Reportes Académicos',
            'url'  => 'admin/reportes',
            'icon' => 'fas fa-fw fa-file-pdf',
        ],
        [
            'text' => 'Base de Datos',
            'url'  => 'admin/config/respaldos',
            'icon' => 'fas fa-fw fa-database',
        ],
        [
            'text' => 'Mis Secciones',
            'url'  => 'docente/secciones',
            'icon' => 'fas fa-fw fa-th-list',
        ],
        ['header' => 'PERFIL ESTUDIANTIL'],
        [
            'text' => 'Mis Notas',
            'url'  => 'estudiante/notas',
            'icon' => 'fas fa-fw fa-award',
        ],
        [
            'text' => 'Documentos',
            'url'  => 'estudiante/documentos',
            'icon' => 'fas fa-fw fa-folder-open',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
    ],
];
