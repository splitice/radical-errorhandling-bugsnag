<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\IToCode;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;
use Radical\CLI\Console\Colors;

class BugsnagCLIOutputErrorHandler extends BugsnagErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	
	private $in_error = false;

    function error(ErrorBase $error) {
        if(!$error->isFatal()){
            if($error instanceof PHPError){
                switch($error->getErrno()){
                    case E_COMPILE_WARNING:
                    case E_CORE_WARNING:
                    case E_USER_WARNING:
                    case E_WARNING:
                        \Radical\CLI\Output\Error::Warning($error->getMessage());
                        break;
                    case E_NOTICE:
                    case E_STRICT:
                    case E_USER_NOTICE:
                    case E_USER_DEPRECATED:
                    case E_DEPRECATED:
                        \Radical\CLI\Output\Error::Notice($error->getMessage());
                        break;
                }
            }
        }
        return parent::error($error);
    }

	function exception(ErrorException $error){
		if($this->in_error){
			return;
		}
        parent::exception($error);
		$this->in_error = true;
		
		$c = Colors::getInstance();
		
		//Code
		if($error instanceof IToCode){
			$code = $error->toCode();
		}else{
			if($error->isFatal()){
				$code = $c->getColoredString('FATAL','red');
			}else{
				$code = $c->getColoredString('ERROR','light_red');
			}
		}
		
		//Format Output
		$message = $error->getMessage();
		if($message{0} != '['){
			$message = ' '.$message;
		}
		$output = sprintf(static::CLI_START,$code,$message);
		
		//If Threaded include ThreadID
		/*$T = Thread::current();
		if($T){//If threading
			if($T->parent || count($T->children)){
				$output = '['.$c->getColoredString('#'.$T->getId(),'cyan').']'.$output;
			}
		}*/
		
		//Output it
		\Radical\CLI\Console\Colors::getInstance()->Output($output);
		
		//OB
		if(ob_get_level()) ob_flush();
		

		$this->in_error = false;
	}
}