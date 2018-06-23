app.controller('AdvisoriesController', function($scope, $http, Notification, AdvisoriesService, RequestFactory){

    $scope.url = RequestFactory.getBaseURL();

    $scope.requestedAds = [];
    $scope.showRequestedAds = false;

    $scope.adviserAds = [];
    $scope.showAdviserdAds = false;
    

    $scope.showAdvisories = false;
    $scope.showNewAdvisory = false;

    //Request advisory
    $scope.subjects = {};
    $scope.selectedSub = null;



    $scope.getRequestedAds = function(){
        $scope.loading.status = true;
        
        $scope.showRequestedAds = true;
        $scope.showAdviserdAds = false;

        AdvisoriesService.getRequestedAdvisories( $scope.student.id,
            function(success){
                $scope.requestedAds = success.data;
                $scope.loading.status = false;
            },
            function(error){
                Notification.error("Ocurrio un error: "+error.data);
                $scope.loading.status = false;
            }
        );
    };

    $scope.getAdviserAds = function(){
        $scope.loading.status = true;
        $scope.showRequestedAds = false;
        $scope.showAdviserdAds = true;

        AdvisoriesService.getAdviserAdvisories( $scope.student.id,
            function(success){
                $scope.adviserAds = success.data;
                $scope.loading.status = false;
            },
            function(error){
                Notification.error("Ocurrio un error: "+error.data);
                $scope.loading.status = false;
            }
        );
    };
    

    $scope.getSubjects = function(){
        AdvisoriesService.getSubjects(
            function(success){
                $scope.subjects = success.data;
            },
            function(error){
                Notification.error("Error: "+error.data);
            }
        );
    };

    $scope.newAdvisory = function(){
        //TODO: obtener solo materias que no se han solicitado
        $scope.getSubjects();
        $scope.selectedSub = null;
        $scope.showNewAdvisory = true;
    }

    $scope.closeNewAdvisory = function(){
        $scope.showNewAdvisory = false;
        $scope.selectedSub = null;
    }

    $scope.selectSub = function(subject){
        $scope.selectedSub = subject;
    };


    $scope.requestAdvisory = function(){
        if( $scope.selectedSub == null){
            Notification.warning("No ha seleccionado materia");
            return;    
        }

        Notification("Procesando...");
        var advsory = {
            subject: $scope.selectedSub.id,
            description: "Sin descripcion",
            student: $scope.student.id
        };
        AdvisoriesService.requestAdvisory(advsory, 
            function(success){
                Notification.success("Solicitado con exito");
                $scope.closeNewAdvisory();
                $scope.getRequestedAds();
            },
            function(error){
                if( error.status == CONFLICT ){
                    Notification.warning("Error al solicitar asesoria: "+error.data);
                }
                else{
                    Notification.error("Error al solicitar asesoria: "+error.data);
                }
            }
        );
        
    };

    $scope.finalize = function(advisory_id){
        Notification("Procesando...");

        AdvisoriesService.finalizeAdvisory(advisory_id,
            function(success){
                Notification.success("Asesoria finalizada con éxito");
                //TODO: obtener asesorias
            },
            function(error){
                Notification.error("Ocurrio un error");
            }
        );
    };



    (function(){

        $scope.loading.status = true;
        $scope.period.message = "";
        
        AdvisoriesService.getCurrentPeriod(
            function(success){
                if( success.status == NO_CONTENT ){
                    $scope.loading.status = false;
                    $scope.period.message = "No hay un periodo actual disponible";
                }
                else{
                    $scope.period.data = success.data;
                    $scope.showAdvisories = true;

                    $scope.getRequestedAds();
                }
            },
            function(error){
                $scope.loading.status = false;
            }
        );
        
        
    })();


});