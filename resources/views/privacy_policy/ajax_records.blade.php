<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Sr #</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($data['privacy_policy']) && count($data['privacy_policy'])>0)
            @php $any_permission_found = false; @endphp
            @foreach ($data['privacy_policy'] as $key => $item)
                @php
                   $sr_no = $key + 1;
                        if ($data['privacy_policy']->currentPage()>1) {
                            $sr_no = ($data['privacy_policy']->currentPage()-1)*$data['privacy_policy']->perPage();
                            $sr_no = $sr_no + $key + 1;
                        }

                        if ($item['status'] == 'Published')
                            $color = '#455356';
                        else
                            $color = '#b4b5af';
                @endphp

                <tr data-user-id="{{ $item->id }}">
                    <td>{{ $sr_no }}</td>
                    <td>{{ @$item->title }}</td>
                    <td>{{ date('M d, Y H:i A', strtotime($item->created_at)) }}</td>

                    <td>
                        @canany(['privacy-policy-edit', 'privacy-policy-delete', 'privacy-policy-status', 'privacy-policy-detail'])
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
                                    @can('privacy-policy-detail')
                                        <button type="button" class="dropdown-item" data-toggle="modal"
                                            data-target="#privacy_policy-{{ $item->id }}">
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

                                    @can('privacy-policy-edit')
                                        <a class="dropdown-item" href="{{ url('privacy_policy') }}/{{ $item->id }}/edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2 mr-50">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                    @endcan


                                    @can('privacy-policy-delete')
                                        <form action="{{ url('privacy_policy/' . $item['id']) }}" method="post">
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
                <div class="modal fade" id="privacy_policy-{{ $item->id }}" tabindex="-1"
                    aria-labelledby="privacyPolicyModalLabel-{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog   modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="privacyPolicyModalLabel">Privacy policy detail</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="privacyPolicyContent-{{ $item->id }}">
                                    @if ($item->role == 'User')
                                        <div class="privacy_policy_detail">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>Privacy Title</h4>
                                                        <span>{{ $item->title }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>Privacy Policy Description</h4>
                                                        <span>{{ $item->description }}</span>
                                                    </div>

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
        @if (isset($data['privacy_policy']) && count($data['privacy_policy']) > 0)
            {{ $data['privacy_policy']->links('vendor.pagination.bootstrap-4') }}
        @else
            <div class="alert alert-primary">Don't have records!</div>
        @endif

    </div>
</div>
