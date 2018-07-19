<?php

namespace App\Middleware;

//require_once __DIR__ . '/conf.php';

class Authentication {

    protected $token; 
    protected $imei;

    public function __invoke($request, $response, $next) {

        $this->token = $request->getHeaderLine('token');

        return $next($request, $response);
    }

    public function call() {

        //$token = $this->app->request()->getHeaderLine('token');

        checkTokenStatus($this->token);
        $this->next->call();
    }

    protected function checkTokenStatus($token) {

        $result = ORM::for_table('tokens')->where('token_code', $token)->find_one();
        $current_time = date("Y-m-d H:i:s");
        $expire_time = $result->dateexpired;

        if ($expire_time > $current_time) {
            //allow
            $result->dateexpired = date('Y-m-d H:i:s', strtotime("+5 min"));
            $result->save();
        }
     //   $this->app->halt(401);
    }

}
