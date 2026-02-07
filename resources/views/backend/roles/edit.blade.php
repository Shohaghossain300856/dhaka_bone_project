@extends('backend.layouts.app')
@section("title")
    | {{$page_title}}
@endsection

@push('style')
    <style>
        /* ===== Permission Checkbox UI ===== */
        .perm-wrap{
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }
        .perm-toolbar{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
            justify-content:space-between;
            margin-bottom:12px;
        }
        .perm-search{
            max-width: 420px;
            width:100%;
        }
        .perm-actions{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .perm-grid{
            display:grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap:10px;
        }
        @media (max-width: 992px){
            .perm-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 576px){
            .perm-grid{ grid-template-columns: repeat(1, minmax(0, 1fr)); }
        }

        .perm-item{
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 10px 12px;
            display:flex;
            align-items:center;
            gap:10px;
            background:#fafafa;
            transition: .15s ease-in-out;
            cursor:pointer;
            user-select:none;
        }
        .perm-item:hover{
            border-color:#dbe4ff;
            background:#f6f8ff;
        }
        .perm-item input[type="checkbox"]{
            width:18px;
            height:18px;
            cursor:pointer;
            flex:0 0 auto;
        }
        .perm-name{
            font-size: 14px;
            font-weight: 600;
            margin:0;
            line-height:1.3;
            word-break: break-word;
        }
        .perm-badge{
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color:#2b3a8a;
            margin-left:auto;
            white-space:nowrap;
            flex:0 0 auto;
        }

        /* error look */
        .perm-wrap.is-invalid{
            border-color: #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{$page_title}}</h5>

                        <div class="dt-action-buttons">
                            <div class="dt-buttons btn-group">
                                <a href="{{route('role.index')}}"
                                   class="btn btn-primary create-new waves-effect waves-light">
                                    <span>
                                        <i class="ti ti-arrow-left me-1"></i>
                                        <span class="d-none d-sm-inline-block">Back</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <form action="{{route('role.update',$role->id)}}" method="post">
                        @csrf
                        @method("PUT")

                        <div class="card-body">
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-warning" role="alert">{{ $error }}</div>
                                @endforeach
                            @endif

                            {{-- Role Name --}}
                            <div class="mb-3">
                                <label for="permission" class="form-label">Role Name <code>*</code></label>
                                <input type="text" name="name" value="{{$role->name}}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="permission" placeholder="Role Name" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ✅ Permissions (dynamic SAME, only UI changed) --}}
                            <div class="mb-3">
                                <label class="form-label">Permissions</label>

                                <div class="perm-wrap @error('permissions') is-invalid @enderror" id="permWrap">
                                    <div class="perm-toolbar">
                                        <input type="text" id="permSearch"
                                               class="form-control perm-search"
                                               placeholder="Search permission...">

                                        <div class="perm-actions">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="checkAllBtn">
                                                Check All (Filtered)
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="uncheckAllBtn">
                                                Uncheck All (Filtered)
                                            </button>
                                        </div>
                                    </div>

                                    <div class="perm-grid" id="permGrid">
                                        @foreach($permissions as $permission)
                                            @php
                                                // ✅ same dynamic logic as your select option selected
                                                $checked = $role->permissions->contains('name', $permission->name);
                                            @endphp

                                            <label class="perm-item perm-row" data-name="{{ strtolower($permission->name) }}">
                                                <input type="checkbox"
                                                       class="perm-check"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                    {{ $checked ? 'checked' : '' }}>

                                                <p class="perm-name mb-0">{{ $permission->name }}</p>

                                                <span class="perm-badge">{{ $checked ? 'Assigned' : 'Not' }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                @error('permissions')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mt-3">
                                <div class="d-grid gap-2 col-lg-4 mx-auto">
                                    <button class="btn btn-primary btn-lg waves-effect waves-light" type="submit">
                                        Update
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@push('script')
    <script>
        (function(){
            const searchEl = document.getElementById('permSearch');
            const rows = Array.from(document.querySelectorAll('.perm-row'));

            function applyFilter(){
                const q = (searchEl.value || '').trim().toLowerCase();
                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    row.style.display = name.includes(q) ? '' : 'none';
                });
            }

            function getVisibleCheckboxes(){
                return rows
                    .filter(r => r.style.display !== 'none')
                    .map(r => r.querySelector('.perm-check'))
                    .filter(Boolean);
            }

            // Search
            searchEl.addEventListener('input', applyFilter);

            // Check all / uncheck all (only filtered)
            document.getElementById('checkAllBtn').addEventListener('click', function(){
                getVisibleCheckboxes().forEach(ch => {
                    ch.checked = true;
                    // badge update
                    const badge = ch.closest('.perm-row')?.querySelector('.perm-badge');
                    if (badge) badge.textContent = 'Assigned';
                });
            });

            document.getElementById('uncheckAllBtn').addEventListener('click', function(){
                getVisibleCheckboxes().forEach(ch => {
                    ch.checked = false;
                    // badge update
                    const badge = ch.closest('.perm-row')?.querySelector('.perm-badge');
                    if (badge) badge.textContent = 'Not';
                });
            });

            // Badge live update
            rows.forEach(row => {
                const chk = row.querySelector('.perm-check');
                const badge = row.querySelector('.perm-badge');
                if(!chk || !badge) return;

                chk.addEventListener('change', () => {
                    badge.textContent = chk.checked ? 'Assigned' : 'Not';
                });
            });

            // init
            applyFilter();
        })();
    </script>
@endpush
