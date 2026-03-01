@extends('backend.layouts.app')

@section('content')
    <bonedetailshow :bone='@json($bone)'></bonedetailshow>
@endsection