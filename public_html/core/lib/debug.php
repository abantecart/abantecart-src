<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ADebug {
	static public $checkpoints = array();
	static public $queries = array();
	static public $queries_time = 0;
    static private $debug = 0; //off
    static private $debug_level = 0; //only exceptions
    static private $_is_init = false; //class init
    static private $_is_error = false;
	
	static private function isActive()
    {
        if ( !self::$_is_init ) {
            if ( defined('INSTALL') ) {
                self::$debug = 1;
                self::$debug_level = 1;
            } else  if (class_exists('Registry') ) {
                $registry = Registry::getInstance();
                if ( $registry->has('config') ) {

                    if ( $registry->get('config')->has('config_debug') )
                        self::$debug = $registry->get('config')->get('config_debug');

                    if ( $registry->get('config')->has('config_debug_level') )
                        self::$debug_level = $registry->get('config')->get('config_debug_level');

                }
            }
            self::$_is_init = true;
        }

        return self::$debug;		
    }

    static function set_query($query, $time , $backtrace)
	{
		if ( !self::isActive() ) {
            return false;
        }
		self::$queries[] = array(
            'sql'  => $query,
            'time' => sprintf('%01.5f', $time),
            'file' => $backtrace['file'],
            'line' => $backtrace['line']
        );
        self::$queries_time += $time;
	}


    static function checkpoint($name)
	{
		if ( !self::isActive() ) {
            return false;
        }

        $e = new Exception();
		self::$checkpoints[] = array(
			'name' => $name,
			'time' => self::microtime(),
			'memory' => memory_get_usage(),
			'included_files' => count(get_included_files()),
			'queries' => count(self::$queries),
            'type' => 'checkpoint',
            'trace' => $e->getTraceAsString(),
		);
	}

    static function variable($name, $variable)
	{
		if ( !self::isActive() ) {
            return false;
        }

        ob_start();
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
        $msg = ob_get_clean();

        self::$checkpoints[] = array(
            'name' => $name,
			'msg' => $msg,
            'type' => 'variable',
		);
	}

    static function error($name, $code, $msg)
	{

        self::$checkpoints[] = array(
			'name' => $name,
            'time' => self::microtime(),
			'memory' => memory_get_usage(),
			'included_files' => count(get_included_files()),
			'queries' => count(self::$queries),
            'msg' => $msg,
            'code' => $code,
            'type' => 'error',
		);
        self::$_is_error = true;
	}

    static function warning($name, $code, $msg)
	{

        self::$checkpoints[] = array(
			'name' => $name,
            'time' => self::microtime(),
			'memory' => memory_get_usage(),
			'included_files' => count(get_included_files()),
			'queries' => count(self::$queries),
            'msg' => $msg,
            'code' => $code,
            'type' => 'warning',
		);
        self::$_is_error = true;
	}

	static function microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

    static function display_queries( $start, $end )
    {
        
        if ( $end - $start <= 0 ) return null;

        echo '<table class="mysql" cellpadding=5>
            <tr>
                <td><b>Time</b></td>
                <td><b>File</b></td>
                <td><b>Line</b></td>
                <td><b>SQL</b></td>
            </tr>';
        for ($i=$start; $i<$end; $i++ ) {
            $key = $i;
            $query = self::$queries[$key];

            echo '<tr valign="top" ' . ($key % 2 ? 'class="even"' : '').'>
                       <td><b>' . $query['time'] . '</b></td>
                       <td>' . $query['file'] . '</td>
                       <td>' . $query['line'] . '</td>
                       <td>' . $query['sql'] . '</td>
                  </tr>';
        }
        echo '</table>';
    }

    static function display_errors()
    {
        if ( !self::$_is_error ) return null;
        echo '<table class="debug_info" cellpadding=5>
                    <tr>
                        <td><b>Name</b></td>
                        <td><b>Info</b></td>
                    </tr>';

        $show = array('error', 'warning');
        foreach (self::$checkpoints as $c) {
            if ( !in_array($c['type'], $show) ) continue;
            echo '<tr valign="top" class="debug_'.$c['type'].'"><td><b>' . $c['code'].'::'.$c['name'] . '</b><br /></td>';
            echo '<td>'. $c['msg'] . '<br /></td>';
        }
        echo '</table>';
    }

	static function display()
	{
        if ( !self::isActive() ) {
            return false;
        }
		$previous = array();
		$cummulative = array();

		$first = true;

        ob_start();

        switch ( self::$debug_level ) {
            
            case 0 :
                //show only exceptions
                //shown in Abc_Exception::displayError
                break;
            case 1 :
                //show errors and warnings
                self::display_errors();
                break;
            case 2 :
                // #1 + mysql site load, php file execution time and page elements load time
                self::display_errors();

                //count php execution time
                foreach (self::$checkpoints as $name => $c) {
                    if ( $c['type'] != 'checkpoint' ) continue;
                    if ($first == true) {
                        $first = false;
                        $cummulative = $c;
                    }
                    $time = sprintf("%01.4f", $c['time'] - $cummulative['time']);
                }
                echo '<div class="debug_info">';
                echo 'Queries - '. count(self::$queries). '<br />';
                echo 'Queries execution time - '. sprintf('%01.5f', self::$queries_time). '<br />';
                echo 'PHP Execution time - '. $time. '<br />';
                echo '</div>';
                break;

            case 3 :
            case 4 :
            case 5 :
                // #2 + basic logs and stack of execution
                // #3 + dump mysql statements
                // #4 + call stack
                echo '<table class="debug_info" cellpadding=5>
                    <tr>
                        <td><b>Name</b></td>
                        <td><b>Info</b></td>
                    </tr>';

                foreach (self::$checkpoints as $c) {
                    echo '<tr valign="top" class="debug_'.$c['type'].'" ><td><b>' . $c['name'] . '</b><br /></td>';
                    echo '<td>';
                    if ( $first == true && $c['type'] != 'variable' ) {
                        $previous = array(
                            'time' => $c['time'],
                            'memory' => 0,
                            'included_files' => 0,
                            'queries' => 0,
                        );
                        $first = false;
                        $cummulative = $c;
                    }

                    switch ($c['type'] ) {
                        case 'variable':
                            echo $c['msg'].'<br />';
                            break;
                        case 'error':
                        case 'warning':
                            echo $c['msg'].'<br />';
                        case 'checkpoint':
                            echo '- Memory: ' . (number_format($c['memory'] - $previous['memory'])) . ' (' . number_format($c['memory']) . ')' . '<br />';
                            echo '- Files: ' . ($c['included_files'] - $previous['included_files']) . ' (' . $c['included_files'] . ')' . '<br />';
                            echo '- Queries: ' . ($c['queries'] - $previous['queries']) . ' (' . $c['queries'] . ')' . '<br />';
                            echo '- Time: ' . sprintf("%01.4f", $c['time'] - $previous['time']) . ' (' . sprintf("%01.4f", $c['time'] - $cummulative['time']) . ')' . '<br />';
                            if ( self::$debug_level > 3 ) {
                                self::display_queries( $previous['queries'], $c['queries'] );
                            }
                            if ( self::$debug_level > 4 ) {
                                echo '<pre>'.$c['trace'].'</pre>';
                            }
                            $previous = $c;
                            break;
                    }

                    echo '</td></tr>';
                }
                echo '</table>';

                break;

            default:
                
        }

        $debug = ob_get_clean();
        switch ( self::$debug ) {
            case 1:
                //show
                echo $debug;
                break;
            case 2:
                //log
                require_once(DIR_CORE . 'lib/log.php');
                $registry = Registry::getInstance();
                if ( $registry->has('log') ) {
                $log = $registry->get('log');
                } else {
                    $log = new ALog(DIR_LOGS.'error.txt');
                }
                $log->write( strip_tags(str_replace('<br />', "\r\n", $debug)));
                break;
            default:
        }
		
	}

}