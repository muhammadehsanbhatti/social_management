<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Sr #</th>
                {{-- <th>Status</th> --}}
                <th>Course Title</th>
                <th>Short Description</th>
                <th>Duration</th>
                <th>PDF Source </th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $any_permission_found = false; @endphp
            @foreach ($data['courses'] as $key => $item)
                @php
                    $sr_no = $key + 1;
                    if ($data['courses']->currentPage()>1) {
                        $sr_no = ($data['courses']->currentPage()-1)*$data['courses']->perPage();
                        $sr_no = $sr_no + $key + 1;
                    }
                @endphp

                <tr>
                    <td>{{ $sr_no }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->short_description ? \Illuminate\Support\Str::words($item->short_description, 10, ' .....') : '------' }}</td>
                    <td>{{ $item->duration }}</td>
                    <td style="display: inline-flex; padding-top: 20px;">
                        @if($item->course_asset)
                            <a class="icons svg_btns" href="{{ asset($item->course_asset) }}" target="_blank" download> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </a>
                            <a class="icons svg_btns" id="viewer" data-href="{{ asset($item->course_asset) }}" href="#" data-title="Course Source File">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                        @else
                        ---------
                        @endif
                    </td>
                    <td>{{ date('M d, Y H:i A', strtotime($item->created_at)) }}</td>
                    
                    <td>
                        @canany(['course-update', 'course-delete'])
                        <div class="dropdown">
                            {{-- @if ( $item->hasRole('Admin') ) --}}
                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow waves-effect waves-float waves-light" data-toggle="dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                </button>
                                @php $any_permission_found = true; @endphp
                                <div class="dropdown-menu">
                                    @can('course-update')
                                    <a class="dropdown-item" href="{{ url('course')}}/{{$item->id}}/edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 mr-50"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                        <span>Edit</span>
                                    </a>
                                    @endcan
                                    
                                    @can('course-delete')
                                    <form action="{{ url('course/'.$item['id']) }}" method="post">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="dropdown-item" id="delButton" style="width:100%">

                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash mr-50">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                            <span>Delete</span>

                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            {{-- @endif --}}
                        </div>
                        @endcanany
                        @if (!$any_permission_found)
                            {{ 'Not Available' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="pagination_links">
        {{-- {!! $data['users']->links() !!} --}}
        @if (isset($data['courses']) && count($data['courses'])>0)
            {{ $data['courses']->links('vendor.pagination.bootstrap-4') }}
        @else
            <div class="alert alert-primary">Don't have records!</div>
        @endif

    </div>

</div>