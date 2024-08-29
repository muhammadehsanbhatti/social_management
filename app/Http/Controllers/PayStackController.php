<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
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

    public function redirectToGateway(Request $request)
    {


        try {
            $request->validate([
                'amount' => 'required|integer|min:100',
                'email' => 'required|email',
            ]);

            $amount = $request->input('amount');
            $email = $request->input('email');

            // Set up Paystack payment initialization with the amount and email
            $authorizationUrl = Paystack::getAuthorizationUrl([
                'amount' => $amount * 100, // Paystack requires amount in kobo
                'email' => $email,
            ])->getTargetUrl();

            return redirect($authorizationUrl);
        } catch (RequestException $e) {
            Log::error('Guzzle Request Exception: ' . $e->getMessage());
            return redirect()->route('your.failure.route')->with('error_message', 'Something went wrong. Please try again.');
        }


    }
    /**
     * Handle Paystack payment callback
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        if ($paymentDetails['status'] && $paymentDetails['data']['status'] === 'success') {
            // Payment was successful
            // Save the payment details to the database or perform other actions
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
        return view('deposit_fund.add');
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
