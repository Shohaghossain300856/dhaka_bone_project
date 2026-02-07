@extends('backend.layouts.app')
@section("title") | {{$page_title}} @endsection

@push('style')
    <link rel="stylesheet" href="{{asset('backend')}}/vendor/libs/select2/select2.css" />
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Title -->
                        <h5 class="card-title mb-0 text-capitalize">{{ $page_title }}</h5>

                        <!-- Button Group -->
                        <div class="dt-action-buttons">
                            <div class="dt-buttons btn-group">
                                <!-- Back Button -->
                                <a href="{{ url()->previous() }}" class="btn btn-primary create-new waves-effect waves-light">
                                <span>
                                    <i class="ti ti-arrow-left me-1"></i>
                                    <span class="d-none d-sm-inline-block">Back</span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('setting.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $slug }}">

                        <div class="card-body">
                            <!-- Validation Errors -->
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-warning" role="alert">{{ $error }}</div>
                                @endforeach
                            @endif

                            <div class="row">
                                @foreach($fields as $key => $filed)
                                    <div class="{{ $filed['col'] }} mb-3">
                                        <label for="{{ $key }}" class="form-label text-capitalize">
                                            {{ \Illuminate\Support\Str::of($key)->replace('_', ' ') }}
                                            <code>{{ $filed['required'] ? '*' : '' }}</code>
                                        </label>

                                        {{-- SELECT FIELD --}}
                                        @if($filed['type'] === 'select')
                                            <select name="{{ $key }}" id="{{ $key }}"
                                                    class="form-control @error($key) is-invalid @enderror"
                                                {{ $filed['required'] }}>
                                                <option value="">-- Select --</option>
                                                @foreach($filed['options'] as $optionKey => $optionValue)
                                                    <option value="{{ $optionKey }}"
                                                        {{ old($key, $editData->$key ?? '') == $optionKey ? 'selected' : '' }}>
                                                        {{ $optionValue }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            {{-- RADIO FIELD --}}
                                        @elseif($filed['type'] === 'radio')
                                            @foreach($filed['options'] as $optionKey => $optionValue)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                           name="{{ $key }}"
                                                           id="{{ $key }}_{{ $optionKey }}"
                                                           value="{{ $optionKey }}"
                                                        {{ old($key, $editData->$key ?? '') == $optionKey ? 'checked' : '' }}
                                                        {{ $filed['required'] }}>
                                                    <label class="form-check-label" for="{{ $key }}_{{ $optionKey }}">
                                                        {{ $optionValue }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            {{-- CHECKBOX FIELD --}}
                                        @elseif($filed['type'] === 'checkbox')
                                            @php
                                                // Get the old values or existing data, safely
                                                $checkedValues = old($key, []);
                                                if (isset($editData) && property_exists($editData, $key)) {
                                                    $checkedValues = old($key, (array) $editData->$key);
                                                }
                                            @endphp

                                            @foreach($filed['options'] as $optionKey => $optionValue)
                                                @if($key == 'send_sms')
                                                    @can('sms panel')
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="{{ $key }}[]"
                                                                   id="{{ $key }}_{{ $optionKey }}"
                                                                   value="{{ $optionKey }}"
                                                                {{ in_array($optionKey, $checkedValues) ? 'checked' : '' }}
                                                                {{ $filed['required'] }}>
                                                            <label class="form-check-label" for="{{ $key }}_{{ $optionKey }}">
                                                                {{ $optionValue }}
                                                            </label>
                                                        </div>
                                                    @endcan
                                                @endif

                                            @endforeach

                                            {{-- FILE FIELD --}}
                                        @elseif($filed['type'] === 'file')
                                            <input type="file"
                                                   name="{{ $key }}"
                                                   class="form-control @error($key) is-invalid @enderror"
                                                   id="{{ $key }}"
                                                {{ $filed['required'] }}>

                                            {{-- Optional preview of existing file --}}
                                            @if(isset($editData) && !empty($editData->$key))
                                                <div class="mt-2">
                                                    @if(Str::contains($key, ['logo', 'favicon', 'image', 'photo']))
                                                        <img src="{{ asset($editData->$key) }}" alt="{{ $key }}" height="60">
                                                    @else
                                                        <a href="{{ asset($editData->$key) }}" target="_blank">View current file</a>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- DEFAULT INPUT TYPES --}}
                                        @else
                                            <input type="{{ $filed['type'] }}"
                                                   name="{{ $key }}"
                                                   value="{{ old($key, $editData->$key ?? '') }}"
                                                   class="form-control @error($key) is-invalid @enderror"
                                                   id="{{ $key }}"
                                                   placeholder="{{ $filed['placeholder'] ?? '' }}"
                                                {{ $filed['required'] }}>
                                        @endif

                                        @error($key)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div> {{-- end row --}}

                            <div class="row mt-3">
                                <div class="d-grid gap-2 col-lg-4 mx-auto">
                                    <button class="btn btn-primary btn-lg waves-effect waves-light" type="submit">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{asset('backend')}}/vendor/libs/select2/select2.js"></script>
    <script src="{{asset('backend')}}/js/forms-selects.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolesSelect').select2({
                placeholder: 'Select a role',
                allowClear: true
            });

            $('#permissionsSelect').select2({
                placeholder: 'Select permissions',
                allowClear: true
            });
        });

    </script>
@endpush
