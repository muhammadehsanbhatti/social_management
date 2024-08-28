<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Sr #</th>
                {{-- <th>Status</th> --}}
                <th>Profile Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone No</th>
                <th>Role</th>
                <th>Status</th>
                <th>Selfie With ID</th>
                <th>ID Card Identification</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $any_permission_found = false; @endphp
            @foreach ($data['users'] as $key => $item)
                @if ($item->hasRole('Admin'))
                    @continue
                @endif
                @php
                    $sr_no = $key + 1;
                    if ($data['users']->currentPage() > 1) {
                        $sr_no = ($data['users']->currentPage() - 1) * $data['users']->perPage();
                        $sr_no = $sr_no + $key + 1;
                    }
                @endphp

                <tr data-user-id="{{ $item->id }}">
                    <td>{{ $sr_no }}</td>
                    <td>
                        <div class="display_images_list">

                            <span class="avatar-color">
                                <a data-fancybox="demo" data-src="{{ is_image_exist($item->profile_image) }}">
                                    <img title="{{ $item->name }}" src="{{ is_image_exist($item->profile_image) }}"
                                        height="100">

                                    @if (Cache::has('user-is-online' . $item->id))
                                        <span class="avatar-status-online"></span>
                                    @else
                                        <span class="avatar-status-offline"></span>
                                    @endif
                                </a>
                            </span>

                        </div>
                    </td>

                    <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->phone_number }}</td>
                    <td>
                        <span class="role-badge {{ $item->role == 'Employee' ? 'role-employee' : 'role-user' }}">
                            {{ $item->role }}
                        </span>
                    </td>

                    <td>
                        @php
                            $statusClass = '';
                            switch ($item->user_status) {
                                case 'Verified':
                                    $statusClass = 'status-verified';
                                    break;
                                case 'Unverified':
                                    $statusClass = 'status-unverified';
                                    break;
                                case 'Pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'Block':
                                    $statusClass = 'status-block';
                                    break;
                                default:
                                    $statusClass = 'status-unverified';
                                    break;
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}"
                            data-user-id="{{ $item->id }}">{{ ucfirst($item->user_status) }}</span>
                    </td>
                    <td>
                        <div class="display_images_list">

                            <span class="avatar-color">
                                <a data-fancybox="demo" data-src="{{ is_image_exist($item->personal_identity) }}">
                                    <img title="{{ $item->name }}"
                                        src="{{ is_image_exist($item->personal_identity) }}" height="100">
                                </a>
                            </span>

                        </div>
                    </td>
                    <td>
                        <div class="display_images_list">

                            <span class="avatar-color">
                                <a href="{{ asset($item->identity_document) }}"
                                    download="{{ basename($item->identity_document) }}"
                                    title="{{ $item->identity_document }}">
                                    <button type="button" class="download_button">Download</button>
                                </a>
                            </span>

                        </div>
                    </td>
                    <td>{{ date('M d, Y H:i A', strtotime($item->created_at)) }}</td>

                    <td>
                        @canany(['user-edit', 'user-delete', 'user-status', 'user-detail'])
                            <div class="dropdown">
                                {{-- @if ($item->hasRole('Admin')) --}}
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
                                    @can('user-status')
                                        {{-- <form action="{{ url('user_change_status')}}" method="Post" enctype="multipart/form-data">
                                    @method('POST') --}}
                                        {{-- @csrf --}}
                                        {{-- <input type="hidden" name="update_id" value="{{$item->id}}">
                                        <input type="hidden" name="user_status" value="2"> --}}

                                        <button type="button" class="dropdown-item change-status-btn" data-toggle="modal"
                                            data-target="#statusModal-{{ $item->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-user-x mr-50">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="8.5" cy="7" r="4"></circle>
                                                <line x1="18" y1="8" x2="23" y2="13"></line>
                                                <line x1="23" y1="8" x2="18" y2="13"></line>
                                            </svg>
                                            <span>Change Status</span>
                                        </button>
                                        {{-- </form> --}}
                                    @endcan
                                    @can('user-detail')
                                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#userDetailModal-{{ $item->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye mr-50">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <span>View Detail</span>
                                        </button>
                                    @endcan

                                    @can('user-edit')
                                        <a class="dropdown-item" href="{{ url('user') }}/{{ $item->id }}/edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2 mr-50">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                    @endcan


                                    @can('user-delete')
                                        <form action="{{ url('user/' . $item['id']) }}" method="post">
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

                <div class="modal fade" id="userDetailModal-{{ $item->id }}" tabindex="-1"
                    aria-labelledby="userDetailModalLabel-{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog   modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="userDetailModalLabel">User Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Content will be loaded here dynamically -->
                                <div id="userDetailContent-{{ $item->id }}">
                                    @if($item->role == 'User')
                                        <div class="user_detail">
                                            <div class="user_detail_heading pt-2 pb-2"  style="text-align: center;">
                                                <h4 style="display: inline-block; border: 2px solid #be97ff; border-radius: 4px; padding: 10px;margin: 0;">User Detail</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>User First Name</h4>
                                                        <span>{{ $item->first_name }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>User Last Name</h4>
                                                        <span>{{ $item->last_name }}</span>
                                                    </div>

                                                </div>
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>Email</h4>
                                                        <span>{{ $item->email }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>Phone</h4>
                                                        <span>{{ $item->phone_number }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>DOB</h4>
                                                        <span>{{ $item->dob }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>Status</h4>
                                                        <span>{{ $item->user_status }}</span>
                                                    </div>

                                                </div>
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>Email Verified At</h4>
                                                        <span>{{ $item->email_verified_at }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>User last login</h4>
                                                        <span>{{ $item->last_seen }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>Role</h4>
                                                        <span>{{ $item->role }}</span>
                                                    </div>
                                                    <div class="user_info_div mb-2">
                                                        <h4>Register From</h4>
                                                        <span>{{ $item->register_from }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="user_info_div mb-2">
                                                        <h4>Country</h4>
                                                        <span>{{ $item->country }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->role == 'Employee' || isset($item->employee_id))
                                    <div class="employee_detail">
                                        <div class="employee_detail_heading pt-2 pb-2" style="text-align: center;">
                                            <h4 style=" display: inline-block; border: 2px solid #be97ff;border-radius: 4px; padding: 10px;margin: 0;">Employee Detail</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>User First Name</h4>
                                                    <span>{{ $item->first_name }}</span>
                                                </div>
                                                <div class="user_info_div mb-2">
                                                    <h4>User Last Name</h4>
                                                    <span>{{ $item->last_name }}</span>
                                                </div>

                                            </div>
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>Email</h4>
                                                    <span>{{ $item->email }}</span>
                                                </div>
                                                <div class="user_info_div mb-2">
                                                    <h4>Phone</h4>
                                                    <span>{{ $item->phone_number }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>DOB</h4>
                                                    <span>{{ $item->dob }}</span>
                                                </div>
                                                <div class="user_info_div mb-2">
                                                    <h4>Status</h4>
                                                    <span>{{ $item->user_status }}</span>
                                                </div>

                                            </div>
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>Email Verified At</h4>
                                                    <span>{{ $item->email_verified_at }}</span>
                                                </div>
                                                <div class="user_info_div mb-2">
                                                    <h4>User last login</h4>
                                                    <span>{{ $item->last_seen }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>Role</h4>
                                                    <span>{{ $item->role }}</span>
                                                </div>
                                                <div class="user_info_div mb-2">
                                                    <h4>Register From</h4>
                                                    <span>{{ $item->register_from }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="user_info_div mb-2">
                                                    <h4>Country</h4>
                                                    <span>{{ $item->country }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                </div>

                            </div>
                        </div>



                        <div class="modal fade" id="statusModal-{{ $item->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="statusModalLabel-{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="statusModalLabel-{{ $item->id }}">Change User
                                            Status</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <select id="statusSelect-{{ $item->id }}" name="user_status"
                                            class="form-control">
                                            <option value="Pending"
                                                {{ $item->user_status == 'Pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="Verified"
                                                {{ $item->user_status == 'Verified' ? 'selected' : '' }}>Verified
                                            </option>
                                            <option value="Unverified"
                                                {{ $item->user_status == 'Unverified' ? 'selected' : '' }}>Unverified
                                            </option>
                                            <option value="Block"
                                                {{ $item->user_status == 'Block' ? 'selected' : '' }}>
                                                Block</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary saveStatusBtn"
                                            data-user-id="{{ $item->id }}">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
            @endforeach
        </tbody>
    </table>




    <div class="pagination_links">
        @if (isset($data['users']) && count($data['users']) > 0)
            {{ $data['users']->links('vendor.pagination.bootstrap-4') }}
        @else
            <div class="alert alert-primary">Don't have records!</div>
        @endif

    </div>
</div>

@section('user_change_status')
    <script>
        $(document).ready(function() {

            function showUserDetails(userId) {
                // Optionally, you can show a loader here
                document.getElementById('userDetailContent').innerHTML = 'Loading...';

                // Make an AJAX request to fetch the user details
                fetch(`/user/${userId}/edit`)
                    .then(response => response.text())
                    .then(data => {
                        // Update modal content with fetched data
                        document.getElementById('userDetailContent').innerHTML = data;

                        // Show the modal
                        var userDetailModal = new bootstrap.Modal(document.getElementById(
                            'userDetailModal'), {});
                        userDetailModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching user details:', error);
                        document.getElementById('userDetailContent').innerHTML =
                            'Error loading details. Please try again later.';
                    });
            }


            $('.saveStatusBtn').click(function() {
                var userId = $(this).data('user-id');
                var selectedStatus = $('#statusSelect-' + userId).val();

                console.log(userId);
                console.log(selectedStatus);

                $.ajax({
                    url: "{{ route('user_status') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        userId: userId,
                        status: selectedStatus
                    },
                    success: function(response) {
                        var userRow = $('tr[data-user-id="' + userId + '"]');

                        var statusBadge = userRow.find('.status-badge');
                        statusBadge.text(response.newStatus);
                        statusBadge.removeClass().addClass('status-badge ' + response
                            .statusClass);

                        $('#statusModal-' + userId).modal('hide');
                    },
                    error: function(xhr) {
                        alert('Something went wrong! Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
