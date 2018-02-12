<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Middleware\Authentication;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/conf.php';
require 'helpers.php';



$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;


$app = new \Slim\App($config);


$authenticator = function ($request, $response, $next) {

    if ($request->hasHeader('token')) {
        $token = $request->getHeaderLine('token');
        $reply = checkTokenStatus($token);
        if ($reply == 'false') {
            return $response->withStatus(401)
                            ->withHeader('Content-Type', 'application/json');
        }

        $response = $next($request, $response);

        return $response;
    }
    if ($request->hasHeader('imei')) {
        $imei = $request->getHeaderLine('imei');
        $reply = validateDeviceImei($imei);
        if ($reply == 'false') {
            return $response->withStatus(401)
                            ->withHeader('Content-Type', 'application/json');
        }

        $response = $next($request, $response);

        return $response;
    }
    return $response->withStatus(401)
                    ->withHeader('Content-Type', 'application/json');
};


//toll apis
// Notice we pass along that $mailer we created in index.php



$app->get('/', function ( Request $request, Response $response) {

    $response->getBody()->write(' Hello ');

    return $response;
});

$app->get('/api/endofshift', function (Request $request, Response $response,$args) {
    
     $cashier = $request->getHeaderLine('userid');
     $shift = $request->getHeaderLine('shift');
     $date = date('Y-m-d');
     

    $dataresponse = endofShift($cashier,$shift,$date);


    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->post('/api/authenticate', function (Request $request, Response $response, $args) {
    $dataArray = getRequestParsedBody($request);

    // print_r($dataArray) ;
    $dataresponse = authenticateuser($dataArray);

//
    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});




$app->get('/test', function (Request $request, Response $response) {
    $service = testfunc();

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($service));
})->add($authenticator);

$app->get('/api/cashiers', function (Request $request, Response $response) {


    $dataresponse = getCashiers();

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});
$app->get('/api/tollpoints', function (Request $request, Response $response) {
    $dataresponse = getTollpoints();
    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->get('/api/categories', function (Request $request, Response $response) {
    $dataresponse = getCategories();
    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->post('/api/transactions', function (Request $request, Response $response) {
    $dataArray = getRequestParsedBody($request);
    $dataresponse = saveTransactions($dataArray);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->post('/api/excesscash', function (Request $request, Response $response) {
     $cashier = $request->getHeaderLine('userid');
     $shift = $request->getHeaderLine('shift');
     $date = date('Y-m-d');
     

    $dataArray = getRequestParsedBody($request);
    $dataresponse = saveExcessCash($dataArray);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->get('/api/districtscashiers/{ids}', function (Request $request, Response $response, $args) {

    $dataresponse = getRegionCashiers($args);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});


$app->get('/api/tollcashiers/{ids}', function (Request $request, Response $response, $args) {

    $dataresponse = getTollCashiers($args);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});


$app->get('/api/regiontolls/{ids}', function (Request $request, Response $response, $args) {

    $dataresponse = getRegionTolls($args);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});


$app->get('/api/transactions', function (Request $request, Response $response) {
//
    $dataresponse = getTransactions();
    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});

$app->get('/api/setup', function (Request $request, Response $response) {

    $imei = $request->getHeaderLine('imei');

    $dataresponse = setup($imei);

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($dataresponse));
});










//toll apis
//web apis
//************************************************cashier apis ***************************************
$app->group('/api', function () use ($app) {


    $app->get('/reset/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = resetUserPassword($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->get('/cashiers/reset/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = resetCashierPassword($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/changepassword', function (Request $request, Response $response, $args) {

        $token = $request->getHeaderLine('token');
        $code = $request->getHeaderLine('code');
        $dataresponse = changePassword($code, $token);
//
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });







    $app->get('/cashier/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = getCashierInformation($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->post('/cashier', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);
        $dataresponse = registerCashier($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->put('/cashier', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);
        //print_r($dataArray);
        $dataresponse = updateCashier($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->delete('/cashier/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = deleteCashier($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


//*****************************************************end cashier apis *****************************
//***********************************************begin toll apis **************************************************


    $app->get('/toll/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = getTollInformation($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/tollpoint', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);
        $dataresponse = registerTollpoint($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->put('/toll', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);
        //print_r($dataArray);
        $dataresponse = updateToll($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->delete('/toll/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = deleteToll($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

//**************************************************end toll apis ****************************************************
//***********************************************begin category apis **************************************************



    $app->get('/category/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = getCategoryInformation($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/category', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);
        $dataresponse = saveCategory($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->delete('/category/{id}', function (Request $request, Response $response, $args) {
        $dataresponse = deleteCategory($args['id']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->put('/category', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);
        //print_r($dataArray);
        $dataresponse = updateCategory($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });



//**************************************************end category apis ****************************************************
//***********************************************begin transaction apis **************************************************





    $app->get('/tolltransactions', function (Request $request, Response $response) {
        $dataresponse = fetchTransactionsOnTollLevel();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });
    $app->get('/regiontransactions', function (Request $request, Response $response) {
        $dataresponse = fetchTransactionsOnRegionalLevel();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });
//**************************************************end transaction apis ****************************************************
//***********************************************begin user apis **************************************************

    $app->get('/users', function (Request $request, Response $response) {
        $dataresponse = getUsers();
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/user', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = registerUser($dataArray);
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/users/{userid}', function (Request $request, Response $response, $args) {

        // echo $args['userid'];
        $dataresponse = getUserInformation($args['userid']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->put('/users', function (Request $request, Response $response, $args) {

        $dataArray = getRequestParsedBody($request);
        $dataresponse = updateUserInformation($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->delete('/users/{userid}', function (Request $request, Response $response, $args) {

        // echo $args['userid'];
        $dataresponse = deleteUserInformation($args['userid']);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });





//***********************************************end user apis **************************************************
//***********************************************begin mobile apis **************************************************
//***********************************************end mobile apis **************************************************
//***********************************************begin report apis **************************************************

    $app->post('/generalreport', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = generalReport($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/transactions/region', function (Request $request, Response $response) {
        $dataresponse = getTransactionsPerRegion();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/daywisereport', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = fetchDayWiseReport($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/monthlywisereport', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = fetchMonthlyWiseReport($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->post('/yearlyreport', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = fetchYearlyReport($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/shiftreport', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = fetchShiftReport($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/cummulative', function (Request $request, Response $response) {
        $tollpoints = getTollPointsTotalCount();
        $totalcashiers = getCashiersTotalCount();
        $numberoftransactions = getTransactionsTotalCount();
        $totaltransactions = getTotalTransactionsCost();

        $dataArray = array(
            "status" => 0,
            "message" => "success",
            "data" => array(
                'totaltoll' => $tollpoints,
                'totalcashiers' => $totalcashiers,
                'numberoftransactions' => $numberoftransactions,
                'totaltransactions' => $totaltransactions
            )
        );

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });


    $app->get('/performingcashiers', function (Request $request, Response $response) {

        $dataArray = reportforPerformingCashiersAcrossCountry();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });

    $app->get('/nonperformingcashiers', function (Request $request, Response $response) {

        $dataArray = reportforNonPerformingCashiersAcrossCountry();

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });


    $app->get('/regionperformance', function (Request $request, Response $response) {

        $dataArray = reportforRegionPerformance();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });



    $app->get('/shiftperformance', function (Request $request, Response $response) {

        $dataArray = reportforShiftPerformance();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });


    $app->get('/performingtolls', function (Request $request, Response $response) {

        $dataArray = reportforPerformingTollsAcrossCountry();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });

    $app->get('/nonperformingtolls', function (Request $request, Response $response) {

        $dataArray = reportforNonPerformingTollsAcrossCountry();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });


    $app->get('/categoryperformance', function (Request $request, Response $response) {

        $dataArray = reportforCategoryPerformance();


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataArray));
    });


    $app->get('/weeklyreport/{type}/{value}', function (Request $request, Response $response, $args) {

        $dataresponse = reportWeekly($args);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/yearlyreport/{type}/{value}', function (Request $request, Response $response, $args) {

        $dataresponse = reportyearly($args);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/monthlyreport/{type}/{value}', function (Request $request, Response $response, $args) {

        $dataresponse = reportMonthly($args);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });
    $app->post('/customperformance', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = customPerformance($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });


    $app->post('/customtrend', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);

        $dataresponse = customTrendAnalysis($dataArray);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->post('/endofshift', function (Request $request, Response $response) {
        $dataArray = getRequestParsedBody($request);


        $dataresponse = endofShift($dataArray['cashier'],$dataArray['shift'],$dataArray['date']);


        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

//***********************************************end report apis **************************************************
//***********************************************begin setup apis **************************************************


    $app->get('/regions', function (Request $request, Response $response) {
        $dataresponse = getRegions();
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/districts', function (Request $request, Response $response) {
        $dataresponse = getDistricts();
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });

    $app->get('/regiondistricts/{region}', function (Request $request, Response $response, $args) {
        $dataresponse = getRegionDistricts($args['region']);
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });




    $app->post('/district', function (Request $request, Response $response, $args) {
        $dataArray = getRequestParsedBody($request);
        //print_r($dataArray);
        $dataresponse = saveDistricts($dataArray);

        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($dataresponse));
    });
})->add($authenticator);

//***********************************************end setup apis **************************************************
// Run app
$app->run();
