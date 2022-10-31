@extends('layouts.default')
@section('content')
    Number of pages crawled: {{ $number_pages_crawled }}<br>
    Number of unique images: {{ $number_unique_images }}<br>
    Number of unique internal links: {{ $number_unique_internal_links }}<br>
    Number of unique external links: {{ $number_unique_external_links }}<br>
    Number of average page load in seconds: {{ $average_page_load_in_seconds }}<br>
    Number of average word count: {{ $average_word_count }}<br>
    Number of average title length: {{ $average_title_length }}<br>
    <table border="1">
        <thead>
            <tr>
                <th>Page</td>
                <th>Status</td>
            </tr>
        </thead>
    @foreach ($pages as $page)
        <tr>
            <td>{{ $page->path }}</td>
            <td>{{ $page->status }}</td>
        </tr>

    @endforeach
    </table>
    <a href="{{ url('/') }}">Home</a>
@stop