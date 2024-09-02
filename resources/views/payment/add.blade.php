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
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <form id="paymentForm">
                                    <div class="form-submit">
                                        <button type="submit" onclick="payWithPaystack()">Pay with Paystack</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener("submit", payWithPaystack, false);
        function payWithPaystack(e) {
            e.preventDefault();
            let handler = PaystackPop.setup({
                key: "{{ env('PAYSTACK_PUBLIC_KEY') }}",
                email: "codewitharefin@gmail.com",
                amount: 1500,
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Laptop",
                            variable_name: "laptop",
                            value: "Laptop"
                        },
                        {
                            display_name: "Quantity",
                            variable_name: "quantity",
                            value: "1"
                        }
                    ]
                },
                onClose: function(){
                    alert('Window closed.');
                },
                callback: function(response){
                    // let message = 'Payment complete! Reference: ' + response.reference;
                    // alert(message);
                    //alert(JSON.stringify(response));
                    window.location.href = "{{ route('callback') }}" + response.redirecturl;
                }
            });
            handler.openIframe();
        }
    </script>
@endsection
