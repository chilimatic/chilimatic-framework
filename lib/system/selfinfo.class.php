<?php
class System_SelfInfo
{


    /**
     * $pid process id
     *
     * @var int
     */
    public $pid = 0;


    /**
     * group id
     *
     * @var int
     */
    public $gid = 0;


    /**
     * inode
     *
     * @var int
     */
    public $inode = 0;


    /**
     * user_id
     *
     * @var int
     */
    public $uid = 0;


    /**
     * current memory usage
     *
     * @var int
     */
    public $current_mem_use = 0;


    /**
     * current memory peak
     *
     * @var int
     */
    public $current_mem_peak = 0;


    /**
     * memory usage in MB
     *
     * @var float
     */
    public $current_mem_use_mb = 0;


    /**
     * memory peak in MB
     *
     * @var float
     */
    public $current_mem_peak_mb = 0;


    /**
     * username
     *
     * @var string
     */
    public $user = null;


    /**
     * uname data
     *
     * @var array
     */
    public $uname = null;


    /**
     * cpu info linux
     * tail -n25 /proc/cpuinfo
     * 
     * @var array
     */
    public $cpu_info = null;


    /**
     * system memory statisticis linux
     * cat /proc/meminfo
     *
     * @var array
     */
    public $system_memory_kb = null;


    /**
     * constructor
     *
     * @return \System_SelfInfo
     */
    public function __construct()
    {
        $this->refresh_info();
        $this->process_memory_info();
        $this->cpu_info();
        $this->system_memory_info();
    }


    /**
     * refresh the basic info
     *
     * @return boolean
     */
    protected function refresh_info()
    {

        $this->pid = (int) getmypid();
        $this->gid = (int) getmygid();
        $this->inode = (int) getmyinode();
        $this->uid = (int) getmyuid();
        $this->user = (string) get_current_user();
        $this->uname = posix_uname();
        return true;
    }


    /**
     * Collect Cpu info
     *
     * @return array
     */
    protected function cpu_info()
    {

        $this->cpu_info = array();
        if ( !stristr(PHP_OS, 'win') )
        {
            exec("cat /proc/cpuinfo", $output);
            foreach ( $output as $line )
            {
                $parts = explode(':', $line);
                
                if ( empty($parts[0]) ) continue;
                
                $this->cpu_info[trim($parts[0])] = (!empty($parts[1])) ? trim($parts[1]) : null;
            }
        }
        
        return true;
    }

    /**
     * memory info for a linux system
     *
     * @return bool
     */
    protected function system_memory_info()
    {

        $this->system_memory_kb = array();
        
        exec("cat /proc/meminfo", $output);
        foreach ( $output as $line )
        {
            $parts = explode(':', $line);
            
            if ( empty($parts[0]) ) continue;
            // remove the KB sign and reduce it to a key value array
            $this->system_memory_kb[trim($parts[0])] = (int) (!empty($parts[1]) ? trim(str_replace('kB', '', $parts[1])) : null);
        }
        
        return true;
    }


    /**
     * get memory infos
     *
     * @return boolean
     */
    protected function process_memory_info()
    {

        $this->current_mem_use = (int) memory_get_usage();
        $this->current_mem_peak = (int) memory_get_peak_usage();
        $this->current_mem_peak_mb = (float) $this->current_mem_peak / 1024 / 1024;
        $this->current_mem_use_mb = (float) $this->current_mem_use / 1024 / 1024;
        
        return true;
    }
}