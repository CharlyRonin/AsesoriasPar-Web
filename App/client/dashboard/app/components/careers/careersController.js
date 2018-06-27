angular.module("Dashboard").controller('CareersController', function($scope,  Notification, CareerService, STATUS){
    $scope.page.title = "Carreras";
    
    $scope.careers = [];
    $scope.career = {
        id: null,
        name: null,
        short_name: null
    };

    $scope.showNewCareer = false;
    $scope.showUpdateCareer = false;
    $scope.loading = true;

    
    /**
     * Obtiene carreras registrados
     */
    $scope.getCareers = function(){

        $scope.showUpdateCareer = false;
        $scope.loading = true;

        CareerService.getCareers()
            .then(function(success){
                if( success.status === STATUS.NO_CONTENT ){
                    //Notification.primary("no hay registros");
                    $scope.careers = [];
                }
                else
                    $scope.careers = success.data;

                $scope.loading = false;
            },
            function(error){
                Notification.error("Error al obtener carreras: "+error.data);
                $scope.careers = [];
                $scope.loading = false;
            }
        );
    }

    /**
     * Agrega un carrera
     * @param {*} career 
     */
    $scope.addCareer = function(career){
        Notification("Procesando...");
        
        CareerService.addCareer(career)
            .then(function(success){
                Notification.success("Registrado con exito");
                career = null;
                $scope.getCareers();
            },
            function(error){
                Notification.error("Error: "+error.data);
            }
        );
    }


    $scope.editCareer = function(career){
        $scope.career = career;
        $scope.showUpdateCareer = true;
    };


    $scope.updateCareer = function(career){
        Notification("Procesando...");
        
        CareerService.updateCareer(career)
            .then(function(success){
                Notification.success("Actualizado con exito");
                $scope.getCareers();
            },
            function(error){
                Notification.error("Error: "+error.data);
                $scope.showUpdateCareer = false;
            }
        );
    };


    

    $scope.deleteCareer = function(career_id){

        Notification("Procesando...");
        //Deshabilita botones
        $scope.disableButtons(true, '.opt-career-'+career_id);
        
        CareerService.deleteCareer(career_id)
            .then(function(success){
                Notification.success("Eliminado con exito");
                $scope.getCareers();
            },
            function(error){
                Notification.success("Error: "+error.data);
                $scope.disableButtons(false, '.opt-career-'+career_id);
            }
        );
    };

    /**
     * 
     * @param {*} career_id 
     */
    $scope.disableCareer = function(career_id){
        $scope.disableButtons(true, '.opt-career-'+career_id);
        Notification("Procesando...");

        CareerService.changeStatus(career_id, DISABLED)
            .then(function(success){
                Notification.success("Deshabilitado con exito");
                $scope.getCareers();
            },
            function(error){
                Notification.error("Error al Deshabilitar carrera: "+error.data);
                $scope.disableButtons(false, '.opt-career-'+career_id);
            }
        );
    };

    /**
     * 
     * @param {*} career_id 
     */
    $scope.enableCareer = function(career_id){
        $scope.disableButtons(true, '.opt-career-'+career_id);
        Notification("Procesando...");

        CareerService.changeStatus(career_id, ACTIVE)
            .then(function(success){
                Notification.success("habilitado con exito");
                $scope.getCareers();
            },
            function(error){
                Notification.error("Error al habilitar carrera: "+error.data);
                $scope.disableButtons(false, '.opt-career-'+career_id);
            }
        );
    };


    //Obtiene todos por default
    $scope.getCareers();

});