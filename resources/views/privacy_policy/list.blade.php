@section('title', 'Privacy Policy List')
@extends('layouts.master_dashboard')

@section('content')

<div class="content-wrapper">
    <div class="content-header row">

    </div>
    <div class="content-body">

        <!-- Select2 Start  -->
        <section class="basic-select2">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Filter Privacy Policy</h4>
                        </div>

                        <div class="card-body">
                            {{-- <form method="GET" id="filterForm" action="{{ url('/user') }}">
                                @csrf
                                <input name="page" id="filterPage" value="1" type="hidden">
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" for="select2-search">Search</label>
                                        <input value="" type="text" id="search" placeholder="Search term"  class="formFilter form-control" name="search">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" for="select2-roles">Roles</label>
                                        <select class="formFilter select2 form-select" name="roles" id="select2-roles">
                                            <option value=""> ---- Choose Role ---- </option>
                                            @foreach ($data['all_roles'] as $key => $role_obj)
                                                <option value="{{$role_obj['name']}}">{{$role_obj['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" for="select2-account-status">Account Status</label>
                                        <select class="formFilter select2 form-select" name="user_status" id="select2-account-status">
                                            <option value=""> ---- Choose Status ---- </option>
                                            @foreach (Config::get('constants.statusActiveBlock') as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <label class="form-label" for="orderBy_value">Sort By Value</label>
                                        <select class="formFilter select2 form-select" name="orderBy_value" id="orderBy_value">
                                            <option value=""> ---- Choose an option ---- </option>
                                            <option value="ASC">ASC</option>
                                            <option value="DESC">DESC</option>
                                        </select>
                                    </div>
                                </div>
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Select2 End -->

        @if (Session::has('message'))
            <div class="alert alert-success"><b>Success: </b>{{ Session::get('message') }}</div>
        @endif
        @if (Session::has('error_message'))
            <div class="alert alert-danger"><b>Sorry: </b>{{ Session::get('error_message') }}</div>
        @endif

        <div id="table_data">
            {{ $data['html'] }}
        </div>

    </div>
</div>
@endsection
