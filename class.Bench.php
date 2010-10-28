<?php
/**
 * Copyright (c) 2010, Daniel Doezema
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * The names of the contributors and/or copyright holder may not be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL DANIEL DOEZEMA BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * A light-weight class for quickly benchmarking/timing/profiling PHP code.
 *
 * @copyright  Copyright (c) 2010 Daniel Doezema. (http://dan.doezema.com)
 * @license    http://dan.doezema.com/licenses/new-bsd     New BSD License
 */
class Bench {

    /**
     * Errors that occurred during request.
     *
     * @var array
     */
    protected static $errors = array();
    
    /**
     * Mark arrays.
     *
     * @var array
     */
    protected static $marks = array();
    
    /**
     * Microtime of when self::start() was called.
     *
     * @var float
     */
    protected static $start = null;
    
    /**
     * Microtime of when self::stop() was called.
     *
     * @var float
     */
    protected static $stop = null;
    
    /**
     * Start timer.
     *
     * @return void;
     */
    public static function start() {
        if(self::$start !== null) {
            self::logError('Please call '.__CLASS__.'::reset() before calling '.__CLASS__.'::start() again.');
            return;
        }
        self::$start = microtime(true);
    }
    
    /**
     * Stop timer.
     *
     * @return float; -> self::getElapsed()
     */
    public static function stop() {
        if(self::$stop !== null) {
            self::logError('Please call '.__CLASS__.'::reset() before calling '.__CLASS__.'::stop() again.');
            return;
        }
        self::$stop = microtime(true);
        return self::getElapsed();
    }
    
    /**
     * Reset timer.
     *
     * @return void;
     */
    public static function reset() {
        self::$marks = array();
        self::$start = null;
        self::$stop = null;
    }
    
    /**
     * Mark a point in time.
     *
     * @param string; The id of the mark. (e.g., 'connection_start', 'connected_success', 'connection_fail');
     * @return mixed; Float, the time in seconds since last mark, or if no marks self::$start) - false, on error.
     */
    public static function mark($id) {
        if(self::$start === null) {
            self::logError('Please call '.__CLASS__.'::start() before calling '.__CLASS__.'::mark("'.$id.'").');
            return false;
        }
        $mark = array();
        $mark['id'] = $id;
        $mark['microtime'] = microtime(true);
        $mark['since_start'] = $mark['microtime'] - self::$start;
        $mark['since_last_mark'] = count(self::$marks) ? ($mark['microtime'] - self::$marks[count(self::$marks)-1]['microtime']) : $mark['since_start'];
        self::$marks[] = $mark;
        return $mark['since_last_mark'];
    }
    
    /**
     * Get the marks array.
     *
     * @return array;
     */
    public static function getMarks() {
        return self::$marks;
    }
    
    /**
     * Get a mark by its id.
     *
     * @param string; The id of the existing mark.
     * @return mixed; array on success, false on failure.
     */
    public static function getMarkById($id) {
        foreach(self::$marks as $mark) {
            if($mark['id'] == $id) {
                return $mark;
            }
        }
        return false;
    }
    
    /**
     * Get average time (in seconds) between marks.
     *
     * @return mixed; float on success, false on failure.
     */
    public static function getMarkAverage() {
        if(($mark_count = count($marks = self::getMarks()))) {
            $sum = 0;
            foreach($marks as $mark) $sum += $mark['since_last_mark'];
            return  $sum / $mark_count;
        }
        return false;
    }
    
    /**
     * Get the longest mark based on [since_last_mark].
     *
     * @return mixed; array on success, false on failure.
     */
    public static function getLongestMark() {
        if(count($marks = self::getMarks())) {
            $longest_mark = null;
            foreach($marks as $mark) {
                if(($longest_mark == null) || ($mark['since_last_mark'] > $longest_mark['since_last_mark'])) {
                    $longest_mark = $mark;
                }
            }
            return $longest_mark;
        }
        return false;
    }
    
    /**
     * Get the shortest mark based on [since_last_mark].
     *
     * @return mixed; array on success, false on failure.
     */
    public static function getShortestMark() {
        if(count($marks = self::getMarks())) {
            $shortest_mark = null;
            foreach($marks as $mark) {
                if(($shortest_mark == null) || ($mark['since_last_mark'] < $shortest_mark['since_last_mark'])) {
                    $shortest_mark = $mark;
                }
            }
            return $shortest_mark;
        }
        return false;
    }
            
    /**
     * Get the last/latest mark.
     *
     * @return mixed; array on success, false on failure.
     */
    public static function getLastMark() {
        if(count(self::$marks)) {
            return self::$marks[count(self::$marks)-1];
        }
        return false;
    }
    
    /**
     * Get the time (in seconds) elapsed since a specified mark.
     *
     * @param string; The id of an existing mark.
     * @return mixed; float, false on failure.
     */
    public static function getElaspedSinceMark($id) {
        if($mark = self::getMarkById($id)) {
            return microtime(true) - $mark['microtime'];
        }
        return false;
    }
    
    /**
     * Get the time (in seconds) elapsed since the last mark() call.
     *
     * @return mixed; float, false on error.
     */
    public static function getElaspedSinceLastMark() {
        if($mark = self::getLastMark()) {
            return microtime(true) - $mark['microtime'];
        }
        return false;
    }
    
    /**
     * Get the time elapsed (in seconds) based on context and/or parameters.
     * 
     * getElapsed()
     *   if[stop() has been called] -- Time (in seconds() between start() and stop()
     *   else -- Time (in seconds) between start() and the getElapsed() call.
     * 
     * getElapsed("from_mark_id", "to_mark_id") - Time (in seconds) between marks.
     *
     * @param mixed;
     * @param mixed;
     * @return mixed; float, false on error.
     */
    public static function getElapsed($from_mark_id = null, $to_mark_id = null) {
        $microtime = microtime(true);
        $elapsed = false;
        if(self::$start === null) {
            self::logError('Please call '.__CLASS__.'::start() before calling '.__CLASS__.'::getElapsed()');
            return false;
        }
        if(!$from_mark_id && !$to_mark_id) {
            $minuend = (self::$stop !== null) ? self::$stop : $microtime;
            $elapsed = $minuend - self::$start;
        } else {
            if (($mark_from = self::getMarkById($from_mark_id)) && ($mark_to = self::getMarkById($to_mark_id))) {
                $elapsed = abs($mark_to['microtime'] - $mark_from['microtime']);
            } else {
                if(!$mark_from) self::logError(__CLASS__.'::getElapsed(): A mark with the id of "'.$from_mark_id.'" does not exist.');
                if(!$mark_to) self::logError(__CLASS__.'::getElapsed(): A mark with the id of "'.$to_mark_id.'" does not exist.');
            }
        }
        return $elapsed;
    }
    
    /**
     * Get statistics on what has happened since calling start();
     *
     * @return mixed; array of statistics, false on error.
     */
    public static function getStats() {
        if(self::$start === null) {
            self::logError('Please call '.__CLASS__.'::start() before calling '.__CLASS__.'::getStats()');
            return false;
        }
        $elapsed = self::getElapsed();
        $stats = array();
        if(count(self::getMarks())) {
            // Average Time (in seconds) Between Marks
            $stats['mark_average'] = self::getMarkAverage();
            // The Shortest Mark
            $stats['mark_shortest'] = self::getShortestMark();
            // The Longest Mark
            $stats['mark_longest'] = self::getLongestMark();
        }
        // Start Microtime
        $stats['start'] = self::$start;
        // Stop Microtime
        $stats['stop'] = self::$stop ? self::$stop : null;
        // Elapsed Time (in seconds) -- Check comments of self::getElapsed() for more info.
        $stats['elapsed'] = $elapsed;
        return $stats;
    }
    
    /**
     * Dumps Stats, Marks, and Errors then (by default) kills the script.
     *
     * @param bool; if true die() -- else output.
     * @return void;
     */
    public static function dump($die = true) {
        var_dump(array('STATISTICS'=>self::getStats(), 'MARKS'=>self::getMarks(), 'ERRORS'=>self::getErrors()));
        if($die) die();
    }
    
    /**
     * Returns true if any errors occurred.
     *
     * @return bool;
     */
    public static function hasErrors() {
        return count(self::$errors) ? true : false;
    }
    
    /**
     * Get the errors array.
     *
     * @return array;
     */
    public static function getErrors() {
        return self::$errors;
    }
    
    /**
     * Get the errors array.
     *
     * @param string;
     * @return void;
     */
    protected static function logError($error) {
        self::$errors[] = $error;
        error_log(__CLASS__.': '.$error);
    }    
}