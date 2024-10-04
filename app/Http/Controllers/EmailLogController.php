<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailLogController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:email-log-list|email-log-edit|email-log-delete', ['only' => ['index']]);
        $this->middleware('permission:email-log-create', ['only' => ['create','store']]);
        $this->middleware('permission:email-log-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:email-log-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posted_data = $request->all();
        $posted_data['orderBy_name'] = 'email_logs.id';
        $posted_data['orderBy_value'] = 'DESC';
        $posted_data['paginate'] = 10;
        $data['email_log'] = $this->EmailObj->getEmailLogs($posted_data);
        $data['html'] = view('email_log.ajax_records', compact('data'));

        if($request->ajax()){
            return $data['html'];
        }
        return view('email_log.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('email_log.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_data = $request->all();
        // echo '<pre>';print_r($request_data);'</pre>';exit;
        $rules = array(
            'subject' => 'required',
            'send_on' => 'required',
            'message' => 'required'
        );

        $validator = \Validator::make($request_data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $email_send_on_detail = $this->EmailObj->getEmailLogs([
                'detail' => true,
                'send_on' => $request_data['send_on']
            ]);

            if ($email_send_on_detail && $email_send_on_detail->send_on == $request_data['send_on']) {
                \Session::flash('error_message', '"'.$request_data['send_on'].'" Email template already exists, if you want to update than go and edit in email template!');
                return redirect()->back()->withInput();
            }else{
                \Session::flash('message', 'Email Template created successfully!');
                $this->EmailObj->saveUpdateEmailLog($request_data);
                return redirect('/email_template');
            }
        }
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
        //
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
        //
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
