<?php
return array(

    /*
     * ShiftGearman module configuration
     */
    'ShiftGearman' => array(

        /*
         * Connections
         * This connections will be used to create client and worker
         * connections and is basically a pool of gearman servers.
         */
        'connections' => array(

            //default gearman connection
            'default' => array(
                'timeout' => null,
                'servers' => array(
                    array('host' => '127.0.0.1', 'port' => 4730)
                )
            )
        ),

        /*
         * Workers
         * This section configures workers, connections they use and their
         * capabilities.
         */
        'workers' => array(

            //example worker
            'example' => array(
                'connection' => 'default',
                'jobs' => array(
                    'ShiftGearman\Job\ExampleJob'
                )
            )
        )
    )
);