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
            ),
        ),

        /*
        * Workers
        * This section configures workers, connections they use and their
        * capabilities.
        */
        'workers' => array(

            //example worker
            'example' => array(
                'description' => 'This is an example worker used for testing',
                'connection' => 'default',
                'jobs' => array(
                    'ShiftGearman\Job\ExampleJob',
                    'ShiftGearman\Job\DieJob',
                )
            ),
        ),

        /*
         * Scheduler
         * This section configures scheduler process, its sleep timeout and
         * maximum iterations before exit.
         */
        'scheduler' => array(
            'timeoutSeconds' => 10,
            'maximumIterations' => 500
        )


    ),

    /*
     * ShiftKernel module configuration
     * Execute gearman migrations in postinstall cli process.
     */
    'ShiftKernel' => array(
        'cli' => array(
            'migrationsSequence' => array(
                999 => 'ShiftGearman' //execute late
            )
        ),
    ),

);

