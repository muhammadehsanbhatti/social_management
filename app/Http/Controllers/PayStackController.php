<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Paystack;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class PayStackController extends Controller
{

    // function __construct()
    // {
    //     parent::__construct();
    //     $this->middleware('permission:deposit-fund-list|deposit-fund-edit|deposit-fund-delete', ['only' => ['index']]);
    //     $this->middleware('permission:deposit-fund-create', ['only' => ['create','store']]);
    //     $this->middleware('permission:deposit-fund-edit', ['only' => ['edit','update']]);
    //     $this->middleware('permission:deposit-fund-delete', ['only' => ['destroy']]);
    // }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function redirectToGateway()
     {
        try {
            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch(\Exception $e) {
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>'; exit;
            \Log::error('Paystack Error: ' . $e->getMessage());
            return Redirect::back()->withMessage(['msg' => 'The paystack token has expired. Please refresh the page and try again.', 'type' => 'error']);
        }
     }

     /**
      * Obtain Paystack payment information
      * @return void
      */
     public function handleGatewayCallback()
     {
         $paymentDetails = Paystack::getPaymentData();

         dd($paymentDetails);
         // Now you have the payment details,
         // you can store the authorization_code in your db to allow for recurrent subscriptions
         // you can then redirect or do whatever you want
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


}
