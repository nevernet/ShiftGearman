# ShiftGearman job queue and scheduler

This module provides integration of [Gearman](http://gearman.org/) with ZendFramework. And allows you to offload job execution to background worker process with support for:

- Running tasks synchronously on worker (wait for response)
- Running tasks asynchronously in background (fire an forget)
- Planning task to run at the given time
- Repeating task with a given interval

## Dependencies

__Gearman PHP extension__

The module wraps functionality of standard [PHP Gearman extension] (http://php.net/manual/en/book.gearman.php) so you need gearman server running and extension enabled. It is advised to run gearman with persistent que support not to loose queued jobs if server crashes.

__Background process controller__

Executing jobs requires one (or more) background worker processes capable of running a job. Although you can run the worker from CLI it is advised that you have some sort of process control service capable of running, monitoring and restarting worker processes. We recommend using tools like [Supervisor](http://supervisord.org/) or [Daemontools](http://cr.yp.to/daemontools.html)

__ShiftDoctrine module (for scheduler queue)__

Gearman server does support delayed and scheduled tasts, although PHP extension does not support it. That is why the module implements its own scheduling functionality that requires a doctrine for scheduler queue persistence.

## Writing job procedures

Job is a certain piece of functionality registered by a given name within workers. Workers then listen for commands to execute a job the name it was registered. This commands are called tasks (see sections below). To write your job, extend base ShiftGearman\Job\AbstractJob and define a name by wich this job will be called, a description of what job does and the actual functionality code in Job::execute() method.

## Calling job execution with Tasks

A Task is basically a directive to execute certain Job with provided properties that include:

- Job name (what job to run)
- Workload (what data to pass to job)
- Priority (may be high, normal or low)
- Start time (when to start the task)
- Repeat times and interval (how much times to repeat and with what interval)

Essentially you create a new Task, configure it through convenient API and pass for execution. The GearmanService will then decide whether the task should be executed immediately or put to scheduler queue.

Here is an example of creating a task:

```PHP
$task = new \ShiftGearman\Task;
$task->setJobName('shiftgearman.example')
    ->setWorkload('Pass this data to task')
    ->priorityHigh()
    ->runInBackground()
    ->setRepeat(3, 'PT1M');

$service = $this->locator->get('ShiftGearman\GearmanService');
$service->add($task);
```
 