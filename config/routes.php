<?php
return array(

/*
 * Gearman testing route
 */
'gearman' => array(
    'type'    => 'Zend\Mvc\Router\Http\Literal',
    'options' => array(
        'route'    => '/gearman/',
        'defaults' => array(
            'controller' => 'shiftgearman-controller-gearman',
            'action'     => 'index',
        ),
    ),
),

);