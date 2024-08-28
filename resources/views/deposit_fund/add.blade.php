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
                            <div class="card-header">
                                <h4 class="card-title">{{ isset($data->id) ? 'Update' : 'Add' }}
                                    {{ !isset($data->user_role) && isset($data->id) ? 'Deposit Fund' : 'User Detail' }}</h4>
                            </div>
                            <div class="card-body">
                                @if (Session::has('message'))
                                    <div class="alert alert-success"><b>Success: </b>{{ Session::get('message') }}</div>
                                @endif
                                @if (Session::has('error_message'))
                                    <div class="alert alert-danger"><b>Sorry: </b>{{ Session::get('error_message') }}</div>
                                @endif

                                <form method="POST" action="{{ route('pay') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" name="amount" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Pay Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
