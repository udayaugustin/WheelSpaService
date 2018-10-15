<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Tyre\Controller\Tyre' => 'Tyre\Controller\TyreController',
        ),
    ),

    // Controller config
    'router' => array(
        'routes' => array(
            'tyre' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/tyre[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Tyre\Controller\Tyre',
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
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
