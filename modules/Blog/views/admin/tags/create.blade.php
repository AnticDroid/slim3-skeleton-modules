@extends('layouts.admin')

@section('content')
<ol class="breadcrumb">
    <li><a href="/admin">Admin</a></li>
    <li><a href="/admin/tags">Tags</a></li>
    <li class="active">Create tag</li>
</ol>

<form method="post" action="/admin/tags">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" value="{{ @$params['name'] }}" class="form-control" name="name" id="name" placeholder="Name">
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" value="{{ @$params['slug'] }}" class="form-control" name="slug" id="slug" placeholder="Slug">
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="/admin/tags" class="btn btn-default">Cancel</a>
</form>
@stop
