<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Paystack;

class PayStackController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:deposit-fund-list|deposit-fund-edit|deposit-fund-delete', ['only' => ['index']]);
        $this->middleware('permission:deposit-fund-create', ['only' => ['create','store']]);
        $this->middleware('permission:deposit-fund-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:deposit-fund-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        // Process webhook payload and update the database accordingly
    }

    public function redirectToGateway()
    {
        request()->validate([
            'amount' => 'required|integer|min:100',
            'email' => 'required|email',
        ]);

        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Handle Paystack payment callback
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        // Now you have the payment details, you can process the payment information, save to DB, etc.
        // Example:
        if ($paymentDetails['status'] && $paymentDetails['data']['status'] === 'success') {
            // Payment was successful
            // Save the payment details to the database
            // Display a success message to the user
            return redirect()->route('your.success.route')->with('message', 'Payment successful!');
        }

        return redirect()->route('your.failure.route')->with('error_message', 'Payment failed. Please try again.');
    }



    public function index()
    {
        return view('deposit_fund.add');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo '<pre>'; print_r("SDfasdfs"); echo '<pre>'; exit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
