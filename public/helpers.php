<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require __DIR__ . '/conf.php';

function getRequestParsedBody($request) {
    $body = $request->getParsedBody();
    return $body;
}

function testfunc() {
    $users = ORM::for_table('users')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $users
    );
    return $dataArray;
}

function getCashiers() {
    $results = ORM::for_table('cashiers_view')->where('active', 0)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getCategories() {
    $results = ORM::for_table('categories')->where('active', 0)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getTollpoints() {
    $results = ORM::for_table('tollpoints_view')->where('active', 0)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getUsers() {
    $results = ORM::for_table('users')->where('active', 0)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getTransactions() {
    $results = ORM::for_table('transaction_view')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getDistricts() {
    $results = ORM::for_table('districts')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getRegions() {
    $results = ORM::for_table('regions')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function setup($imei) {

    $regions = ORM::for_table('regions')->find_array();
    $categories = ORM::for_table('categories')->find_array();

    $devicecode = checkdeviceexistence($imei);
    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => array(
            "categories" => $categories,
            "regions" => $regions,
            "devicecode" => $devicecode,
            "counter" => getDevicelastCounter($devicecode)
        )
    );

    return $dataArray;
}

function registerPos($imei) {

    $queryset = ORM::for_table('devices')->create();

    $queryset->devicecode = rand(0, 100);
    $queryset->imei = $imei;
    $queryset->save();

    $data = $queryset->devicecode;

    return $data;
}

function checkdeviceexistence($imei) {

    $result = ORM::for_table('devices')->where('imei', $imei)->find_one();

    if (empty($result)) {

        $response = registerPos($imei);
        return $response;
    }

    return $result->devicecode;
}

function getRegionCashiers($args) {

    $region_ids = $args['ids'];
    $resultset = ORM::forTable()->rawQuery('SELECT cashiers.*  FROM `cashiers` left join tollpoints on cashiers.toll= tollpoints.id WHERE tollpoints.region IN (' . $region_ids . ') AND cashiers.active=0 ')->findArray();


    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $resultset
    );
    return $dataArray;
}

function authenticateuser($data) {
    
    
    $username = $data['email'];
    $password = md5($data['password']);

    $result = ORM::for_table('users')->where(array(
                'email' => $username,
                'password' => $password,
            ))->find_one();

    $name = $result->name;
    $role = $result->role;
    $lastlogin = $result->lastlogin;
    $userid = $result->id;
    $token = getUserToken($userid);

    if (empty($result)) {

        $dataArray = array(
            "status" => 1,
            "message" => "user does not exist"
        );

        return $dataArray;
    }
    updateUserLastLogin($userid);

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "details" => array(
            'name' => $name,
            'role' => $role,
            'lastlogin' => $lastlogin,
            'userid' => $userid,
            'token' => $token
        )
    );

    return $dataArray;
}

function authenticatecashier($data) {
    $username = $data['contact'];
    $password = md5($data['password']);

    $result = ORM::for_table('cashiers')->where(array(
                'contact' => $username,
                'password' => $password,
                'active' => 0
               
            ))->find_one();

    $name = $result->name;
    $lastlogin = $result->lastlogin;
    $cashierid = $result->id;

    if (empty($result)) {

        $dataArray = array(
            "status" => 1,
            "message" => "Incorrect credentials or Cashier already logged in"
        );

        return $dataArray;
    }
    updateCashierLastLogin($cashierid);

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "details" => array(
            'name' => $name,
            'lastlogin' => $lastlogin,
            'userid' => $cashierid
        )
    );

    return $dataArray;
}

function updateUserLastLogin($userid) {

    $result = ORM::for_table('users')->where('id', $userid)->find_one();

    $result->lastlogin = date('Y-m-d h:i:s');
    $result->save();
}

function updateCashierLastLogin($cashierid) {

    $result = ORM::for_table('cashiers')->where('id', $cashierid)->find_one();

    $result->lastlogin = date('Y-m-d h:i:s');
    $result->loggedin = 1;
    $result->save();
}

//function getlasttransactioncounter($devicecode) {
//
//    $result = ORM::for_table('transactions')->where('devicecode', $devicecode)->find_one();
//
//    if (empty($result)) {
//
//        $response = registerPos($imei);
//        return $response;
//    }
//
//    return $result->devicecode;
//}

function getRegionDistricts($regionid) {

    $results = ORM::for_table('region_districts_view')->where('regionid', $regionid)->find_array();


    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function saveTransactions($data) {
    $devicecode = $data['devicecode'];
    $transactions = $data['transactions'];

    $sql = array();
    foreach ($transactions as $row) {
        $sql[] = '(' . $devicecode . ',' . $row['toll'] . ',' . $row['category'] . ',"' . $row['amount'] . '",' . $row['cashier']
                . ',"' . $row['transactiondate'] . '",' . $row['counter'] . ',"' . $row['transactionid'] . '","' . $row['shift'] . '")';
    }


    $query = ORM::raw_execute('INSERT IGNORE INTO transactions (devicecode, toll,category,amount,cashier,transactiondate,counter,transactionid,shift) VALUES ' . implode(',', $sql));

    if ($query) {


        $dataArray = array(
            "status" => 0,
            "message" => "success",
            "data" => array(
                "devicecode" => $devicecode,
                "counter" => getDevicelastCounter($devicecode)
            )
        );
        return $dataArray;
    } else {


        $dataArray = array(
            "status" => 0,
            "message" => "transactions fail to save",
            "devicecode" => $devicecode
        );
    }
//  print "INSERT INTO transactions VALUES  $transactions ";
}

function getDevicelastCounter($devicecode) {

//SELECT counter FROM `transactions` WHERE id=(SELECT MAX(id) FROM `transactions`) AND devicecode=2;

    $resultset = ORM::forTable()->rawQuery('SELECT counter FROM `transactions` WHERE id=(SELECT MAX(id) FROM `transactions`) AND devicecode=' . $devicecode)->findOne();

    if (empty($resultset)) {
        return 0;
    }

    return $resultset->counter;
}

function saveCategory($data) {

    $query = ORM::raw_execute('INSERT IGNORE INTO categories (name, url,amount,description,addedby) VALUES ("' . $data['name'] . '","' . $data['url'] . '","' . $data['price'] . '","' . $data['description'] . '","' . $data['addedby'] . '")');


//    $queryset = ORM::for_table('categories')->create();
//
//    $queryset->name = $data['name'];
//    $queryset->url = $data['url'];
//    $queryset->amount = $data['amount'];
//    $queryset->description = $data['description'];
//    $queryset->addedby = $data['addedby'];
//    $result = $queryset->save();

    if ($query) {
        $dataArray = array(
            "status" => 0,
            "message" => "Category saved successfully"
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 1,
        "message" => "Failed  "
    );
    return $dataArray;
}

function registerTollpoint($data) {

    $query = ORM::raw_execute('INSERT  INTO tollpoints (area, region,addedby) VALUES ("' . $data['area'] . '","' . $data['region'] . '","' . $data['addedby'] . '")');

    if ($query) {
        $dataArray = array(
            "status" => 0,
            "message" => "Tollpoint registered successfully"
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 1,
        "message" => "Failed in registering tollpoint"
    );
    return $dataArray;
}

function registerCashier($data) {

    $query = ORM::raw_execute('INSERT IGNORE INTO cashiers (name, contact,username,password,email,toll,addedby) VALUES ("' . $data['name'] . '","' . $data['contact'] . '","' . $data['username'] . '","' . $data['password'] . '","' . $data['email'] . '","' . $data['toll'] . '","' . $data['addedby'] . '")');

    if ($query) {
        $dataArray = array(
            "status" => 0,
            "message" => "Cashier registered successfully"
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 1,
        "message" => "Failed in registering cashier"
    );
    return $dataArray;
}

function registerUser($data) {

    $email = $data['email'];

    $result = checkuserexistence($email);

    if ($result == 0) {

        $queryset = ORM::for_table('users')->create();

        $queryset->name = $data['name'];
        $queryset->password = $data['password'];
        $queryset->contact = $data['contact'];
        $queryset->role = $data['role'];
        $queryset->email = $data['email'];
        $queryset->addedby = $data['addedby'];
         $queryset->region = $data['region'];
        $queryset->save();


        $dataArray = array(
            "status" => 0,
            "message" => "User registered successfully"
        );
        return $dataArray;
    }


    $dataArray = array(
        "status" => 1,
        "message" => "User Email Already exist "
    );
    return $dataArray;
}

function checkuserexistence($email) {

    $result = ORM::for_table('users')->where('email', $email)->find_one();


    if (empty($result)) {
        return '0';
    }
    return '1';
}

function saveDistricts($data) {

    $districts = $data['districts'];
    $addedby = $data['addedby'];
    $sql = array();
    foreach ($districts as $row) {
        $sql[] = '("' . $row['name'] . '",' . $addedby . ')';
    }


    $query = ORM::raw_execute('INSERT IGNORE INTO districts (name,addedby) VALUES ' . implode(',', $sql));

    if ($query) {


        $dataArray = array(
            "status" => 0,
            "message" => "success in saving districts"
        );
        return $dataArray;
    } else {


        $dataArray = array(
            "status" => 0,
            "message" => "Fail saving districts",
        );
    }
//  print "INSERT INTO transactions VALUES  $transactions ";
}

function generalReport($data) {

    $cashier = $data['cashier'];
    $category = $data['category'];
    $region = $data['region'];
    $district = $data['district'];
    $toll = $data['toll'];
    $startdate = $data['startdate'];
    $enddate = $data['enddate'];


    $query_build = "SELECT * FROM transaction_view WHERE DATE(transactiondate) BETWEEN '$startdate' AND '$enddate'";

    if (!empty($cashier)) {
        $query_build = $query_build . " AND cashier IN(" . $cashier . ")";
    }

    if (!empty($category)) {
        $query_build = $query_build . " AND category IN(" . $category . ")";
    }

    if (!empty($region)) {
        $query_build = $query_build . " AND region_id IN(" . $region . ")";
    }

    if (!empty($district)) {
        $query_build = $query_build . " AND district_id IN(" . $district . ")";
    }

    if (!empty($toll)) {
        $query_build = $query_build . " AND toll IN(" . $toll . ")";
    }



    $results = ORM::forTable()->rawQuery($query_build)->findArray();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getTransactionsPerRegion() {
    $results = ORM::for_table('transactions_per_regions')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchTransactionsOnTollLevel() {
    $results = ORM::for_table('tollpoint_report_view')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchTransactionsOnRegionalLevel() {
    $results = ORM::for_table('regions_report_view')->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchDayWiseReport($data) {


    $toll = $data['toll'];
    $startdate = $data['startdate'];
    $enddate = $data['enddate'];


    $query_build = "SELECT * FROM daywise_report_view WHERE DATE(transaction_date) BETWEEN '$startdate' AND '$enddate'";


    if (!empty($toll)) {
        $query_build = $query_build . " AND toll IN(" . $toll . ")";
    }



    $results = ORM::forTable()->rawQuery($query_build)->findArray();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchYearlyReport($data) {


    $toll = $data['toll'];

    $year = $data['year'];


    $query_build = "SELECT * FROM yearly_report_view WHERE transaction_year = '$year' ";


    if (!empty($toll)) {
        $query_build = $query_build . " AND toll IN(" . $toll . ")";
    }



    $results = ORM::forTable()->rawQuery($query_build)->findArray();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchMonthlyWiseReport($data) {


    $toll = $data['toll'];
    $month = $data['month'];
    $year = $data['year'];


    $query_build = "SELECT * FROM monthly_report_view WHERE transaction_month = '$month' AND transaction_year= '$year'";


    if (!empty($toll)) {
        $query_build = $query_build . " AND toll IN(" . $toll . ")";
    }



    $results = ORM::forTable()->rawQuery($query_build)->findArray();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function fetchShiftReport($data) {


    $toll = $data['toll'];

    $shift = $data['shift'];
    $startdate = $data['startdate'];
    $enddate = $data['enddate'];


    $query_build = "SELECT * FROM shiftwise_report WHERE shift = '$shift' AND DATE(transactiondate) BETWEEN '$startdate' AND '$enddate' ";


    if (!empty($toll)) {
        $query_build = $query_build . " AND toll IN(" . $toll . ")";
    }



    $results = ORM::forTable()->rawQuery($query_build)->findArray();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getCashiersTotalCount() {
    $results = ORM::for_table('cashiers')->count();
    return $results;
}

function getTransactionsTotalCount() {
    $results = ORM::for_table('transactions')->count();
    return $results;
}

function getTollPointsTotalCount() {
    $results = ORM::for_table('tollpoints')->count();
    return $results;
}

function getTotalTransactionsCost() {
    $resultset = ORM::forTable()->rawQuery('SELECT SUM(amount) as total FROM transactions')->findOne();
    return $resultset->total;
}

function getUserInformation($userid) {

    $results = ORM::for_table('users')->where('id', $userid)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function deleteUserInformation($userid) {

    $results = ORM::for_table('users')->where('id', $userid)->find_one();

    $results->active = 1;
    $results->save();

    $dataArray = array(
        "status" => 0,
        "message" => "User deleted successfully",
    );

    return $dataArray;
}

function updateUserInformation($data) {

    $userid = $data['userid'];


    $results = ORM::for_table('users')->where('id', $userid)->find_one();

    $results->name = $data['name'];
    $results->email = $data['email'];
    $results->contact = $data['contact'];
    $results->role = $data['role'];

    $saved = $results->save();

    if (!$saved) {
        $dataArray = array(
            "status" => 1,
            "message" => "User information wasnt updated",
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 0,
        "message" => "User information updated successfully",
    );

    return $dataArray;
}

function updateCashier($data) {

    $id = $data['cashier'];


    $results = ORM::for_table('cashiers')->where('id', $id)->find_one();

    $results->name = $data['name'];
    $results->email = $data['email'];
    $results->contact = $data['contact'];
    $results->toll = $data['toll'];

    $saved = $results->save();

    if (!$saved) {
        $dataArray = array(
            "status" => 1,
            "message" => "Cashier information wasnt updated",
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 0,
        "message" => "Cashier information updated successfully",
    );

    return $dataArray;
}

function updateToll($data) {

    $id = $data['toll'];


    $results = ORM::for_table('tollpoints')->where('id', $id)->find_one();

    $results->area = $data['area'];
    $results->region = $data['region'];

    $saved = $results->save();

    if (!$saved) {
        $dataArray = array(
            "status" => 1,
            "message" => "Toll information wasnt updated",
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 0,
        "message" => "Toll updated successfully",
    );

    return $dataArray;
}

function updateCategory($data) {

    $id = $data['categoryid'];


    $results = ORM::for_table('categories')->where('id', $id)->find_one();

    $results->name = $data['name'];
    $results->amount = $data['price'];
    $results->url = $data['url'];
    $results->description = $data['description'];

    $saved = $results->save();

    if (!$saved) {
        $dataArray = array(
            "status" => 1,
            "message" => "Category information wasnt updated",
        );
        return $dataArray;
    }

    $dataArray = array(
        "status" => 0,
        "message" => "Category updated successfully",
    );

    return $dataArray;
}

function deleteCategory($id) {

    $results = ORM::for_table('categories')->where('id', $id)->find_one();

    $results->active = 1;
    $results->save();

    $dataArray = array(
        "status" => 0,
        "message" => "Category deleted successfully",
    );

    return $dataArray;
}

function deleteCashier($id) {

    $results = ORM::for_table('cashiers')->where('id', $id)->find_one();

    $results->active = 1;
    $results->save();

    $dataArray = array(
        "status" => 0,
        "message" => "Cashier deleted successfully",
    );

    return $dataArray;
}

function deleteToll($id) {

    $results = ORM::for_table('tollpoints')->where('id', $id)->find_one();

    $results->active = 1;
    $results->save();

    $dataArray = array(
        "status" => 0,
        "message" => "Toll deleted successfully",
    );

    return $dataArray;
}

function getTollInformation($id) {

    $results = ORM::for_table('tollpoints')->where('id', $id)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getCashierInformation($id) {

    $results = ORM::for_table('cashiers')->where('id', $id)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getCategoryInformation($id) {

    $results = ORM::for_table('categories')->where('id', $id)->find_array();

    $dataArray = array(
        "status" => 0,
        "message" => "success",
        "data" => $results
    );
    return $dataArray;
}

function getUserToken($userid) {



    $result = ORM::for_table('tokens')->where('userid', $userid)->find_one();

    if (empty($result)) {
        $token_code = createUserToken($userid);

        return $token_code;
    }

    $result->token_code = rand_code(12) . str_pad($userid, 8, '0', STR_PAD_LEFT);

    $result->dateexpired = date('Y-m-d H:i:s', strtotime("+5 min"));
    $result->save();

    return $result->token_code;
}

function createUserToken($userid) {

    $queryset = ORM::for_table('tokens')->create();

    //dateexpired
    $queryset->userid = $userid;
    $queryset->token_code = rand_code(12) . str_pad($userid, 5);
    $queryset->dateexpired = date('Y-m-d H:i:s', strtotime("+5 min"));

    $queryset->save();

    $data = $queryset->token_code;

    return $data;
}

function checkTokenStatus($token) {

    $result = ORM::for_table('tokens')->where('token_code', $token)->find_one();
    $current_time = date("Y-m-d H:i:s");
    $expire_time = $result->dateexpired;

    if ($expire_time > $current_time) {
        //allow
        $result->dateexpired = date('Y-m-d H:i:s', strtotime("+5 min"));
        $result->save();

        return 'true';
    }
    return 'false';
}

function validateDeviceImei($imei) {

    $result = ORM::for_table('devices')->where('imei', $imei)->find_one();

    if (empty($result)) {

        return 'false';
    }

    return 'true';
}

function rand_code($len) {
    $min_lenght = 0;
    $max_lenght = 100;
    $bigL = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $smallL = "abcdefghijklmnopqrstuvwxyz";
    $number = "0123456789";
    $bigB = str_shuffle($bigL);
    $smallS = str_shuffle($smallL);
    $numberS = str_shuffle($number);
    $subA = substr($bigB, 0, 5);
    $subB = substr($bigB, 6, 5);
    $subC = substr($bigB, 10, 5);
    $subD = substr($smallS, 0, 5);
    $subE = substr($smallS, 6, 5);
    $subF = substr($smallS, 10, 5);
    $subG = substr($numberS, 0, 5);
    $subH = substr($numberS, 6, 5);
    $subI = substr($numberS, 10, 5);
    $RandCode1 = str_shuffle($subA . $subD . $subB . $subF . $subC . $subE);
    $RandCode2 = str_shuffle($RandCode1);
    $RandCode = $RandCode1 . $RandCode2;
    if ($len > $min_lenght && $len < $max_lenght) {
        $CodeEX = substr($RandCode, 0, $len);
    } else {
        $CodeEX = $RandCode;
    }
    return $CodeEX;
}
