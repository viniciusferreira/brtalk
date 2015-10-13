<?php
/*
 * @author  Hédi Carlos Minin
 * @email   hedicarlos@gmail.com
 * 
 * @author  André da Silva Severino
 * @email   andre@andrewd.com.br
 * 
 * @url     http://andrewebdeveloper.github.io/brtalk/
 * 
 * @version 1.0
 */
class PDOConnection extends PDO 
{
    private $host = 'localhost';
    private $database = 'bt_brtalk';
    private $user = 'root';
    private $password = 'root';
    private $charset = 'utf8';
    private $persistent = false;

    public function __construct() 
    {
        set_exception_handler(array(__CLASS__, 'exception_handler'));

        $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->charset, 
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                    PDO::ATTR_PERSISTENT => $this->persistent
                );

        parent::__construct('mysql:host='.$this->host.';dbname='.$this->database.';charset='.$this->charset, $this->user, $this->password,  $options);

        restore_exception_handler();
    }

    public static function getInstance() 
    {
        static $instance = NULL;

        if($instance == NULL)
            $instance = new PDOConnection;

        return $instance;
    }


    public static function exception_handler($exception) 
    {
        die('Uncaught exception: '. $exception->getMessage());
    }
	
}

?>