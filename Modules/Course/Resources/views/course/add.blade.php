
@if (isset($data->id))
    @section('title', 'Update Course')
@else
    @section('title', 'Add Course')
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
                            <h4 class="card-title">{{ isset($data->id)? 'Update':'Add' }} Course Detail</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success"><b>Success: </b>{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger"><b>Sorry: </b>{{ Session::get('error_message') }}</div>
                            @endif

                            @if (isset($data->id))
                                <form class="form course_form_submit" action="{{ route('course.update', $data->id) }}" method="post" enctype="multipart/form-data">
                                @method('PUT')
                                <input type="hidden" name="update_id" id="update_id" value="{{$data->id}}">
                                
                            @else
                                <form class="form course_form_submit" action="{{ route('course.store') }}" method="POST" enctype="multipart/form-data">
                                
                            @endif
                                @csrf
                                <div class="row">   
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input value="{{old('title', isset($data->title)? $data->title: '')}}" type="text" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter course title here" name="title" required>
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="duration">Duration</label>
                                            <input value="{{old('duration', isset($data->duration)? $data->duration: '')}}" type="text" id="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Enter course duration here" name="duration" required>
                                            @error('duration')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12 lesson_status_div">
                                        <label for="cover_image">Upload Cover Image</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text basic-addon">
                                                    <div class="display_images preview_cover_image">
                                                        @if (isset($data->cover_image) && !empty($data->cover_image))
                                                            <a data-fancybox="demo" data-src="{{ is_image_exist($data->cover_image) }}"><img title="{{ $data->title }}" src="{{ is_image_exist($data->cover_image) }}" height="100"></a>
                                                        @endif
                                                    </div>
                                                </span>
                                                </div>
                                            <input type="file" id="cover_image" data-img-val="preview_cover_image" class="form-control @error('cover_image') is-invalid @enderror" placeholder="Profile Image" name="cover_image">
                                            @error('cover_image')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12 lesson_status_div">
                                        <div class="form-group">
                                            <label for="source">Upload Pdf</label>
                                             @if (isset($data->course_asset) && !empty($data->course_asset))
                                                <a class="icons" href="{{ asset($data->course_asset) }}" target="_blank" style="float:right" download>
                                                    <i data-feather="download" class="font-medium"></i>
                                                </a>
                                            @endif
                                            <input type="file" id="course_asset" data-img-val="preview_source" class="form-control @error('course_asset') is-invalid @enderror" name="course_asset">
                                            @error('course_asset')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="short_description">Short Description</label>
                                             <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description"  rows="5" id="placeOfDeath" value="{{old('short_description', isset($data->short_description)? $data->short_description: '')}}"  placeholder="Enter course short description here">{!! (isset($data->short_description)? $data->short_description: '') !!}</textarea>
                                            {{-- <input value="{{old('short_description', isset($data->short_description)? $data->short_description: '')}}" type="text" id="short_description" class="form-control @error('short_description') is-invalid @enderror" placeholder="Short description" name="short_description" required> --}}
                                            @error('short_description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">{{ isset($data->id)? 'Update':'Add' }}</button>
                                        <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
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
