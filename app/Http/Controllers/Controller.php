<?php

namespace App\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
	* @var helperService
	*/
	protected $helperService;

	/**
    * @param HelperService
    */
    public function __construct(HelperService $helperService) {
    	$this->helperService = $helperService;
    }

}
