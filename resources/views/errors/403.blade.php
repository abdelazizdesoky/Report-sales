@extends('errors.layout')

@section('title', 'وصول غير مصرح به')
@section('message', 'ليس لديك الصلاحيات الكافية للوصول إلى هذه الصفحة. يرجى التواصل مع المسؤول إذا كنت تعتقد أن هذا خطأ.')

@section('icon-bg', 'bg-amber-100 dark:bg-amber-900/30')
@section('icon-color', 'text-amber-600 dark:text-amber-400')

@section('icon')
<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
@endsection
