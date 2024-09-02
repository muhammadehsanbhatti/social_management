<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
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
            \Log::error('Paystack Error: ' . $e->getMessage());
            return Redirect::back()->withMessage(['msg' => 'The paystack token has expired. Please refresh the page and try again.', 'type' => 'error']);
        }
     }

     /**
      * Obtain Paystack payment information
      * @return void
      */
      public function handleGatewayCallback(Request $request)
      {
        echo '<pre>'; print_r($request->all()); echo '</pre>'; exit;
        try {
            // Get the transaction reference from Paystack (or URL parameters)
            $reference = request()->query('reference'); // Make sure Paystack returns a 'reference' query parameter

            if (!$reference) {
                // If no reference is found, redirect back with an error
                return redirect()->route('home')->with('error', 'Transaction reference not found.');
            }

            // Fetch the payment details using Paystack's transaction verification endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            ])->get('https://api.paystack.co/transaction/verify/' . $reference);

            $paymentDetails = $response->json(); // Convert the response to an array

            // Log payment details for debugging
            Log::info('Payment Details: ', $paymentDetails);

            // Check if the request was successful
            if ($response->successful() && $paymentDetails['data']['status'] === 'success') {
                // Handle successful payment
                return redirect()->route('home')->with('success', 'Payment was successful!');
            } else {
                // Payment failed or status is not successful
                return redirect()->route('home')->with('error', 'Payment verification failed. Please try again.');
            }
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error verifying payment: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'There was an error processing your payment.');
        }
      }

      public function callback(Request $request)
      {
          dd($request->all());
          $reference = $request->reference;
          $secret_key = env('PAYSTACK_SECRET_KEY');
          $curl = curl_init();
          curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                  "Authorization: Bearer $secret_key",
                  "Cache-Control: no-cache",
              ),
          ));
          $response = curl_exec($curl);
          curl_close($curl);
          $response = json_decode($response);
          //dd($response);
          $meta_data = $response->data->metadata->custom_fields;
          if($response->data->status == 'success')
          {
              $obj = new Payment;
              $obj->payment_id = $reference;
              $obj->product_name = $meta_data[0]->value;
              $obj->quantity = $meta_data[1]->value;
              $obj->amount = $response->data->amount / 100;
              $obj->currency = $response->data->currency;
              $obj->payment_status = "Completed";
              $obj->payment_method = "Paystack";
              $obj->save();
              return redirect()->route('success');
          } else {
              return redirect()->route('cancel');
          }
      }

      public function success()
      {
          return "Payment is successful";
      }
      public function cancel()
      {
          return "Payment is cancelled";
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
