<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:privacy-policy-list|privacy-policy-edit|privacy-policy-delete', ['only' => ['index']]);
        $this->middleware('permission:privacy-policy-create', ['only' => ['create','store']]);
        $this->middleware('permission:privacy-policy-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:privacy-policy-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();
        $posted_data = array();
        $posted_data = $request->all();
        $posted_data['paginate'] = 10;
        $data['privacy_policy'] = $this->PrivacyPolicyObj->getPrivacyPolicy($posted_data);
        $data['html'] = view('privacy_policy.ajax_records', compact('data'));

        if($request->ajax()){
            return $data['html'];
        }
        return view('privacy_policy.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('privacy_policy.add');
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
            'title' => 'required',
            'description' => 'required',
        );

        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try{
            $this->PrivacyPolicyObj->saveUpdatePrivacyPolicy($posted_data);
            \Session::flash('message', 'Privacy Policy Added Successfully!');
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
    public function show($id)
    {
        //
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
        $data = $this->PrivacyPolicyObj->getPrivacyPolicy($posted_data);

        return view('privacy_policy.add',compact('data'));
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
        $rules = array(
            'title' => 'required',
            'description' => 'required',
        );

        $validator = \Validator::make($posted_data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try{

            $this->PrivacyPolicyObj->saveUpdatePrivacyPolicy($posted_data);
            \Session::flash('message', 'Privacy policy Update Successfully!');
            return redirect()->back();

        } catch (Exception $e) {
            \Session::flash('error_message', $e->getMessage());
        }
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
