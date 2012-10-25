<?php

/**
 * An example of sending
 * job to worker.
 */

namespace ShiftGearman\Example;


class SynchronousClient
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

        do
        {
            //execute while
            $result = $client->do("reverse", "Hello!");

            //check responses
            switch($client->returnCode())
            {
              case GEARMAN_WORK_DATA:
                echo "Data: $result\n";
                break;
              case GEARMAN_WORK_STATUS:
                list($numerator, $denominator)= $client->doStatus();
                echo "Status: $numerator/$denominator complete\n";
                break;
              case GEARMAN_WORK_FAIL:
                echo "Failed\n";
                exit;
              case GEARMAN_SUCCESS:
                  echo "result : $result \\n" ;
                  break;
              default:
                echo "RET: " . $client->returnCode() . "\n";
                exit;
            }

        }
        while($client->returnCode() != GEARMAN_SUCCESS);

        echo 'done';
        return;
    }



} //class ends here

