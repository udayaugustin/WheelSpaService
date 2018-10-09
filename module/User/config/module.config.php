<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
        ),
    ),

    // Controller config
    'router' => array(
        'routes' => array(
            'User' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'abstract_factories' => array(
             'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
             'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
             'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
            'locale' => 'en_US',
            'translation_file_patterns' => array(
                array(
                    'type'     => 'gettext',
                    'base_dir' => __DIR__ . '/../language',
                    'pattern'  => '%s.mo',
                ),
            ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'             => __DIR__ . '/../view/layout/layout.phtml',
            'user/index/index'          => __DIR__ . '/../view/user/index/index.phtml',
            'error/404'                 => __DIR__ . '/../view/error/404.phtml',
            'error/index'               => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables'=> array(
             'category_helper' => 'Application\View\Helper\CategoryHelper',
        )
   ),
);
