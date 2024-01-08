<?php

namespace App\Services;

use App\Services\HelperService;
use App\Traits\ResponseManager;

class Service {

    use ResponseManager;

	/**
	* @var helperService
	*/
	private $helperService;
	/**
    * Service Constructor
    * @param HelperService
    */
    public function __construct(HelperService $helperService) {
    	$this->helperService = $helperService;
    }
}
