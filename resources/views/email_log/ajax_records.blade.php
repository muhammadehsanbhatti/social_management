<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Sr #</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>Email Subject</th>
                <th>Email Status</th>
                <th>Email SentAt</th>
                <th>Email SentAfter</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($data['email_log']) && count($data['email_log'])>0)
            @php $any_permission_found = false; @endphp
            @foreach ($data['email_log'] as $key => $item)
                @php
                   $sr_no = $key + 1;
                        if ($data['email_log']->currentPage()>1) {
                            $sr_no = ($data['email_log']->currentPage()-1)*$data['email_log']->perPage();
                            $sr_no = $sr_no + $key + 1;
                        }

                        if ($item['email_status'] == 'Pending')
                            $color = '#455356';
                        else
                            $color = '#b4b5af';
                @endphp

                <tr data-user-id="{{ $item->id }}">
                    <td>{{ $sr_no }}</td>
                    <td>{{ @$item->userDetails->first_name .' '. @$item->userDetails->last_name  }}</td>
                    <td>{{ @$item->email }}</td>
                    <td>{{ @$item->email_subject }}</td>
                    <td style="background_color: {{ $color }}">{{ @$item->email_status }}</td>
                    <td>{{ @$item->send_at }}</td>
                    <td>{{ @$item->send_email_after }}</td>
                    <td>{{ date('M d, Y H:i A', strtotime($item->created_at)) }}</td>

                    <td>
                        @canany(['email-log-edit', 'email-log-delete', 'email-log-status', 'email-log-detail'])
                            <div class="dropdown">
                                <button type="button"
                                    class="btn btn-sm dropdown-toggle hide-arrow waves-effect waves-float waves-light"
                                    data-toggle="dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-more-vertical">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                @php $any_permission_found = true; @endphp
                                <div class="dropdown-menu">
                                    @can('email-log-detail')
                                        <button type="button" class="dropdown-item" data-toggle="modal"
                                            data-target="#email_log-{{ $item->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-eye mr-50">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <span>View Detail</span>
                                        </button>
                                    @endcan

                                    @can('email-log-edit')
                                        <a class="dropdown-item" href="{{ url('email_log') }}/{{ $item->id }}/edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2 mr-50">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                    @endcan


                                    @can('email-log-delete')
                                        <form action="{{ url('email_log/' . $item['id']) }}" method="post">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="dropdown-item" id="delButton" style="width:100%">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-trash mr-50">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
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
                <div class="modal fade" id="email_log-{{ $item->id }}" tabindex="-1"
                    aria-labelledby="userDetailModalLabel-{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog   modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="userDetailModalLabel">Email Log detail</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Content will be loaded here dynamically -->
                                <div id="userDetailContent-{{ $item->id }}">
                                    @if ($item->role == 'User')
                                        <div class="user_detail">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    {{-- <div class="user_info_div mb-2">
                                                        <h4>User First Name</h4>
                                                        <span>{{ $item->first_name }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>User Last Name</h4>
                                                        <span>{{ $item->last_name }}</span>
                                                    </div> --}}

                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

@endif
        </tbody>
    </table>
    <div class="pagination_links">
        @if (isset($data['email_log']) && count($data['email_log']) > 0)
            {{ $data['email_log']->links('vendor.pagination.bootstrap-4') }}
        @else
            <div class="alert alert-primary">Don't have records!</div>
        @endif

    </div>
</div>
