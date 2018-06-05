<?php

$container = $app->getContainer();

//----------------
// CORS
//----------------
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");


//-----------------------
// MONOLOG
//-----------------------
//https://www.projek.xyz/slim-monolog/
//https://akrabat.com/logging-errors-in-slim-3/
//https://www.slimframework.com/docs/v3/tutorial/first-app.html


//$container['errorHandler'] = function($c) {
//    return function ($request, $response, $exception) use ($c){
//        return $c['response']->withStatus(500)
//            ->withHeader('Content-Type', 'text/html')
//            ->write('Something went wrong!');
//           throw new \App\Exceptions\InternalErrorException("SLIM", "Ocurrio un error", $exception);
//    };
//};


//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found');
    };
};


//-----------------------
//Middelware methods
//-----------------------
$container['InputMiddelware'] = function($c){
    return new App\Middelware\InputParamsMiddelware();
};

//-----------------------
//Controllers methods
//-----------------------
$container['MailController'] = function($c){
    return new App\Controller\MailController();
};

$container['UserController'] = function($c){
    return new App\Controller\UserController();
};
$container['StudentController'] = function($c){
    return new App\Controller\StudentController();
};
$container['CareerController'] = function($c){
    return new App\Controller\CareerController();
};
$container['PlanController'] = function($c){
    return new App\Controller\PlanController();
};
$container['SubjectController'] = function($c){
    return new App\Controller\SubjectController();
};
$container['PeriodController'] = function($c){
    return new App\Controller\PeriodController();
};
$container['ScheduleController'] = function($c){
    return new App\Controller\ScheduleController();
};
$container['AdvisoryController'] = function($c){
    return new App\Controller\AdvisoryController();
};