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
                                <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="email" value="otemuyiwa@gmail.com">
                                    <input type="hidden" name="orderID" value="345">
                                    <input type="hidden" name="amount" value="80000">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="currency" value="NGN">
                                    <input type="hidden" name="metadata" value="{{ json_encode(['key_name' => 'value']) }}">
                                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">

                                    <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
                                        <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
