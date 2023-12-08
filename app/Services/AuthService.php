<?php

namespace App\Services;

use App\Services\HelperService;

class AuthService {

	/**
	* @var helper_service
	*/
	private $helper_service;

	/**
    * AuthService Constructor
    * @param HelperService
    */
    public function __construct(HelperService $helper_service) {
    	$this->helper_service = $helper_service;
    }
}
