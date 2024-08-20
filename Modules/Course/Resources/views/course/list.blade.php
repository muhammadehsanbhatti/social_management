@section('title', 'Course List')
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
                            <h4 class="card-title">Filter Courses</h4>
                        </div>
                        
                        <div class="card-body">
                            <form method="GET" id="filterForm" action="{{ url('/course') }}">
                                @csrf
                                <input name="page" id="filterPage" value="1" type="hidden">
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" for="select2-course_name">Course Title</label>
                                         <input  type="text" id="title" class="formFilter form-control courseFormFilter" placeholder="Enter course title here" name="title">
                                    </div>
                                </div>
                            </form>
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
