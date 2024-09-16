@if (isset($data->id))
    @section('title', 'Update Privacy Policy')
@else
    @section('title', 'Add Privacy Policy')
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
                                    {{ !isset($data->user_role) && isset($data->id) ? 'Profile' : 'Privacy Policy Detail' }}</h4>
                            </div>
                            <div class="card-body">
                                @if (Session::has('message'))
                                    <div class="alert alert-success"><b>Success: </b>{{ Session::get('message') }}</div>
                                @endif
                                @if (Session::has('error_message'))
                                    <div class="alert alert-danger"><b>Sorry: </b>{{ Session::get('error_message') }}</div>
                                @endif

                                @if (isset($data->id))
                                    <form class="form" action="{{ route('privacy_policy.update', $data->id) }}" method="post"
                                        enctype="multipart/form-data">
                                        @method('PUT')
                                    @else
                                        <form class="form" action="{{ route('privacy_policy.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="title">Enter privacy policy title</label>
                                            <input
                                                value="{{ old('title', isset($data->title) ? $data->title : '') }}"
                                                type="text" id="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="Last Name" name="title">
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label for="description">Enter privacy policy description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" name="description"  rows="5" id="placeOfDeath" value="{{old('description', isset($data->description)? $data->description: '')}}"  placeholder="Enter course short description here">{!! (isset($data->description)? $data->description: '') !!}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit"
                                            class="btn btn-primary mr-1 waves-effect waves-float waves-light">{{ isset($data->id) ? 'Update' : 'Add' }}</button>
                                        <button type="reset"
                                            class="btn btn-outline-secondary waves-effect">Reset</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
