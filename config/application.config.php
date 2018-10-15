<?php
return array(
    'modules' => array(
        'Application',
        'User',
        'Vehicle',
        'Tyre',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
