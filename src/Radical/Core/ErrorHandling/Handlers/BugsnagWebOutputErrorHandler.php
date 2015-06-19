<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;
use Radical\Web\Page\Controller\Error;
use Radical\Web\Page\Handler\PageRequest;

class BugsnagWebOutputErrorHandler extends BugsnagErrorHandlerBase {
	function exception(ErrorException $error){
        parent::exception($error);
		if(ob_get_level()) ob_end_clean();
		try {
			\Radical\Web\Page\Handler::init();
			\Radical\Web\Page\Handler::$stack->push(new PageRequest(null));
			//@todo Remove ugly hack
			$page = new Error($error);
			while($page){
				$page = $page->GET();
			}
			\Radical\Web\Page\Handler::current(true)->headers->output();
		}catch(\Exception $ex){
			die('Error: '.$ex->getMessage());
		}
		exit;
	}
}