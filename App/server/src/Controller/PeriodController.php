<?php namespace App\Controller;

use App\Exceptions\RequestException;
use App\Model\Period;
use App\Service\PeriodService;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Utils;

class PeriodController
{
    /**
     * @param $req Request
     * @param $res Response
     * @return Response
     */
    public function getPeriods($req, $res){
        try {
            $periodService = new PeriodService();
            $result = $periodService->getPeriods();
            return Utils::makeResultJSONResponse( $res, Utils::$OK, $result );
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }

    /**
     * @param $req Request
     * @param $res Response
     * @return Response
     */
    public function getCurrentPeriod($req, $res){
        try {
            $periodService = new PeriodService();
            $result = $periodService->getCurrentPeriod();
            return Utils::makeResultJSONResponse( $res, Utils::$OK, $result );
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }

    /**
     * @param $req Request
     * @param $res Response
     * @param $params array
     * @return Response
     */
    public function getPeriod_ById($req, $res, $params){
        try {
            $periodService = new PeriodService();
            $result = $periodService->getPeriod_ById( $params['id'] );
            return Utils::makeResultJSONResponse( $res, Utils::$OK, $result );
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }


    /**
     * @param $req Request
     * @param $res Response
     * @return Response
     */
    public function createPeriod($req, $res){
        try {
            $periodService = new PeriodService();
            /* @var $period Period */
            $period = $req->getAttribute('period_data');
            $periodService->createPeriod( $period->getDateStart(), $period->getDateEnd() );
            return Utils::makeMessageJSONResponse( $res, Utils::$CREATED, "Periodo registrado con exito");
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }


    /**
     * @param $req Request
     * @param $res Response
     * @param $params
     * @return Response
     */
    public function updatePeriod($req, $res, $params){
        try {
            $periodService = new PeriodService();
            /* @var $period Period */
            $period = $req->getAttribute('period_data');
            $period->setId( $params['id'] );
            $periodService->updatePeriod( $period );
            return Utils::makeMessageJSONResponse( $res, Utils::$OK, "Periodo actualizado con exito");
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }

    /**
     * @param $req Request
     * @param $res Response
     * @param $params
     * @return Response
     */
    public function changeStatus($req, $res, $params){
        try {
            $periodService = new PeriodService();
            $periodService->changeStatus( $params['id'], $params['status'] );
            return Utils::makeMessageJSONResponse( $res, Utils::$OK, "Modificado status de periodo con exito");

        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }




    /**
     * @param $req Request
     * @param $res Response
     * @param $params
     * @return Response
     */
    public function deletePeriod($req, $res, $params){
        try {
            $periodService = new PeriodService();
            $periodService->deletePeriod( $params['id'] );
            return Utils::makeMessageJSONResponse( $res, Utils::$OK, "Periodo eliminado");
        } catch (RequestException $e) {
            return Utils::makeMessageJSONResponse( $res, $e->getStatusCode(), $e->getMessage() );
        }
    }


}