<?php

/**
 * An example of sending
 * job to worker.
 */

namespace ShiftGearman\Example;


class AsynchronousClient
{
    public function getGearmanVersion()
    {
        return gearman_version();
    }

    /**
     * Send job to gearman
     * client API.
     */
    public function sendJob()
    {
        //create client instance & connect
        $client = new \GearmanClient;
        $client->addServer('127.0.0.1');

        //run job in background
        $job_handle = $client->doBackground("reverse", "this is a test");
        if ($client->returnCode() != GEARMAN_SUCCESS)
        {
          echo "bad return code\n";
          exit;
        }

        //exit normally
        echo 'done';
        return;
    }



} //class ends here

