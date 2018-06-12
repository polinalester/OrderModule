<?php

namespace Order;

use Zend\Router\Http\Segment;

return [
    // a list of all controllers provided by module
    'router' => [
            // routing
            'routes' => [
                'order' => [
                    'type'    => Segment::class,
                    'options' => [
                        'route' => '/order[/:action[/:id]]', // any url which starts with /order
                        'constraints' => [
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'id'     => '[0-9]+',
                        ],
                        'defaults' => [
                            'controller' => Controller\OrderController::class,
                            'action'     => 'viewOrder',
                        ],
                    ],
                ],
            ],
        ],

    'view_manager' => [
        // a path for view scripts
        'template_path_stack' => [
            'order' => __DIR__ . '/../view',
        ],
    ],
];