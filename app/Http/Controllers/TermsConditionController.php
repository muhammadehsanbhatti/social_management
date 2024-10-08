<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsConditionController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:terms-condition-list|terms-condition-edit|terms-condition-delete', ['only' => ['index']]);
        $this->middleware('permission:terms-condition-create', ['only' => ['create','store']]);
        $this->middleware('permission:terms-condition-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:terms-condition-delete', ['only' => ['destroy']]);
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
        $data['terms_condition'] = $this->TermsConditionObj->getTermsCondition($posted_data);
        $data['html'] = view('terms_condition.ajax_records', compact('data'));

        if($request->ajax()){
            return $data['html'];
        }
        return view('terms_condition.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('terms_condition.add');
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
            $this->TermsConditionObj->saveUpdateTermsCondition($posted_data);
            \Session::flash('message', 'Terms Condition Added Successfully!');
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
        $data = $this->TermsConditionObj->getTermsCondition($posted_data);

        return view('terms_condition.add',compact('data'));
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

            $this->TermsConditionObj->saveUpdateTermsCondition($posted_data);
            \Session::flash('message', 'Terms & Condition Update Successfully!');
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
