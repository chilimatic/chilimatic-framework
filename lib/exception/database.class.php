<?php
namespace chilimatic\lib\exception;

use chilimatic\lib\cache\engine\Cache;
use chilimatic\lib\config\Config;

class Exception_Database extends \ErrorException
{

    /**
     * Simple XML tpl
     *
     * @var string
     */
    Const ERROR_LOG_XML = "<error>\n\t<code>#CODE#</code>\n\t<severity>#SEVERITY#</severity>\n\t<message>#MESSAGE#</message>\n\t<datetime>#DATETIME#</datetime>\n\t<file>#FILE#</file>\n\t<line>#LINE#</line>\n\t<backtrace>#BACKTRACE#</backtrace>\n</error>\n";
    
    /**
     * email address from which the fatal errors get sent
     *
     * @var string
     */
    private $fatal_recipient = '';
    
    /**
     * the general subject of the error mail
     *
     * @var string
     */
    private $fatal_subject = 'MYSQL Error';
    
    /**
     * Fatal from name
     *
     * @var string
     */
    private $fatal_from = "From:";
    
    /**
     * error log object
     *
     * @var object
     */
    private $error_log = null;
     
    /**
     * type of error
     *
     * @var string
     */
    protected $error_type = 'database';
    
    /**
     * backtrace [optional needs to be activated in the config]
     *
     * @var string
     */
    public $backtrace = '';


    /**
     * @param null $message
     * @param null $code
     * @param null $severity
     * @param null $filename
     * @param null $lineno
     * @param null $previous
     */
    public function __construct( $message = null, $code = null, $severity = null, $filename = null, $lineno = null, $previous = null )
    {

        if ( empty( $message ) && empty( $code ) && empty( $severity ) )
        {
            error_log( "$message, $code, $severity, $filename, $lineno, $previous" );
        }
        
        $log_extension = 'log';
        
        if ( Config::get( 'mailto' ) )
        {
            $this->fatal_recipient = Config::get( 'mailto' );
        }
        
        if ( Config::get( 'log_type' ) == 'xml' )
        {
            $log_extension = 'xml';
        }
        
        switch ( $code )
        {
            case Database_Mysql::ERR_CONN :
                $file_name = date( 'Y-m-d' ) . "_database_connection_error.$log_extension";
                break;
            case Database_Mysql::ERR_EXEC :
                $file_name = date( 'Y-m-d' ) . "_database_query_error.$log_extension";
                break;
            default :
                $file_name = date( 'Y-m-d' ) . "_database_error.$log_extension";
                break;
        }
        
        // assign properties
        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->filename = $filename;
        $this->lineno = $lineno;
        $this->previous = $previous;
        $mem = memory_get_usage();
        
        $resume = true;
        
        if ( Config::get( 'sql_debug_on' ) !== true && Config::get( 'memcached_server' ))
        {
            $key = md5( $this->message );
            $count = 0;
            
            if ( ( $count = (int) Cache::get( $key ) ) != 0 )
            {
                $resume = false;
            }
            
            $count++;
            
            Cache::set( $key, $count, 300 );
        }
        
        // only trace if its activated and the memory is within the limit
        if ( ( Config::get( 'error_backtrace' ) == 1 || $severity == Database_Mysql::SEVERITY_MAIL ) && $mem < Config::get( 'back_trace_mem_size_limit' ) )
        {
            $this->backtrace = var_export( debug_backtrace(), true );
        }
        
        // only send the email if the resume option is true
        if ( $severity > Database_Mysql::SEVERITY_DEBUG && $resume === true )
        {
            $this->send_error_mail();
        }
        
        switch ( Config::get( 'log_type' ) )
        {
            case 'xml' :
                $log_msg = str_replace( '#MESSAGE#', $this->message, Exception_Database::ERROR_LOG_XML );
                $log_msg = str_replace( '#DATETIME#', date( Log_Error::LOG_DATE_FORMAT ), $log_msg );
                $log_msg = str_replace( '#SEVERITY#', $this->severity, $log_msg );
                $log_msg = str_replace( '#CODE#', $this->code, $log_msg );
                $log_msg = str_replace( '#FILE#', $this->filename, $log_msg );
                $log_msg = str_replace( '#LINE#', $this->lineno, $log_msg );
                $log_msg = str_replace( '#BACKTRACE#', $this->backtrace, $log_msg );
                break;
            case 'json' :
                $log_msg = json_encode( $this );
                break;
            default :
                $log_msg = "\nerror:$this->message\nseverity:$this->severity\ncode:$this->code\nfile:$this->filename\nline:$this->lineno\nbacktrace:$this->backtrace\n---\n";
                break;
        }
        
        // create an error log
        $this->error_log = new Log_Error( $file_name );
        
        // write the error log
        if ( $severity > Database_Mysql::SEVERITY_DEBUG || Config::get( 'sql_debug_on' ) === true )
        {
            $this->error_log->write_log( $log_msg );
        }
    }


    /**
     * sends out the error mail based on the exception
     *
     * @return boolean
     */
    public function send_error_mail()
    {

        if ( Config::get( 'exception_mail' ) == '' ) return false;
        
        $error_msg = (string) "Script Name: {$_SERVER['SCRIPT_NAME']}\n";
        $error_msg .= (string) "Port: {$_SERVER['SERVER_PORT']}\n";
        $error_msg .= (string) "Query String: {$_SERVER['QUERY_STRING']}\n";
        $error_msg .= (string) "Server Name: {$_SERVER['SERVER_NAME']}\n";
        $error_msg .= (string) "Request URI: {$_SERVER['REQUEST_URI']}\n";
        $error_msg .= (string) "Exception Message: $this->message\n";
        $error_msg .= (string) "Remote User: {$_SERVER['REMOTE_ADDR']}\n";
        $error_msg .= (string) "Client: {$_SERVER['HTTP_USER_AGENT']}\n";
        $error_msg .= (string) 'Referer: ' . ( isset( $_SERVER ['HTTP_REFERER'] ) ? $_SERVER ['HTTP_REFERER'] : null ) . "\n";
        $error_msg .= (string) "Request Time: {$_SERVER['REQUEST_TIME']}\n";
        $error_msg .= (string) "debug_backtrace: $this->backtrace\n";
        
        return mail( $this->fatal_recipient, $this->fatal_subject, $error_msg, $this->fatal_from );
    }
}
?>