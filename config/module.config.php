<?php
return array(

    /*
     * ShiftMedia module configuration
     */
    'ShiftGearman' => array(

        //workers configuration
        'workers' => array(

            //example worker
            'example' => array(
                'timeout' => null,
                'server' => array('host' => '127.0.0.1', 'port' => 4730),
                'jobs' => array(
                    'ShiftGearman\Job\ExampleJob'
                )
            )
        )
    )
);