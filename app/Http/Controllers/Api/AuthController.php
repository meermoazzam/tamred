<?php

namespace App\Http\Controllers\Api;

use App\Mail\EmailVerification;
use App\Mail\ForgotPassword;
use App\Models\User;
use App\Models\UserMeta;
use Exception;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiController
{
	/**
	* @var auth_service, helper_service
	*/
	private $auth_service;

	/**
    * @param AuthService
    */
    public function __construct(AuthService $auth_service) {
    	$this->auth_service = $auth_service;
    }

    public function signup(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'nickname' => 'max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'confirmed|required',
            ]);

            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->errors()
                ], 422);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nickname' => $request->nickname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
    	}
    }

    public function login(Request $request): Response
    {
    	try {
	    	$validator = Validator::make($request->all(), [
	    		'email' => 'required|email',
	    		'password' => 'required'
	    	]);
	    	if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
	    	} else {
	    		if(!Auth::attempt($request->only(['email', 'password']))){
	    		    return response()->json([
                        'status' => false,
                        'message' => 'Email or Password does not match with our records',
                    ], 401);
				} else {
                    $user = User::where('email', $request->email)->first();
                    return response()->json([
                        'status' => true,
                        'message' => 'User Logged In Successfully',
                        'token' => $user->createToken("API TOKEN")->plainTextToken
                    ], 200);
                }
	    	}
    	} catch (Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
    	}
    }

    public function logout(Request $request)
    {
		try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'message' => 'User Logged out Successfully',
            ], 204);
        } catch (Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
    	try {
	    	$validator = Validator::make($request->all(), [
	    		'password' => 'confirmed|required'
	    	]);
	    	if($validator->fails()) {
				return response()->json([
					'status' => false,
					'message' => 'Validation failed',
					'errors' => $validator->errors()
				], 422);
	    	} else {
	    		$updatedUser = User::where('id', auth()->id())->update(['password' => Hash::make($request->password)]);
				return response()->json([
					'status' => true,
					'message' => 'Password Updated Successfully!',
				], 200);
	    	}
    	} catch (Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
    	}
    }

    public function forgotPassword(Request $request)
    {
    	try {
	    	$validator = Validator::make($request->all(), [
	    		'email' => 'required|email'
	    	]);
	    	if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
	    	} else {
	    		$user = User::where('email', $request->email)->first();
                if($user) {
                    $otp = random_int(1000, 9999);
                    $message = 'Please use ' . $otp . ' as your OTP for Resetting password';
                    $emailStatus = Mail::to($user)->send(new ForgotPassword($user->name, $message));

                    if($emailStatus) {
                        $userMeta = UserMeta::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'meta_key' => 'reset_password_otp',
                            ],[
                                'meta_value' => $otp
                            ]
                        );

                        return response()->json([
                            'status' => true,
                            'message' => 'OTP sent at Email: ' . $user->email,
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Failed to send OTP at Email ' . $user->email,
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Could not verify user against email: ' . $request->email,
                    ], 400);
                }
	    	}
    	} catch(Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
    	}
    }

    public function resetPassword(Request $request)
    {
    	try {
	    	$validator = Validator::make($request->all(), [
	    		'email' => 'required|email',
	    		'otp' => 'required',
	    		'password' => 'confirmed|required'
	    	]);
	    	if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
	    	} else {
                $user = User::where('email', $request->email)->first();
                if($user) {
                    $userMeta = UserMeta::where('user_id', $user->id)
                    ->where('meta_key', 'reset_password_otp')
                    ->where('meta_value', $request->otp)
                    ->first();

                    if($userMeta) {
                        $updatedUser = User::where('id', $user->id)->update(['password' => Hash::make($request->password)]);
                        $updatedUserMeta = UserMeta::where('user_id', $user->id)->where('meta_key', 'reset_password_otp')->update(['meta_value' => null]);
                        return response()->json([
                            'status' => true,
                            'message' => 'Password reset successfully!',
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'OPT did not match',
                        ], 422);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Could not verify user against email: ' . $request->email,
                    ], 400);
                }
	    	}
    	} catch(Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function requestEmailVerification(Request $request)
    {
    	try {
            $user = auth()->user();
	    	if ($user->email_verified_at) {
                return response()->json([
                    'status' => true,
                    'message' => 'Email address already verified',
                ], 200);
            } else {
                $otp = random_int(1000, 9999);
                $message = 'Please use ' . $otp . ' as your OTP for verifying Email';
                $emailStatus = Mail::to($user)->send(new EmailVerification($user->name, $message));

                if($emailStatus) {
                    $userMeta = UserMeta::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'meta_key' => 'verify_email_otp',
                        ],[
                            'meta_value' => $otp
                        ]
                    );

                    return response()->json([
                        'status' => true,
                        'message' => 'OTP sent at Email: ' . $user->email,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to send OTP at Email ' . $user->email,
                    ], 500);
                }
	    	}
    	} catch(Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
    	}
    }

    public function emailVerification(Request $request)
    {
    	try {
            $user = auth()->user();
	    	$validator = Validator::make($request->all(), [
	    		'otp' => 'required',
	    	]);
	    	if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
	    	} else {
                $userMeta = UserMeta::where('user_id', $user->id)
                ->where('meta_key', 'verify_email_otp')
                ->where('meta_value', $request->otp)
                ->first();

                if($userMeta) {
                    $updatedUser = User::where('id', $user->id)->update(['email_verified_at' => now()]);
                    $updatedUserMeta = UserMeta::where('user_id', $user->id)->where('meta_key', 'verify_email_otp')->update(['meta_value' => null]);
                    return response()->json([
                        'status' => true,
                        'message' => 'Email verified successfully!',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'OPT did not match',
                    ], 422);
                }
            }
    	} catch(Exception $exception) {
			return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

}
