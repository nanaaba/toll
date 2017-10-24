<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once '../../tollconf/conf.php'; //holds the DB initializations. 
//update for your environment

ORM::configure(array(
    'connection_string' => 'mysql:host=localhost;dbname=toll',
    'username' => 'root',
    'password' => 'password'
));
