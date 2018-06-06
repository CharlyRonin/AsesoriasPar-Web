app.service('ScheduleService', function($http){

    

    this.getDaysAndHours = function(successCallback, errorCallback){
        $http({
            method: 'GET',
            url: "http://api.ronintopics.com/index.php/schedule/source"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    } 


    this.getStudentSchedule = function(student_id, successCallback, errorCallback){
        $http({
            method: 'GET',
            url: "http://api.ronintopics.com/index.php/students/"+student_id+"/schedule"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    }

    this.createSchedule = function(student_id, successCallback, errorCallback){
        $http({
            method: 'POST',
            url: "http://api.ronintopics.com/index.php/students/"+student_id+"/schedule"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    } 


    this.updateScheduleHours = function(schedule_id, hours, successCallback, errorCallback){
        $http({
            method: 'PUT',
            url: "http://api.ronintopics.com/index.php/schedule/"+schedule_id+"/hours",
            data: {
                hours: hours
            }
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    } 


    this.updateScheduleSubjects = function(schedule_id, subjects, successCallback, errorCallback){
        $http({
            method: 'PUT',
            url: "http://api.ronintopics.com/index.php/schedule/"+schedule_id+"/subjects",
            data: {
                subjects: subjects
            }
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });

    }


    
    this.getSubjects = function(successCallback, errorCallback){
        $http({
            method: 'GET',
            url: "http://api.ronintopics.com/index.php/subjects/enabled"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    } 

});