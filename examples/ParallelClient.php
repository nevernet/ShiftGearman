<?php

/**
 * An example of sending
 * job to worker.
 */

namespace ShiftGearman\Example;

/**
 * Parallel jobs client
 * This uses the asynchronous worker.
 */
class ParallelClient
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

        // set some arbitrary application data
        $jobData['someDataForTheJob'] = 'bar';

        // add two tasks
        $task= $client->addTask(
            "reverse",
            "reverse this stuff",
            $jobData
        );
        $task2= $client->addTaskLow(
            "reverse",
            "resersing this will take a bit longer",
            NULL
        );

        // run the tasks in parallel (assuming multiple workers)
        if (!$client->runTasks())
        {
            echo "ERROR " . $client->error() . "\n";
            exit;
        }


        // register created callback
        $client->setCreatedCallback(function($client){
            echo "CREATED: " . $client->jobHandle() . "\n";
        });


        //register set data
        $client->setDataCallback(function($task){
            echo "DATA: " . $task->data() . "\n";
        });

        //register set status
        $client->setStatusCallback(function($task){
            $message = "STATUS: " . $task->jobHandle();
            $message .= " - " . $task->taskNumerator();
            $message .= "/" . $task->taskDenominator() . "\n";
            echo $message;
        });

        //set complete callback
        $client->setCompleteCallback(function($task){
            echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        });

        //set task failed
        $client->setFailCallback(function($task){
            echo "FAILED: " . $task->jobHandle() . "\n";
        });


    }



} //class ends here

