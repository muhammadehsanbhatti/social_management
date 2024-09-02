@if (isset($data->id))
    @section('title', 'Update Deposit fund')
@else
    @section('title', 'Add Deposit fund')
@endif
@extends('layouts.master_dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="container">
                    <div class="alert alert-success">
                        <h4>Payment Successful</h4>
                        <p>Thank you for your payment. Here are the details of your transaction:</p>
                        <ul>
                            <li><strong>Amount:</strong> {{ $paymentDetails['data']['amount'] / 100 }} {{ $paymentDetails['data']['currency'] }}</li>
                            <li><strong>Transaction Reference:</strong> {{ $paymentDetails['data']['reference'] }}</li>
                            <li><strong>Status:</strong> {{ $paymentDetails['data']['status'] }}</li>
                            <!-- Add more details as needed -->
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
