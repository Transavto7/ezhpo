@extends('errors.error')

@section('title', 'Ошибка! Упс')

@section('message', "422 | {$exception->getMessage()}")

