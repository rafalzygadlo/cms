<?php

/** 
 * Logging class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class Log
{
    // declare log file and file pointer as private properties
    private $log_file;
    private $fp;
    
    // set log file (path and name)
    public function file($path)
    {
        $this->log_file = $path;
    }
    // write message to the log file
    public function write($message)
    {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp))
        {
            $this->open();
        }
        // define script name
//        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time $message" . PHP_EOL);
    }
    
    // close log file (it's always a good idea to close a file when you're done with it)
    public function close()
    {
        fclose($this->fp);
    }
    
    // open log file (private method)
    private function open()
    {
      
        $log_file_default = '/tmp/logfile.txt';
        
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}


?>