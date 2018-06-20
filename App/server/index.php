<?php

require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'src/autoload.php';


use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use \Slim\Http\Request;
use \Slim\Http\Response;
use \Slim\App;


//TODO: quitar cuando este en producción
$config = [
    'settings' => [
        'displayErrorDetails' => false,
    ],
];

//Instanciando APP
$app = new App($config);
//Contenedores de controlladores y midd
require_once 'src/settings.php';

//--------- NOTA:
// --Los Middleware se ejecutan antes y después que los controllers
// --Se usa el getBody para escribir en el response sin enviarlo
// --Los Middleware y controllers siempre deben retornar el response
// --Los Middleware reciben un callable referente al siguiente Middleware o controller el cual deben llamar ($next)
// el cual retorna un response para ser manejado desde el midd
// --Para pasar parametros entre Middlewares,se usa:
//      Para enviar: $request = $request->withAttribute('foo', 'bar');
//      Para obtener: $foo = $request->getAttribute('foo');
//--------NOTA:
// --se puede agregar un Middleware global aregandolo directamente a $app y no a un verbo GET, POST, etc.
// --El orden de ejecucion de lod MID es LIFO (pila)
// --Se debe obtener los parametros directamente del $request cuando este es un Middleware,
//  en un controller se recibe un "array" como parametro


//TODO: agregar un status por default directamente en Persistencia para evitar problemas en DB


$app->get('/', function(Request $request, Response $response, $params){
    //TODO: mostrar api
    $response->write("Welcome to the API");
});



//--------------------------
//  GENERAL routes
//--------------------------

$app->post('/mail/send', 'MailController:sendMail')
        ->add('InputMiddleware:checkData_Mail')
        ->add('AuthMiddleware:requireStaff');


//Permite autenticarse (signin)
$app->post('/auth/signin', 'AuthController:signin')
        ->add('InputMiddleware:checkData_Auth');

//Crear un usuario y un estudiante a la vez
$app->post('/auth/signup', 'AuthController:signup')
        ->add('InputMiddleware:checkData_Student') //Es el registro de estudiante
        ->add('InputMiddleware:checkData_User'); //Es el registro de usuario (se ejecuta primero)

//Para verificar usuario y activar
$app->get('/auth/confirm/{token}', 'AuthController:confirm')
        ->add('InputMiddleware:checkParam_Token');

//Para reenviar correo de confirmación
//$app->get('/auth/confirm/send', 'AuthController:sendConfirmEmail');

//--------------------------
//  USER ROUTES
//--------------------------

//Obtiene todos los usuarios
$app->get('/users', 'UserController:getUsers')
        ->add('AuthMiddleware:requireStaff');


//Obtiene todos los usuarios con rol: Mod/Admin
$app->get('/users/staff', 'UserController:getStaffUsers')
        ->add('AuthMiddleware:requireStaff');

//TODO: hacer mas descriptiva
//Obtiene usuarios con un status especifico
//$app->get('/users/status/{status}', 'UserController:getUsersByStatus')
//        ->add('InputMiddleware:checkParam_Status');

//busca usuarios por correo (coincidencias)
//$app->get('/users/search/{email}', 'UserController:searchUsersByEmail')
//        ->add('InputMiddleware:checkParam_Email')
//        ->add('AuthMiddleware:requireStaff');

//busca usuarios por correo (coincidencias)
$app->get('/users/search/{email}/staff', 'UserController:searchStaffUsersByEmail')
        ->add('InputMiddleware:checkParam_Email')
        ->add('AuthMiddleware:requireStaff');


//TODO: obtener por rol

//Obtiene usuario por ID
$app->get('/users/{id}', 'UserController:getUser_ById')
        ->add('InputMiddleware:checkParam_Id')
        ->add('AuthMiddleware:requireBasic');

//Obtener estudiante por id de usuario
$app->get('/users/{id}/student', 'UserController:getStudent_ByUserId')
        ->add('InputMiddleware:checkParam_Id')
        ->add('AuthMiddleware:requireBasic');


//Crear un usuario simple
$app->post('/users', 'UserController:createUser')
        ->add('InputMiddleware:checkData_User') //Es el registro de estudiante
        ->add('InputMiddleware:checkData_Role'); //Es el registro de estudiante


//Crear un usuario y un estudiante a la vez
$app->post('/users/student', 'UserController:createUserAndStudent')
    ->add('InputMiddleware:checkData_Student') //Es el registro de estudiante
    ->add('InputMiddleware:checkData_User'); //Es el registro de usuario (se ejecuta primero)

//TODO: ruta para confirmar usuario---> GET: user/confirm/{token}

//Actualiza datos de usuario
$app->put('/users/{id}', 'UserController:updateUser')
        ->add('InputMiddleware:checkData_User')
        ->add('InputMiddleware:checkData_Role')
        ->add('InputMiddleware:checkParam_Id');

//Cambia estado de usuario
$app->patch('/users/{id}/status/{status}', 'UserController:changeStatusUser')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_Id');

//Elimina a un usuario
$app->delete('/users/{id}', 'UserController:deleteUser')
        ->add('InputMiddleware:checkParam_Id');

//--------------------------
//  CAREER ROUTES
//--------------------------

//Obtiene carreras
$app->get('/careers', 'CareerController:getCareers');

//Obtiene carrera por Id
$app->get('/careers/{id}', 'CareerController:getCareer_ById')
        ->add('InputMiddleware:checkParam_Id');

//TODO: obtener materias por carrera

//Crear carrera
$app->post('/careers', 'CareerController:createCareer')
        ->add('InputMiddleware:checkData_Career');

//Cambia estado de carrera
$app->patch('/careers/{id}/status/{status}', 'CareerController:changeStatus')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_Id');

//Actualiza carrera
$app->put('/careers/{id}', 'CareerController:updateCareer')
        ->add('InputMiddleware:checkData_Career')
        ->add('InputMiddleware:checkParam_Id');

//Elimina carrera
$app->delete('/careers/{id}', 'CareerController:deleteCareer')
        ->add('InputMiddleware:checkParam_Id');


//--------------------------
//  PLAN ROUTES
//--------------------------

//Obtiene planes
$app->get('/plans', 'PlanController:getPlans');

//Obtiene plan por id
$app->get('/plans/{id}', 'PlanController:getPlan_ById')
        ->add('InputMiddleware:checkParam_Id');

//TODO: obtener materias por plan

//Crea un plan
$app->post('/plans', 'PlanController:createPlan')
        ->add('InputMiddleware:checkData_Plan');

//TODO: hacer mas descriptivo
//cambia status de un plan
$app->patch('/plans/{id}/status/{status}', 'PlanController:changeStatus')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_Id');


//Actualiza plan
$app->put('/plans/{id}', 'PlanController:updatePlan')
        ->add('InputMiddleware:checkData_Plan')
        ->add('InputMiddleware:checkParam_Id');

//elimina plan
$app->delete('/plans/{id}', 'PlanController:deletePlan')
        ->add('InputMiddleware:checkParam_Id');

//--------------------------
//  SUBJECT ROUTES
//--------------------------

//Obtiene materias
$app->get('/subjects', 'SubjectController:getSubjects');

$app->get('/subjects/enabled', 'SubjectController:getEnabledSubjects');

//Obtiene materias
$app->get('/subjects/carrera/{career}/semestre/{semester}/plan/{plan}', 'SubjectController:getSubject_Search');

//TODO: agregar ruta directo de career --> /career/{id}/subject/{id}

//Obtiene materia por id
$app->get('/subjects/{id}', 'SubjectController:getSubject_ById')
        ->add('InputMiddleware:checkParam_Id');

        //Obtiene materias por nombre
$app->get('/subjects/search/{name}', 'SubjectController:searchSubjects_ByName');

//crear materia
$app->post('/subjects', 'SubjectController:createSubject')
        ->add('InputMiddleware:checkData_Subject');


//cambia estado de materia
$app->patch('/subjects/{id}/status/{status}', 'SubjectController:changeStatus')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_id');

//Actualiza materia
$app->put('/subjects/{id}', 'SubjectController:updateSubject')
        ->add('InputMiddleware:checkData_Subject')
        ->add('InputMiddleware:checkParam_id');

//Elimina materia
$app->delete('/subjects/{id}', 'SubjectController:deleteSubject')
        ->add('InputMiddleware:checkParam_id');


//--------------------------
//  STUDENT ROUTES
//--------------------------

//Obtiene estudiantes
$app->get('/students', 'StudentController:getStudents');

//obtiene estudiante por id
$app->get('/students/{id}', 'StudentController:getStudent_ById')
    ->add('InputMiddleware:checkParam_Id');

//Busca estudiante por coincidencias (todos los campos de string: nombre, apellido, correo, telefono)
$app->get('/students/search/{search_student}', 'StudentController:searchStudents')
    ->add('InputMiddleware:checkParam_search_student');

//TODO: Obtener correos tipo gmail para agregar y enviarles correos

//Actualiza datos de usuario
//TODO: update avatar, facebook, etc.
$app->put('/students/{id}', 'StudentController:updateStudent')
    ->add('InputMiddleware:checkData_Student')
    ->add('InputMiddleware:checkParam_Id');


//--------------------------
//  PERIOD ROUTES
//--------------------------

//Obtiene periodos
$app->get('/periods', 'PeriodController:getPeriods');

//TODO: Obtener asesorias de un periodo

//TODO: periodo activo
$app->get('/periods/current', 'PeriodController:getCurrentPeriod');

//Obtiene periodo por id
$app->get('/periods/{id}', 'PeriodController:getPeriod_ById')
    ->add('InputMiddleware:checkParam_id');

//Crea periodo
$app->post('/periods', 'PeriodController:createPeriod')
    ->add('InputMiddleware:checkData_Period');

//actualiza periodo
$app->put('/periods/{id}', 'PeriodController:updatePeriod')
    ->add('InputMiddleware:checkData_Period')
    ->add('InputMiddleware:checkParam_id');

//cambia status de periodo
$app->patch('/periods/{id}/status/{status}', 'PeriodController:changeStatus')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_id');

//elimina periodo
$app->delete('/periods/{id}', 'PeriodController:deletePeriod')
        ->add('InputMiddleware:checkParam_id');


//--------------------------
//  SCHEDULE, HOURS AND DAYS ROUTES
//--------------------------

//Obtiene horas y dias disponibles para utilizar
$app->get('/schedule/source', 'ScheduleController:getHoursAndDays');


//obtiene horario por id
$app->get('/schedule/{id}', 'ScheduleController:getSchedule_ById')
        ->add('InputMiddleware:checkParam_id');

$app->get('/schedule/adviser/{adviser}/alumn/{alumn}/match', 'ScheduleController:getScheduleMatchHours_ByStudents');
//    ->add('InputMiddleware:checkParam_Id')
//  ;

//Obtiene todos los asesores activos del periodo actual
//$app->get('/schedule/advisers', 'ScheduleController:getCurrentAdvisers')
//    ->add('InputMiddleware:checkParam_id')
//  ;


//obtiene asesores disponibles (activos) por materia
$app->get('/subject/{id}/advisers', 'SubjectController:getAdvisers_BySubject_IgnoreStudent')
    ->add('InputMiddleware:checkParam_id');

//TODO: obtener asesores disponibles de dicha por materia
//TODO: obtener horario de Asesor y comparar con Solicitante


//Cambia estado de horario
$app->patch('/schedule/{id}/status/{status}', 'ScheduleController:changeScheduleStatus')
        ->add('InputMiddleware:checkParam_Status')
        ->add('InputMiddleware:checkParam_Id');


//-------------------ESTUDIANTE

//Obtiene horario actual de estudiante
$app->get('/students/{id}/schedule', 'StudentController:getSchedule_ByStudentId')
    ->add('InputMiddleware:checkParam_Id');


//crea horario (horas)
$app->post('/students/{id}/schedule', 'StudentController:createSchedule')
        ->add('InputMiddleware:checkParam_Id');


//TODO: obtener materias de horario
//TODO: deshabilitar para ser asesor

//actualiza horas de horario
$app->put('/schedule/{id}/hours', 'ScheduleController:updateScheduleHours')
        ->add('InputMiddleware:checkData_schedule_hours')
        ->add('InputMiddleware:checkParam_Id');


//agrega materias a horario
//$app->post('/schedule/{id}/subjects', 'ScheduleController:addScheduleSubjects')
//        ->add('InputMiddleware:checkData_schedule_subjects')
//        ->add('InputMiddleware:checkParam_Id')
//      ;

//actualiza materias de horario
$app->put('/schedule/{id}/subjects', 'ScheduleController:updateScheduleSubjects')
        ->add('InputMiddleware:checkData_schedule_subjects')
        ->add('InputMiddleware:checkParam_Id');


//TODO: validacion de materias de horario (admin)
//TODO: agregar: deshabilitar/habilitar horario
//TODO: agregar: eliminar (admin)


//--------------------------
//  ADVISORY ROUTES
//--------------------------
//TODO: debe obtenerse solo materias validadas por admin (status = 2)
//TODO: no debe poder solictarse la misma materia mientras una asesoria este activa con la misma
//TODO: Al final del periodo, debe finalizarse todas las asesorias activas


//Obtiene asesorias actuales
$app->get('/advisories', 'AdvisoryController:getAdvisories');


//Obtiene asesorias de estudiante
$app->get('/student/{id}/advisories', 'StudentController:getCurrentAdvisories_ByStudentId')
    ->add('InputMiddleware:checkParam_Id');

//Obtiene asesorias de estudiante donde es alumno
$app->get('/student/{id}/advisories/requested', 'StudentController:getCurrentAdvisories_Requested')
    ->add('InputMiddleware:checkParam_Id');

//Obtiene asesorias de estudiante donde es alumno
$app->get('/student/{id}/advisories/adviser', 'StudentController:getCurrentAdvisories_Adviser')
    ->add('InputMiddleware:checkParam_Id');

//Obtiene horas de asesoria asignada
$app->get('/advisories/{id}/hours', 'AdvisoryController:getAdvisoryHours_ById')
    ->add('InputMiddleware:checkParam_Id');


//--------------ADVISORIES
//Obtiene asesoria por id
$app->get('/advisories/{id}', 'AdvisoryController:getAdvisory_ById')
        ->add('InputMiddleware:checkParam_Id');

$app->get('/subjects/{id}/advisers/ignore/{student}', 'SubjectController:getAdvisers_BySubject_IgnoreStudent')
    ->add('InputMiddleware:checkParam_Id');


//crear asesoria por estudiante
$app->post('/students/{id}/advisories', 'AdvisoryController:createStudentAdvisory')
        ->add('InputMiddleware:checkData_advisory')
        ->add('InputMiddleware:checkParam_Id');


$app->get('/advisories/{id}/schedule', 'AdvisoryController:getAdvisorySchedule')
    ->add('InputMiddleware:checkParam_Id');


$app->patch('/advisories/{id}/finalize', 'AdvisoryController:finalizeAdvisory')
    ->add('InputMiddleware:checkParam_Id');


$app->post('/advisories/{id}/assign', 'AdvisoryController:assignAdviser')
    ->add('InputMiddleware:checkData_advisory_schedule')
    ->add('InputMiddleware:checkParam_Id');


//$app->put('/advisories/{id}/hours', 'AdvisoryController:updateAdvisoryHours');

//$app->patch('/advisories/{id}/status/{status}', 'AdvisoryController:changeStatus');




try{
    $app->run();
} catch (MethodNotAllowedException $e) {
//    throw new InternalErrorException("Index", "Slim error",  $e->getMessage());
} catch (NotFoundException $e) {
//    throw new InternalErrorException("Index", "Slim error",  $e->getMessage());
} catch (\Exception $e) {
//    throw new InternalErrorException("Index", "Slim error",  $e->getMessage());
}