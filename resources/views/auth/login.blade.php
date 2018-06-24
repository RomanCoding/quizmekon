@extends('layouts.auth')

@section('content')
    <login :errors="{{ json_encode($errors->all()) }}"></login>
@endsection
