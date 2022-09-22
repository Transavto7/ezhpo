@extends('layouts.app')

@section('title', 'Подсказки полей')
@section('sidebar', 1)
@section('content')
    @php
        $current_user_permissions = [
            'permission_to_edit' => user()->access('field_prompt_edit'),
            'permission_to_create' => user()->access('field_prompt_create'),
            'permission_to_delete' => user()->access('field_prompt_delete'),
            'permission_to_trash' => user()->access('field_prompt_trash'),
        ];
    @endphp
    <admin-prompt-index
        :permissions='@json($current_user_permissions)'
        :types='@json($types)'
        :fields='@json($fields)'
        :selfprompts='@json($prompts)'
    ></admin-prompt-index>
@endsection
