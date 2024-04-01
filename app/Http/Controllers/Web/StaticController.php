<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppUtils;
use App\Traits\ResponseManager;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaticController extends Controller
{
    use ResponseManager;

    public function exploreScreenData() {
        try {
            $data = AppUtils::where('util_key', 'explore_screen_data')->first();
            if($data && $data?->data) {
                $data = $data->data;
                $data['url'] = config('app.url') . Storage::url(isset ($data['url']) ? $data['url'] : '');
            } else {
                $data = [];
            }

            return $this->jsonSuccess(200, 'Success', $data);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function privacyPolicy() {
        return view('static.privacypolicy');
    }

    public function termsConditions() {
        return view('static.termsconditions');
    }

    public function marketing() {
        return view('static.marketing');
    }
}
