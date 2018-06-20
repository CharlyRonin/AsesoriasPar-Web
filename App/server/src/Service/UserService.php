<?php namespace App\Service;


use App\Auth;
use App\Exceptions\ConflictException;
use App\Exceptions\InternalErrorException;
use App\Exceptions\NoContentException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RequestException;
use App\Model\StudentModel;
use App\Persistence\StudentsPersistence;
use App\Persistence\UsersPersistence;
use App\Model\UserModel;
use App\Utils;

class UserService{

    private $userPer;

    public function __construct(){
        $this->userPer = new UsersPersistence();
    }


    /**
     * Obtiene todos los usuarios registrados
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getUsers(){
        $result = $this->userPer->getUsers();

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getUsers", "Ocurrio un error al obtener usuarios", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No hay usuarios");
        else
            return $result->getData();
    }




    /**
     * @return mixed
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getStaffUsers()
    {
        $result = $this->userPer->getStaffUsers();

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getStaffUsers",
                "Ocurrio un error al obtener usuarios mod/admin", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No hay usuarios");
        else
            return $result->getData();
    }



    /**
     * @param $id
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NotFoundException
     * @throws \App\Exceptions\UnauthorizedException
     */
    public function getUser_ById($id){
        //Dependiendo de Rol, se obtiene cierta info
        $result = null;
        if( Auth::isStaffUser() )
            $result = $this->userPer->getUser_ById( $id );
        else
            $result = $this->userPer->getEnabledBasicUser_ById( $id );



        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getUserById","Ocurrió un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NotFoundException("No se encontró usuario");
        else
            return $result->getData()[0];
    }

    /**
     * Obtiene usuario por id de estudiante
     *
     * @param $student_id int
     *
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function getUser_ByStudentId($student_id){
        $result = $this->userPer->getUser_ByStudentId($student_id);

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getUser_ByStudentid", "Ocurrio un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NotFoundException("No existe usuario asociado");
        else
            return $result->getData()[0];
    }


    /**
     * @param $id
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NotFoundException
     * @throws \App\Exceptions\UnauthorizedException
     */
    public function getStudent_ByUserId($id){

        //Comprueba que exista usuario
        $this->getUser_ById($id);

        //Obtiene estudiante
        $studentPer = new StudentsPersistence();
        //Dependiendo de Rol, se obtiene cierta info
        $result = null;
        if( Auth::isStaffUser() )
            $result = $studentPer->getStudent_ByUserId( $id );
        else
            $result = $studentPer->getStudent_ByEnabledBasicUserId( $id );


        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getStudent_ByUser",
                "Ocurrio un error al obtener estudiante", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NotFoundException("No se encontro estudiante");
        else
            return $result->getData()[0];
    }

    /**
     * @throws InternalErrorException
     * @throws NoContentException
     * @return \mysqli_result
     */
    public function getLastUser(){
        $result = $this->userPer->getUser_Last();

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getLastUser", "Ocurrio un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No hay usuarios");
        else
            return $result->getData();
    }






    /**
     * @param $id
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getRoleUser($id){

        $result = $this->userPer->getRoleUser( $id );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getRoleUser", "Ocurrio un error al obtener rol de usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se obtuvo rol");
        else
            return $result->getData();
    }


    /**
     * @param $email
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getUser_ByEmail($email){
        $result = $this->userPer->getUser_ByEmail( $email );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("getUserByEmail","Ocurrio un error al obtener usuario por email", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se encontro usuario");
        else
            return $result->getData();
    }


    /**
     * @param $email
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function searchUserByEmail($email)
    {
        $result = $this->userPer->searchUsers_ByEmail( $email );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("searchUsersByEmail","Ocurrio un error al obtener usuarios por email", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se encontraron usuarios");

        return $result->getData();
    }

    /**
     * @param $email
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function searchStaffUser_ByEmail($email)
    {
        $result = $this->userPer->searchStaffUsers_ByEmail( $email );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException("searchStaffUserByEmail",
                "Ocurrio un error al obtener usuarios por email", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se encontraron usuarios");

        return $result->getData();
    }


    private function isEmailUsed($email){
        $result = $this->userPer->getUser_ByEmail( $email );

        if( Utils::isSuccessWithResult($result->getOperation()) )
            $result->setOperation(true);
        else if( Utils::isEmpty($result->getOperation()) )
            $result->setOperation(false);

        return $result;
    }


    public function isRoleExists($role){
        $result = $this->userPer->getRole_ByName( $role );

        if( Utils::isSuccessWithResult($result->getOperation()) )
            $result->setOperation(true);
        else if( Utils::isEmpty($result->getOperation()) )
            $result->setOperation(false);

        return $result;
    }

    /**
     * @param $status
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getUsersByStatus($status)
    {
        if( $status == Utils::$STATUS_ENABLE ){
            $result = $this->userPer->getEnableUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException("getUsersByStatus","Ocurrio un error al obtener usuarios habilitados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }
        else if( $status == Utils::$STATUS_DISABLE ){
            $result = $this->userPer->getDisabledUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException("getUsersByStatus","Ocurrio un error al obtener usuarios deshabilitados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }
        else{
            $result = $this->userPer->getNoConfirmUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException("getUsersByStatus","Ocurrio un error al obtener usuarios no confirmados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }


    }

    //------------------REGISTRAR USUARIO


    /**
     * @param $user UserModel
     *
     * @throws ConflictException
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function insertUser($user){

        //TODO: debe enviar correo para que el usuario sea confirmado
        //TODO: cron para eliminar usuario si este no se confirma en una semana
        //TODO: puede solicitarse un correo para confirmar

        //Verifica que email no exista
        $result = $this->isEmailUsed( $user->getEmail() );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "insertUser","Ocurrio un error al verificar email de usuario", $result->getErrorMessage());
        else if( $result->getOperation() == true )
            throw new ConflictException( "Email ya existe" );

        //Se verifica rol
        $result = $this->isRoleExists( $user->getRole() );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "insertUser","Ocurrio un error al verificar rol", $result->getErrorMessage());
        else if( $result->getOperation() == false )
            throw new NotFoundException( "No existe rol asignado" );

        //Se registra usuario
        $result = $this->userPer->insertUser( $user );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "insertUser","Ocurrio un error al registrar usuario", $result->getErrorMessage());
    }


    //------------------REGISTRAR USUARIO Y ESTUDIANTE

    /**
     * @param $student StudentModel
     *
     * @throws InternalErrorException
     * @throws RequestException
     */
    public function insertUserAndStudent($student){
        //Inicia transaccion
        $trans = UsersPersistence::initTransaction();
        if( !$trans )
            throw new InternalErrorException("insertUserAndStudent","Error al iniciar transaccion");


        //------------Verificacion de datos de usuario (excepciones incluidas)
        try {
            //Registramos usuario
            $this->insertUser( $student->getUser() );
            //Obtenemos ultimo registrado
            $result = $this->getLastUser();
            $user = self::makeUserModel( $result[0] );
            //Se agrega al Modelo de estudiante
            $student->setUser( $user );

        } catch (RequestException $e) {
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }


        //--------------CARRERA

        try {
            $careerService = new CareerService();
            $result = $careerService->getCareer_ById( $student->getCareer() );
            $career = CareerService::makeObject_career( $result[0] );
            //Se asigna carrera (model) a student
            $student->setCareer( $career );

        } catch (RequestException $e) {
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }

        //------------Iniciamos registro de estudiante
        try{
            $studentService = new StudentService();
            $studentService->insertStudent( $student );
        }catch (RequestException $e){
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }

        //Si marcha bien, se registra commit
        $trans = UsersPersistence::commitTransaction();
        if( !$trans )
            throw new InternalErrorException("insertUserAndStudent","Error al realizar commit de transaccion");

        //Envia correo de confirmacion
        try{
            $mailServ = new MailService();
            $mailServ->sendConfirmEmail(  $user );
            $staff = $this->getStaffUsers();
            //Se envia a staff
            $mailServ->sendEmailToStaff( "Nuevo estudiante", "Se ha registrado un nuevo estudiante: ".$student->getFirstName()." ".$student->getLastName(), $staff );
        }catch (RequestException $e){}
    }



    /**
     * @param $user UserModel
     *
     * @throws ConflictException
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function updateUser($user){
        $result = $this->userPer->getUser_ById( $user->getId() );

        //TODO: Cuando se haga update del correo, debe cambiarse status para confirmar
        //TODO: no debe eliminarse usuario con cron
//
//        //Verificacion de usuario
//        if( Utils::isError( $result->getOperation() ) )
//            throw new InternalErrorException( "updateUser","Ocurrio un error al obtener usuario");
//        else if( Utils::isEmpty( $result->getOperation() ) )
//            throw new NotFoundException("No existe usuario");

        //Verifica que email
        $user_db = self::makeUserModel( $result->getData()[0] );

        //Si cambio el email
        if( $user_db !== $user->getEmail() ){
            //Se obtiene
            $result = $this->isEmailUsed( $user->getEmail() );
            //Operacion
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( "updateUser","Ocurrio un error al verificar email de usuario", $result->getErrorMessage());
            else if( $result->getOperation() == true )
                throw new ConflictException( "Email ya existe" );
        }


        //Si cambio el rol
        if( $user_db !== $user->getRole() ){
            //Se verifica rol
            $result = $this->isRoleExists( $user->getRole() );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( "updateUser","Ocurrio un error al verificar rol", $result->getErrorMessage());
            else if( $result->getOperation() == false )
                throw new NotFoundException( "No existe rol asignado" );
        }


        //Se actualiza usuario
        $result = $this->userPer->updateUser( $user );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "updateUser","Ocurrio un error al actualizar usuario", $result->getErrorMessage());

    }

    /**
     * @param $user_id int
     * @param $status int
     *
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function changeStatus($user_id, $status ){

        //Verificando si existe usuario
        $this->getUser_ById( $user_id );

        //Eliminando usuario (cambiando status)
        if( $status == Utils::$STATUS_DISABLE ){
            $result = $this->userPer->changeStatusToDisable( $user_id );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( "changeStatus","Ocurrio un error al deshabilitar usuario", $result->getErrorMessage());
        }
        else if( $status == Utils::$STATUS_ENABLE ){
            $result = $this->userPer->changeStatusToEnable( $user_id );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( "changeStatus","Ocurrio un error al habilitar usuario", $result->getErrorMessage());
        }

    }

    /**
     * @param $id
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function deleteUser($id)
    {
        //Verificando si existe usuario
        $result = $this->userPer->getUser_ById( $id );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "deleteUser","Ocurrio un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NotFoundException("No existe usuario");

        //Eliminando usuario (cambiando status)
        $result = $this->userPer->deleteUser_ById( $id );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( "deleteUser","Ocurrio un error al eliminar usuario", $result->getErrorMessage());
    }




    //-----------------------
    // EXTRAS
    //-----------------------
    /**
     * array['field']
     *
     * @param $data \mysqli_result
     *
     * @return UserModel
     */
    public static function makeUserModel($data ){
        $user = new UserModel();
        //setting data
        $user->setId( $data['id'] );
        $user->setEmail( $data['email'] );
//        $user->setPassword( $data['password'] );
        $user->setdate_register( $data['date_register'] );
        $user->setStatus( $data['status'] );
        $user->setRole( $data['role'] );
        //Returning object
        return $user;
    }




}