<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    
    public function registerAccount(Request $request)
    {
        $requested_data = $request->all();
        $rules = array(
            'first_name' => 'required|max:8|regex:/^[a-zA-Z0-9 ]+$/u',
            'last_name' => 'required|max:8|regex:/^[a-zA-Z0-9 ]+$/u',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'dob' => 'required|date_format:Y-m-d',
            'phone_number'  => 'required|regex:/^\d{3}-\d{3}-\d{4}$/',
            'register_from'     => 'required|in:Web,Facebook,Gmail,Apple,IOS,Android',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            'confirm_password'  => 'required_with:password|same:password',
        );
        
        $messages = array(
            'first_name.regex' => 'Only letters and numbers are allowed',
            'last_name.regex' => 'Only letters and numbers are allowed',
            'phone_number.regex' => 'Please enter a valid phone number in the format XXX-XXX-XXXX',
            'register_from.in' => 'The selected register from value is invalid you can use only one of these Web/Facebook/Gmail/Apple/IOS/Android',
            'password.regex' => 'Password must be atleast eight characters long and must use atleast one letter, number and special character.',
        );
        $validator = \Validator::make($requested_data, $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }
        else {
            $requested_data['verification_token'] = \Str::random(60);
            $userDetail = $this->UserObj->saveUpdateUser($requested_data);
            
            saveEmailLog([
                'user_id' => $userDetail->id,
                'email_template_id' => 1 //welcome email
            ]);
            saveEmailLog([
                'user_id' => $userDetail->id,
                'email_template_id' => 4 //email verification through link
            ]);

            $userDetail->assignRole('User');
            $response = $userDetail;
            return $this->sendResponse($response, 'User registered successfully. Please check and verify your email, thanks.');

        }
    }
    
    public function loginAccount(Request $request)
    {
        $user_data = array();
        $posted_data = $request->all();

        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );
        
        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }


        $credentials = array();
        $credentials['email'] = $posted_data['email'];
        $credentials['password'] = $posted_data['password'];
        $user = $this->UserObj::whereEmail($posted_data['email'])->first();
        if($user){
            if($user->email_verified_at){
                if(\Auth::attempt($credentials)){
                    // $user['token'] = \Auth::user()->createToken('authToken')->accessToken;
                    $user['token'] =  $user->createToken('MyApp')->accessToken;
                    return $this->sendResponse($user, 'User login successfully.');
                }else{
                    return $this->sendError('Your email and password is not correct.');
                }
            }else{
                return $this->sendError('Please check your email your email is not verified.');
            }
        }
        
        return $this->sendError('Your email and password is not correct.');
    }

    public function logoutUser()
    {
        $user = \Auth::user()->token();
        $user->revoke();
        return $this->sendResponse([], 'User has been logout.');
    }

    public function get_profile()
    {
        $user = $this->UserObj->getUser([
            'id' => \Auth::user()->id,
            'detail' => true
        ]);
        return $this->sendResponse($user, 'User profile is successfully loaded.');
    }
 

    // public function verifyUserEmail($token){

    //     $where_query = array(['remember_token', '=', isset($token) ? $token : 0]);
    //     $verifyUser = User::where($where_query)->first();

    //     $email_data = [
    //         'name' => isset($verifyUser->name) ? $verifyUser->name : 'Dear User',
    //         'text_line' => 'This verfication code is invalid. Please contact to the customer support',
    //     ];
  
    //     if($verifyUser){
    //         if($verifyUser->email_verified_at == NULL) {
                
    //             $model_response = User::saveUpdateUser([
    //                 'update_id' => $verifyUser->id,
    //                 'remember_token' => NULL,
    //                 'email_verified_at' => date('Y-m-d h:i:s')
    //             ]);

    //             if (!empty($model_response)) {
    //                 $email_data = [
    //                     'name' => $verifyUser->name,
    //                     'text_line' => 'Congratulations! You email is successfully verified. Welcome to '.config('app.name'),
    //                 ];
    //             }
    //         }
    //         else {
    //             $email_data = [
    //                 'name' => $verifyUser->name,
    //                 'text_line' => 'Your email is already verified. Welcome to '.config('app.name'),
    //             ];
    //         }
    //     }
    //     return view('emails.general_email', compact('email_data'));
    // }

    public function forgotPassword(Request $request)
    {
        $request_data = $request->all();
        $rules = array(
            'email' => 'required|email|exists:users'
        );

        $messages = array(
            'email.exists' => 'We do not recognize this email address. Please try again.',
        );

        $validator = \Validator::make($request_data, $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors());     
        } else {

        
            $otp = substr(md5(uniqid(rand(), true)), 5, 5); 
            $userDetail = $this->UserObj->getUser([
                'email' => $request_data['email'],
                'detail' => true
            ]);
            
            if($userDetail){
                $response = $this->UserObj->saveUpdateUser([
                    'update_id' => $userDetail->id,
                    'email_verification_code' => $otp,
                ]);
                if($response){
                    saveEmailLog([
                        'user_id' => $response->id,
                        'email_template_id' => 6, //OTP Verification
                        'otp_code' =>$otp
                    ]);
                    return $this->sendResponse($otp, 'Your password has been reset. Please check your email.');
                }
            }

            // if($userDetail){
            //     $response = $this->UserObj->saveUpdateUser([
            //         'update_id' => $userDetail->id,
            //         'password' => $password
            //     ]);
            //     if($response){
            //         saveEmailLog([
            //             'user_id' => $response->id,
            //             'email_template_id' => 5, //email forgot password
            //             'new_password' => $password
            //         ]);
            //         return $this->sendResponse($password, 'Your password has been reset. Please check your email.');
            //     }
            // }


            return $this->sendResponse([], 'We do not recognize this email address. Please try again.');
        }
    }

    public function verifyOtp(Request $request)
    {
        $request_data = $request->all();
        $rules = array(
            'email_verification_code' => 'required',
            'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            'confirm_password'  => 'required_with:new_password|same:new_password',
        );

        $messages = array(
            'new_password.regex' => 'Password must be atleast eight characters long and must use atleast one letter, number and special character.',
        );

        $validator = \Validator::make($request_data, $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors());     
        } else {

            $userDetail = $this->UserObj->getUser([
                'email_verification_code' => $request_data['email_verification_code'],
                'detail' => true
            ]);
            
            if($userDetail){
                $response = $this->UserObj->saveUpdateUser([
                    'update_id' => $userDetail->id,
                    'password' => $request_data['new_password'],
                    'email_verification_code' => 'NULL'
                ]);
                if($response){
                    saveEmailLog([
                        'user_id' => $response->id,
                        'email_template_id' => 5, //email forgot password
                        'new_password' => $request_data['new_password']
                    ]);
                    return $this->sendResponse($request_data['email_verification_code'], 'Your password has been reset. Please check your email.');
                }
            }
            return $this->sendResponse([], 'We do not recognize this email address. Please try again.');
        }
    }


    public function authorizeUser($posted_data)
    {
        $email = isset($posted_data['email']) ? $posted_data['email'] : '';
        $password = isset($posted_data['password']) ? $posted_data['password'] : '';

        if(\Auth::attempt(['email' => $email, 'password' => $password])){ 
            $user = \Auth::user();
            $response =  $user;

            if ( isset($posted_data['mode']) && $posted_data['mode'] == 'only_validate' ) {
                return $response;
            }

            $response['token'] =  $user->createToken('MyApp')->accessToken;
            return $response;
        }
        else{
            return false;
        }
    }

    public function changePassword(Request $request)
    {
        $requested_data = $request->all();
        $rules = array(
            'email'             => 'required|email|exists:users',
            'old_password'      => 'required',
            'new_password'      => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            'confirm_password'  => 'required|required_with:new_password|same:new_password'
        );

        $messages = array(
            'new_password.regex' => 'Password must be atleast eight characters long and must use atleast one letter, number and special character.',
        );
        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), $validator->errors()); 
        }

        $response = $this->authorizeUser([
            'email' => $requested_data['email'],
            'password' => $requested_data['old_password'],
            'mode' => 'only_validate',
        ]);

        if (!$response) {
            return $this->sendError('Your old password is incorrect.');
        }
        else {

            if ($requested_data['old_password'] == $requested_data['new_password']) {
                return $this->sendError('New and old password must be different.');
            }

            $this->UserObj->saveUpdateUser([
                'update_id' => $response->id,
                'password' => $requested_data['new_password']
            ]);

            saveEmailLog([
                'user_id' => $response->id,
                'email_template_id' => 2, //email changed password
                'new_password' => $requested_data['new_password']
            ]);
            
            return $this->sendResponse([], 'Your password has been changed successfully.');
        }
    }
}