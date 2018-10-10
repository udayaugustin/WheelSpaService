<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/ZendSkeletonAdmin for the canonical source repository
* @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

return array(
     'router' => array(
            'routes' => array(
                  'home' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                              'route'    => '/',
                              'defaults' => array(
                              'controller' => 'Admin\Controller\Index',
                              'action'     => 'index',
                              ),
                        ),
                  ),

                  'login' => array(
                        'type' => 'segment',
                        'options' => array(
                              'route'    => '/login[/:action]',
                              'defaults' => array(
                                    'controller' => 'Admin\Controller\Login',
                                    'action' => 'index',
                              ),
                        ),
                  ),
                  
                  'common' => array(
                        'type' => 'segment',
                        'options' => array(
                        'route' => '/common[/:action][/][:id]',
                        'defaults' => array(
                              'controller' => 'Admin\Controller\Common',
                              'action' => 'index',
                        ),
                        ),
                  ),

                  'admin' => array(
                        'type' => 'segment',
                        'options' => array(
                        'route' => '/admin[/:action][/][:id]',
                        'defaults' => array(
                              'controller' => 'Admin\Controller\Admin',
                              'action' => 'index',
                        ),
                        ),
                  ),

               // The following is a route to simplify getting started creating
               // new controllers and actions without needing to create a new
               // module. Simply drop new controllers in, and you can access them
               // using the path /Admin/:controller/:action
               'admin' => array(
                    'type'    => 'Literal',
                    'options' => array(
                         'route'    => '/admin',
                         'defaults' => array(
                              '__NAMESPACE__' => 'Admin\Controller',
                              'controller'    => 'Index',
                              'action'        => 'index',
                         ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                         'default' => array(
                              'type'    => 'Segment',
                              'options' => array(
                                   'route'    => '/admin/[:controller[/:action]]',
                                   'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                   ),
                                   'defaults' => array(
                                   ),
                              ),
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


     'controllers' => array(
          'invokables' => array(
               'Admin\Controller\Index' => 'Admin\Controller\IndexController',
               'Admin\Controller\Common' => 'Admin\Controller\CommonController',
               'Admin\Controller\Login' => 'Admin\Controller\LoginController',
          ),
     ),
     'controller_plugins' => array(
          'invokables' => array(
               'HasParams' => 'Admin\Controller\Plugin\HasParams'
          )
     ),
     'view_manager' => array(
          'display_not_found_reason' => true,
          'display_exceptions'       => true,
          'doctype'                  => 'HTML5',
          'not_found_template'       => 'error/404',
          'exception_template'       => 'error/index',
          'template_map' => array(
               'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
               'admin/index/index' => __DIR__ . '/../view/admin/index/index.phtml',
               'error/404'               => __DIR__ . '/../view/error/404.phtml',
               'error/index'             => __DIR__ . '/../view/error/index.phtml',
          ),
          'template_path_stack' => array(
               __DIR__ . '/../view',
          ),
     ),
     'view_helpers' => array(
          'invokables'=> array(
               'category_helper' => 'Admin\View\Helper\CategoryHelper',
          )
     ),
     // Placeholder for console routes
     'console' => array(
          'router' => array(
               'routes' => array(

               ),
          ),
     ),
);
