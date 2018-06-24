@extends('layouts.auth')

@section('content')
    <registration :errors="{{ json_encode($errors->all()) }}"></registration>
@endsection
