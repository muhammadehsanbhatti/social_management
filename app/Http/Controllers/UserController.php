<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Laravel\Passport\Token;
use App\Exports\ExportData;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Arr;
use MikeMcLin\WpPassword\Facades\WpPassword;
use App\Models\User;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:user-list|user-edit|user-detail|user-delete|user-status', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit|user-status|edit-profile', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:edit-profile', ['only' => ['editProfile']]);
    }

    public function testing() {
        echo '<pre>';print_r('testing');'</pre>';exit;
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function login()
    {
        return view('auth_v1.login');
    }

    public function register()
    {
        $role = 'User';
        return view('auth_v1.register');
    }
    public function employee_register()
    {
        $role = 'Employee';
        return view('auth_v1.register',compact('role'));
    }

    public function forgotPassword()
    {
        return view('auth_v1.forgot-password');
    }

    public function accountResetPassword(Request $request)
    {
        $request_data = $request->all();
        // if(!isset($request_data['g-recaptcha-response']) || empty($request_data['g-recaptcha-response'])){
        //     return back()->withErrors([
        //         'email' => 'Please check recaptcha and try again.',
        //     ]);
        // }

        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $random_hash = substr(md5(uniqid(rand(), true)), 8, 8);

            $userDetail = $this->UserObj->getUser([
                'email' => $request_data['email'],
                'detail' => true
            ]);

            if($userDetail){
                $response = $this->UserObj->saveUpdateUser([
                    'update_id' => $userDetail->id,
                    'password' => $random_hash,
                ]);
                if($response){
                    saveEmailLog([
                        'user_id' => $response->id,
                        'email_template_id' => 5,
                        'new_password' => $random_hash
                    ]);
                    \Session::flash('message', 'Your password has been changed successfully please check you email!');
                    return redirect('/sp-login');
                }
            }
        }
    }



    public function change_pass()
    {
        return view('auth_v1.reset-password');
    }

    public function authorizeUser($posted_data)
    {
        $email = isset($posted_data['email']) ? $posted_data['email'] : '';
        $password = isset($posted_data['password']) ? $posted_data['password'] : '';

        if (\Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = \Auth::user();
            $response =  $user;

            if (isset($posted_data['mode']) && $posted_data['mode'] == 'only_validate') {
                return $response;
            }

            $response['token'] =  $user->createToken('MyApp')->accessToken;
            return $response;
        } else {
            return false;
        }
    }


    public function reset_pass(Request $request)
    {
        // Validation rules
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            // 'new_password' => [
            //     'required', Password::min(8)
            //         ->letters()
            //         ->mixedCase()
            //         ->numbers()
            //         ->symbols()
            //         ->uncompromised()
            // ],
            'confirm_password' => 'required|same:new_password',
        ];

        // Validate the input data
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the old password is correct
        $user = \Auth::user();
        if (!\Hash::check($request->old_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['old_password' => 'Your old password is incorrect.'])
                ->withInput();
        }

        // Check if the new password is different from the old password
        if ($request->old_password === $request->new_password) {
            return redirect()->back()
                ->withErrors(['new_password' => 'New and old passwords must be different.'])
                ->withInput();
        }

        // Update the user's password
        $user_detail = $this->UserObj->saveUpdateUser([
            'update_id' =>  \Auth::user()->id,
            'password' => $request->new_password,
        ]);
        \Auth::logout();
        \Session::flash('message', 'Your password has been updated. Please log in with your new password.');
        return redirect('/sp-login');
    }


    public function logout()
    {
        if(\Auth::check()){

            $posted_data['device_id'] = \Session::getId();
            $posted_data['user_id'] = \Auth::user()->id;
            $posted_data['detail'] = true;
            $get_notification_tokens = $this->FcnTokenObj->getFcmTokens($posted_data);
            if($get_notification_tokens){
                $this->FcnTokenObj->deleteFCM_Token($get_notification_tokens->id);
            }
            \Auth::logout();

        }
        return redirect('/sp-login');
    }


    public function dashboard(Request $request)
    {
        $data = array();
        $data['counts'] = array();

        $data['counts']['roles'] = $this->RoleObj->getRoles([
            'count' => true
        ]);

        $data['counts']['permissions'] = $this->PermissionObj->getPermissions([
            'count' => true
        ]);

        $data['counts']['users'] = $this->UserObj->getUser([
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['verified_users'] = $this->UserObj->getUser([
            'user_status' => 'Verified',
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);


        $data['counts']['unverified_users'] = $this->UserObj->getUser([
            'user_status' => 'Unverified',
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['pending_users'] = $this->UserObj->getUser([
            'user_status' => 'Pending',
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['block_users'] = $this->UserObj->getUser([
            'user_status' => 'Block',
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['account_verified_users'] = $this->UserObj->getUser([
            'is_email_verification_code' => true,
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['account_not_verified_users'] = $this->UserObj->getUser([
            'is_email_verified_at' => true,
            'role_in' => ['User', 'Employee'],
            'count' => true
        ]);

        $data['counts']['menus'] = $this->MenuObj->getMenus([
            'count' => true
        ]);

        $data['counts']['sub_menus'] = $this->SubMenuObj->getSubMenus([
            'count' => true
        ]);

        $data['counts']['email_messages'] = $this->EmailTemplateObj->getEmailTemplates([
            'count' => true
        ]);

        $data['counts']['short_codes'] = $this->EmailShortCodeObj->getEmailShortCode([
            'count' => true
        ]);




        // echo '<pre>';print_r($data);'</pre>';exit;

        $posted_data = array();
        $posted_data['orderBy_name'] = 'name';
        $posted_data['orderBy_value'] = 'Asc';
        $data['roles'] = $this->RoleObj->getRoles($posted_data);

        return view('dashboard', compact('data'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();
        $data['all_roles'] = $this->RoleObj->getRoles(['roles_not_in' => [1]]);
        $data['assigned_roles'] = \Auth::user()->getRoleNames();

        $posted_data = $request->all();
        $posted_data['paginate'] = 10;
        $posted_data['users_not_in'] = [1];
        if (\Auth::user()->role == 'Employee') {
            $posted_data['users_not'] = 'Employee';
        }
        // $posted_data['printsql'] = true;
        $data['users'] = $this->UserObj->getUser($posted_data);

        $data['html'] = view('user.ajax_records', compact('data'));

        if($request->ajax()){
            return $data['html'];
        }

        return view('user.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array();
        $posted_data = array();
        $posted_data['orderBy_name'] = 'name';
        $posted_data['orderBy_value'] = 'Asc';
        $data['roles'] = $this->RoleObj->getRoles($posted_data);

        return view('user.add',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $posted_data = array();
        $posted_data = $request->all();
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            'email' => 'required|email|unique:users,email',
            'country' => 'required',
            'phone_number' => 'required',
            'dob' => 'required|date|before:today',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg',
            'personal_identity' => 'required|image|mimes:jpeg,png,jpg',
            'role' => 'required|in:Employee,User',
            'description' => 'required',
            'address' => 'required',
        );


        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try{

            $base_url = public_path();
            if($request->file('profile_image')) {
                $extension = $request->profile_image->getClientOriginalExtension();
                if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
                    $imageData = array();
                    // $imageData['fileName'] = time().'_'.$request->profile_image->getClientOriginalName();
                    $imageData['fileName'] = time().'_'.rand(1000000,9999999).'.'.$extension;
                    $imageData['uploadfileObj'] = $request->file('profile_image');
                    $imageData['fileObj'] = \Image::make($request->file('profile_image')->getRealPath());
                    $imageData['folderName'] = 'profile_image';

                    $uploadAssetRes = uploadAssets($imageData, $original = false, $optimized = true, $thumbnail = false);
                    $posted_data['profile_image'] = $uploadAssetRes;
                    if(!$uploadAssetRes){
                        return back()->withErrors([
                            'profile_image' => 'Something wrong with your image, please try again later!',
                        ])->withInput();
                    }
                }else{
                    return back()->withErrors([
                        'profile_image' => 'The Profile image format is not correct you can only upload (jpg, jpeg, png).',
                    ])->withInput();
                }
            }
            if($request->file('personal_identity')) {
                $extension = $request->personal_identity->getClientOriginalExtension();
                if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
                    $imageData = array();
                    $imageData['fileName'] = time().'_'.rand(1000000,9999999).'.'.$extension;
                    $imageData['uploadfileObj'] = $request->file('personal_identity');
                    $imageData['fileObj'] = \Image::make($request->file('personal_identity')->getRealPath());
                    $imageData['folderName'] = 'personal_identity';

                    $uploadAssetRes = uploadAssets($imageData, $original = false, $optimized = true, $thumbnail = false);
                    $posted_data['personal_identity'] = $uploadAssetRes;
                    if(!$uploadAssetRes){
                        return back()->withErrors([
                            'personal_identity' => 'Something wrong with your image, please try again later!',
                        ])->withInput();
                    }
                }else{
                    return back()->withErrors([
                        'personal_identity' => 'The Profile image format is not correct you can only upload (jpg, jpeg, png).',
                    ])->withInput();
                }
            }

            $posted_data['profile_completion'] = 100;
            $posted_data['user_status'] = 'Verified';

            $this->UserObj->saveUpdateUser($posted_data);
            \Session::flash('message', 'Employee created Successfully!');
            return redirect()->back();

        } catch (Exception $e) {
            \Session::flash('error_message', $e->getMessage());
        }
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function theme_layout(Request $request) {

        $posted_data = $request->all();
        $posted_data['update_id'] = \Auth::user()->id;


        if(\Auth::user()->theme_mode == 'Light'){
            $posted_data['theme_mode'] = 'Dark';
        }
        else{
            $posted_data['theme_mode'] = 'Light';
        }
    //    echo '<pre>';print_r($posted_data);echo '</pre>';exit;
    $this->UserObj->saveUpdateUser($posted_data);
        // echo '<pre>';print_r($response);echo '</pre>';exit;
        return response()->json(['message' => 'Data submitted', 'record' => $posted_data]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $posted_data = array();
        $posted_data['id'] = $id;
        $posted_data['detail'] = true;
        $data = $this->UserObj->getUser($posted_data);

        $posted_data = array();
        $posted_data['orderBy_name'] = 'name';
        $posted_data['orderBy_value'] = 'Asc';
        $data['roles'] = $this->RoleObj->getRoles($posted_data);
        $data['user_role'] = count($data->getRoleNames()) > 0 ? $data->getRoleNames()[0] : '';

        return view('user.add',compact('data'));
    }

    public function editProfile()
    {
        $id = \Auth::user()->id;

        $posted_data = array();
        $posted_data['id'] = $id;
        $posted_data['detail'] = true;
        $data = $this->UserObj->getUser($posted_data);

        $posted_data = array();
        $posted_data['orderBy_name'] = 'name';
        $posted_data['orderBy_value'] = 'Asc';
        $data['roles'] = $this->RoleObj->getRoles($posted_data);

        // echo '<pre>';print_r($data);echo '</pre>';exit;
        return view('user.add',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $posted_data = array();
        $posted_data = $request->all();
        $posted_data['update_id'] = $id;
        $user_detail = $this->UserObj->getUser(['id' => $id, 'detail' => true]);
        $rules = array(
            'phone_number' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'last_name' => 'required',
            'first_name' => 'required',
            'dob' => 'required|date|before:today',
            'address' => 'required',
            'country' => 'required',
            'description' => 'required',
        );
        if (!$user_detail->profile_image && !$request->file('profile_image')) {
            $rules['profile_image'] = 'required|image|mimes:jpeg,png,jpg';
        }
        if (!$user_detail->identity_document && !$request->file('identity_document')) {
            $rules['identity_document'] = 'required|mimes:pdf,doc,docx';
        }

        if (!$user_detail->personal_identity && !$request->file('personal_identity')) {
            $rules['personal_identity'] = 'required|image|mimes:jpeg,png,jpg';
        }

        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
            try{



                $base_url = public_path();
                if($request->file('profile_image')) {
                    $extension = $request->profile_image->getClientOriginalExtension();
                    if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
                        $imageData = array();
                        // $imageData['fileName'] = time().'_'.$request->profile_image->getClientOriginalName();
                        $imageData['fileName'] = time().'_'.rand(1000000,9999999).'.'.$extension;
                        $imageData['uploadfileObj'] = $request->file('profile_image');
                        $imageData['fileObj'] = \Image::make($request->file('profile_image')->getRealPath());
                        $imageData['folderName'] = 'profile_image';

                        $uploadAssetRes = uploadAssets($imageData, $original = false, $optimized = false, $thumbnail = false);
                        $posted_data['profile_image'] = $uploadAssetRes;
                        if(!$uploadAssetRes){
                            return back()->withErrors([
                                'profile_image' => 'Something wrong with your image, please try again later!',
                            ])->withInput();
                        }
                        $imageData = array();
                        $imageData['imagePath'] = $user_detail->profile_image;
                        unlinkUploadedAssets($imageData);

                    }else{
                        return back()->withErrors([
                            'profile_image' => 'The Profile image format is not correct you can only upload (jpg, jpeg, png).',
                        ])->withInput();
                    }
                }
                if($request->file('identity_document')) {
                    $extension = $request->identity_document->getClientOriginalExtension();
                    if($extension == 'pdf' || $extension == 'docs'){
                            $file_name = time() . '_' . rand(1000000, 9999999) . '.' . $extension;

                            $filePath = $request->file('identity_document')->storeAs('identity_document', $file_name, 'public');
                            $posted_data['identity_document'] = 'storage/identity_document/' . $file_name;

                            $imageData = array();
                            $imageData['imagePath'] = $user_detail->identity_document;
                            unlinkUploadedAssets($imageData);
                        } else {
                            $error_message['error'] = 'Group Image Only allowled jpg, jpeg or png image format.';
                            return $this->sendError($error_message['error'], $error_message);
                        }
                }


                if($request->file('personal_identity')) {
                    $extension = $request->personal_identity->getClientOriginalExtension();
                    if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
                        $imageData = array();
                        // $imageData['fileName'] = time().'_'.$request->personal_identity->getClientOriginalName();
                        $imageData['fileName'] = time().'_'.rand(1000000,9999999).'.'.$extension;
                        $imageData['uploadfileObj'] = $request->file('personal_identity');
                        $imageData['fileObj'] = \Image::make($request->file('personal_identity')->getRealPath());
                        $imageData['folderName'] = 'personal_identity';

                        $uploadAssetRes = uploadAssets($imageData, $original = false, $optimized = false, $thumbnail = false);
                        $posted_data['personal_identity'] = $uploadAssetRes;
                        if(!$uploadAssetRes){
                            return back()->withErrors([
                                'personal_identity' => 'Something wrong with your image, please try again later!',
                            ])->withInput();
                        }
                        $imageData = array();
                        $imageData['imagePath'] = $user_detail->personal_identity;
                        unlinkUploadedAssets($imageData);

                    }else{
                        return back()->withErrors([
                            'personal_identity' => 'The Profile image format is not correct you can only upload (jpg, jpeg, png).',
                        ])->withInput();
                    }
                }
                if ($user_detail->profile_completion  <= 30) {
                    $posted_data['profile_completion'] = 100;
                }

                $this->UserObj->saveUpdateUser($posted_data);
                \Session::flash('message', 'User Update Successfully!');
                return redirect()->back();

            } catch (Exception $e) {
                \Session::flash('error_message', $e->getMessage());
                // dd("Error: ". $e->getMessage());
            }
            return redirect()->back()->withInput();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function blockUnblockUser(Request $request)
    {
        $posted_data = $request->all();

        $rules = array(
            'update_id' => 'required|exists:users,id',
            'user_status' => 'required'
        );

        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->UserObj->saveUpdateUser($posted_data);

        \Session::flash('message', 'User status updated successfully!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        unlinkUploadedAssets([
            'imagePath' => $user->profile_image
        ]);
        $user->delete();
        \Session::flash('message', 'User deleted successfully!');
        return redirect('/user');

    }

    public function accountLogin(Request $request)
    {
        $posted_data = $request->all();

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'email' => $posted_data['email'],
            'password' => $posted_data['password'],
        ];

        $user = $this->UserObj::whereEmail($posted_data['email'])->first();

        if ($user) {
            // if ($user->email_verified_at) {
                $password = $credentials['password'];
                $wp_hashed_password = $user->password;


                if (WpPassword::check($password, $wp_hashed_password) || $wp_hashed_password == md5($credentials['password'])) {
                    if (\Auth::attempt($credentials)) {
                        if (\Auth::user()->profile_completion < 100 ) {
                            return redirect('/editProfile');
                        }
                        else{
                            \Session::flash('message', 'You logged in successfully');
                            return redirect('/dashboard');
                        }
                    } else {
                        return redirect()->back()->withErrors(['password' => 'Invalid password'])->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors(['password' => 'Invalid password'])->withInput();
                }
            // } else {
            //     return redirect()->back()->withErrors(['email' => 'Please verify your email before logging in.'])->withInput();
            // }
        } else {
            return redirect()->back()->withErrors(['email' => 'This account not found'])->withInput();
        }
    }

    public function accountRegister(Request $request)
    {
        $posted_data = $request->all();
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@^&*()_+]).*$/',
            'confirm_password' => 'required|required_with:password|same:password',
        );


        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            try{
                $posted_data['email_verification_code'] = \Str::random(20);
                $posted_data['profile_completion'] = 30;
                $latest_user = $this->UserObj->saveUpdateUser($posted_data);

                if ($latest_user->role == 'Employee') {
                    $latest_user->assignRole('Employee');
                }
                else{
                    $latest_user->assignRole('User');
                }

                if($latest_user){
                    saveEmailLog([
                        'user_id' => $latest_user->id,
                        'email_template_id' => 4,
                        'email_verification_link' => true
                    ]);
                    \Session::flash('message', 'You Register Successfully!. Kinldy check your email and verify your account');
                    return redirect('/sp-login');
                }

            } catch (Exception $e) {
                \Session::flash('error_message', $e->getMessage());
                // dd("Error: ". $e->getMessage());
            }
            return redirect('/sp-login');
        }
    }

    public function verifyUserEmail($token)
    {

        $where_query = array(['email_verification_code', '=', isset($token) ? $token : 0]);
        $verifyUser = User::where($where_query)->first();

        if ($verifyUser) {
            if (isset($verifyUser->email_verification_code) && !isset($verifyUser->email_verified_at)) {


                $model_response = User::saveUpdateUser([
                    'update_id' => $verifyUser->id,
                    'email_verification_code' => 'NULL',
                    'email_verified_at' => date('Y-m-d h:i:s')
                ]);

                if (!empty($model_response)) {
                    \Session::flash('message', 'Congratulations! You email is successfully verified. Welcome to ' . config('app.name'));
                    return redirect('/sp-login');
                }
            } else {
                $email_data = [
                    'name' => $verifyUser->name,
                    'text_line' => 'Your email is already verified. Welcome to ' . config('app.name'),
                ];
            }
        }
        else{
            return back()->withErrors([
                'error' => 'This verfication code is invalid. Please contact to the customer support',
            ]);
        }

    }

    public function change_status(Request $request){

        $posted_data = array();
        $posted_data = $request->all();

        $data = $this->UserObj->saveUpdateUser([
            'update_id' => $posted_data['userId'],
            'user_status' => $posted_data['status'],
        ]);
        $response = [
            'newStatus' => ucfirst($data->user_status),
            'statusClass' => $this->getStatusClass($data->user_status)
        ];

        return response()->json($response);

    }
    private function getStatusClass($status)
    {
        switch($status) {
            case 'Verified':
                return 'status-verified';
            case 'Unverified':
                return 'status-unverified';
            case 'Pending':
                return 'status-pending';
            case 'Block':
                return 'status-block';
            default:
                return 'status-unverified';
        }
    }


    public function sendNotification() {

        // echo '<pre>';print_r(Session::getId());'</pre>';exit;
        // $token = "fHRRYnyQyrA:APA91bFGF5j4A76XXsC4xb2canvjRPlJqlcL_yKBmgQrOu9egO3Qk9v86Lh5eSE6EQ13DC6qdE4AoxgdFsIYZvv3PtCeNdbtj6zXazZuJKGI6Doxcriw-Zdpd9QnigCD_mDCgz_BA5N7";

        $token = "fBvzndxKvpE:APA91bEc2l-37uRiTcYRulz3vVL63KtqmtwP5Tlm4E8hWvKUVAvfRMHjqb_ony4nHDNxuxmDjbmoPzDcmog2cX5zwL-vCf_CA0bdw8en7mVzdpCOUZeQb8Ne9HVr45LGLu3Nulees_V2";
        $from  = "AAAA1x62L-A:APA91bHPEZuPTTVn8tWhggUur4h2_k92s4cRWIu5L9lkRgS2pHtYJKMgCIkg4UcIMui1lWcXRGStyKxjIgrlH7KXefS0CkSS8tlrR0yDWiNRUkeYsNuivIgnV2rgep6QCmQL75-QpBTd";
        $msg = array
            (
                'body'  => "Testing",
                'title' => "Hi, From Raj1",
                'receiver' => 'erw',
                'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
                'sound' => 'Default'/*Default sound*/
            );

        $fields = array
                (
                    'to'        => $token,
                    'notification'  => $msg
                );

        $headers = array
                (
                    'Authorization: key=' . $from,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        dd($result);
        curl_close( $ch );
        // echo '<pre>';print_r('Notification send successfully');'</pre>';exit;
    }


    public function export_data(Request $request)
    {
        $requested_data = $request->all();

        $data = array();
        if (isset($requested_data['product_id']) || $requested_data['product_id'] == '' )
            $data['product_id'] = $requested_data['product_id'];

        // $export_data = new ExportData();
        // $export_data->set_module = "items";
        // $export_data->set_product_id = $data['product_id'];

        return (new ExportData)
            ->set_module("items")
            ->set_product_id($data['product_id'])
            ->download('items.xlsx');
    }
    public function oauth_authorized() {
        echo'<pre>'; print_r('oauth_authorized'); echo'</pre>';exit;
    }
}
