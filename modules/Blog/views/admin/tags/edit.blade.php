@extends('layouts.admin')

@section('content')
    <ol class="breadcrumb">
        <li><a href="/admin">Admin</a></li>
        <li><a href="/admin/tags">Tags</a></li>
        <li class="active">Edit tag</li>
    </ol>

    <form method="post" action="/admin/tags/{{ $tag->id }}">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" value="{{ $tag->name }}" class="form-control" name="name" id="name" placeholder="Name">
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" value="{{ $tag->slug }}" class="form-control" name="slug" id="slug" placeholder="Slug">
        </div>

        <input type="hidden" name="_METHOD" value="PUT">

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="/admin/tags" class="btn btn-default">Cancel</a>
    </form>
@stop
