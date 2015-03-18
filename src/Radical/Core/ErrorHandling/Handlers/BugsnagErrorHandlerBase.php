<?php
/**
 * Created by PhpStorm.
 * User: splitice
 * Date: 16-11-2014
 * Time: 11:09 PM
 */

namespace Radical\Core\ErrorHandling\Handlers;


use Radical\Core\ErrorHandling\Errors\ExceptionError;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;
use Radical\Core\ErrorHandling\Errors\PHPError;

abstract class BugsnagErrorHandlerBase extends ErrorHandlerBase {
    private $base;

    function __construct(BugsnagOutputErrorHandler $base){
        parent::__construct();
        $this->base = $base;
    }

    public function getClient(){
        return $this->base->getClient();
    }

    function exception(ErrorException $exception){
        $client = $this->getClient();
        if($exception instanceof ExceptionError){
            $exception = $exception->getException();
        }
        $client->notifyException($exception);
    }

    function error(ErrorBase $error) {
        $client = $this->getClient();

        if($error instanceof PHPError) {
            $client->errorHandler($error->getErrno(),$error->getErrorText(),$error->getErrorLocation()->file, $error->getErrorLocation()->line);
        }elseif(!$error instanceof ExceptionError){
            $client->notifyError('error', $error->getMessage());
        }

        if($error->isFatal()){
            throw $error;
        }
    }
} 