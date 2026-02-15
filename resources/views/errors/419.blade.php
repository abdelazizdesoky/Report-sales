@extends('errors.layout')

@section('code', '419')
@section('title', 'انتهت صلاحية الجلسة')
@section('message', 'نعتذر، لقد انتهت صلاحية الجلسة بسبب الخمول لفترة طويلة. يرجى تحديث الصفحة والمحاولة مرة أخرى.')

@section('icon-bg', 'bg-gradient-to-br from-indigo-400 to-indigo-600')

@section('icon')
<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection
