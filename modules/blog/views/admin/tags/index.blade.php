@extends('layouts.admin')

@section('content')
<ol class="breadcrumb">
    <li><a href="/admin">Admin</a></li>
    <li class="active">Tags</li>
</ol>

<a href="/admin/tags/create" class="btn btn-default">New tag</a>

<table class="table table-striped">
    <tr>
        <th>Name</th>
        <th>Slug</th>
        <th>Created</th>
        <th>&nbsp;</th>
    </tr>
    @foreach ($tags as $tag)
        <tr>
            <td>{{ $tag->name }}</td>
            <td>{{ $tag->slug }}</td>
            <td>{{ $tag->created_at }}</td>
            <td width="10%" class="text-right">
                <form id="deleteUser" method="POST" action="/admin/tags">
                    <input type="hidden" name="_METHOD" value="DELETE">

                    <a href="/admin/tags/{{ $tag->id }}/edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="#" onclick="$('form#deleteUser').submit(); return false;"><i class="glyphicon glyphicon-trash"></i></a>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<nav>
    <ul class="pagination">
        <li>
            <a href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li>
            <a href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
@stop
