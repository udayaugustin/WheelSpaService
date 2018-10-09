<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Vehicle\Controller\Vehicle' => 'Vehicle\Controller\VehicleController',
        ),
    ),

    // Controller config
    'router' => array(
        'routes' => array(
            'Vehicle' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vehicle[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle\Controller\Vehicle',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../view/error/404.php',
            'error/index'             => __DIR__ . '/../view/error/index.php',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
