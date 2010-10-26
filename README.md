# Bench

## Purpose

A light-weight class for quickly benchmarking/timing/profiling PHP code.

## Basic Usage

    /**
     * Start & Stop
     */
     include 'class.Bench.php';
     Bench::start();
     // [Some Code To Test]
     echo Bench::stop() . ' Seconds'; -> 2.13274598122
    
    
    /**
     * Marks
     */
     include 'class.Bench.php';
     Bench::start();
     // [Application Bootstrap]
     Bench::mark('bootstrap');
     // [Database Connection Opened]
     Bench::mark('database');
     // [Data Processing + Manipulation]
     Bench::mark('processing');
     // [HTML Creation]
     Bench::mark('html');
     // [MISC]
     Bench::stop();
          
     // Get data on specific mark.
     print_r(Bench::getMarkById('database')); // ->
        Array (
             // The mark id.
             [id] => database
             // The microtime(true) of when this mark occurred.
             [microtime] => 1287969552.88
             // The time (in seconds) since start() was called.
             [since_start] => 1.10582304001
             // The time since the last mark [or] since start(), if first call to mark().
             [since_last_mark] => 0.171210050583
         )
     
     // Get statistics
     print_r(Bench::getStats()); // ->
         Array (
             // The average time between marks (in seconds)
             [mark_average] => 0.346896330516
             // The longest mark
             [mark_longest] => Array
                 (
                     [id] => database
                     [microtime] => 1288045989.62
                     [since_start] => 1.02174592018
                     [since_last_mark] => 0.831446886063
                 )
             // The shortest mark
             [mark_shortest] => Array
                 (
                     [id] => processing
                     [microtime] => 1288045989.64
                     [since_start] => 1.04068899155
                     [since_last_mark] => 0.0189430713654
                 )
             // Start microtime
             [start] => 1288060408.82
             // Stop microtime (if called)
             [stop] => 1288060409.95             
             // Time elapsed (in seconds)
             [elapsed] => 1.0407409668
         )
  
More examples available at http://github.com/veloper/Bench/wiki

## Website

http://dan.doezema.com/2010/10/bench-php-class/

## License

Bench is released under the New BSD license.
http://dan.doezema.com/licenses/new-bsd/