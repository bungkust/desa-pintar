@extends('layouts.app')

@section('content')
    <x-layouts.article-layout
        :title="$post->title"
        :back-url="route('berita') ?? '/'"
        back-text="Kembali ke Berita"
        :image="$post->thumbnail ? (str_starts_with($post->thumbnail, 'http://') || str_starts_with($post->thumbnail, 'https://') ? $post->thumbnail : Storage::url($post->thumbnail)) : null"
        :image-alt="$post->title">
        <x-slot name="meta">
            @if($post->published_at)
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->locale('id')->isoFormat('D MMMM YYYY') }}
                </time>
            @endif
        </x-slot>

                {!! str($post->content)->sanitizeHtml() !!}
    </x-layouts.article-layout>
@endsection
