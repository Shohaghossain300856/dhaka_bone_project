@extends('backend.layouts.app')
@section("title") | {{$page_title}} @endsection

@push('style')
    <link rel="stylesheet" href="{{asset('backend')}}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{asset('backend')}}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{asset('backend')}}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />

@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <!-- Title -->
                <h5 class="card-title mb-0">{{$page_title}}</h5>

                @can('user-create')
                <!-- Button Group -->
                <div class="dt-action-buttons">
                    <div class="dt-buttons btn-group">
                        <!-- Create Button -->
                        <a href="{{route('user.create')}}" class="btn btn-primary create-new waves-effect waves-light">
                            <span>
                                <i class="ti ti-plus me-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </span>
                        </a>
                    </div>
                </div>
                @endcan
            </div>

            <div class="card-datatable text-nowrap">
		<div class="table-responsive mb-3">
                <table class="table custom-datatable border-top">
                    <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Permission</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td><span
                                class="user-popover"
                                data-bs-toggle="popover"
                                data-bs-trigger="hover"
                                data-bs-placement="top"
                                data-bs-html="true"
                                data-bs-content="
                                   <p><strong>Permissions:</strong></p>
                                    @foreach($user->permissions as $permission)
                                        {{ $permission->name }}<br>
                                    @endforeach
                                ">
                                {{ $user->name }}
                            </span></td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone}}</td>
                            <td>{{$user->roles->pluck('name')}}</td>
                            <td>
                                <span class="badge bg-{{$user->status == 1 ? 'success' : 'danger'}}">{{$user->status ? 'Active' : 'Deactivated'}}</span>
                            </td>
                            <td>
                                @can('permission-edit')
                                    <a href="{{route('user.edit',$user)}}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('permission-delete')
                                    <button type="button"  data-id="{{ $user->id }}" class="btn btn-danger delete_button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    <!-- Add more rows as required -->
                    </tbody>
                </table>
		</div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    Are you sure you want to proceed with this action?
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <form action="{{ route('user.destroy', 0) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" id="delete_id" class="delete_id" value="0">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-danger deleteButton"><i class="fa fa-trash"></i> DELETE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
<script src="{{asset('backend')}}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{asset('backend')}}/vendor/libs/datatables-bs5/custom-datatable5.js"></script>

<script>
    $(document).ready(function () {
        $(document).on("click", '.delete_button', function (e) {
            var id = $(this).data('id');
            var url = '{{ route("user.destroy",":id") }}';
            url = url.replace(':id',id);
            $("#deleteForm").attr("action",url);
            $("#delete_id").val(id);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize all popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>
@endpush
