@extends('layouts.admin')

@section('content')
<ol class="breadcrumb">
    <li><a href="/admin">Admin</a></li>
    <li class="active">Users</li>
</ol>

<table class="table table-striped">
    <tr>
        <th>First name</th>
        <th>Last name</th>
        <th>Role</th>
        <th>Email</th>
        <th>Created</th>
        <th>&nbsp;</th>
    </tr>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->first_name }}</td>
            <td>{{ $user->last_name }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at }}</td>
            <td width="10%" class="text-right">
                <form id="deleteUser" method="POST" action="/admin/users/{{ $user->id }}">
                    <input type="hidden" name="_METHOD" value="DELETE">

                    <a href="/admin/users/{{ $user->id }}/edit"><i class="glyphicon glyphicon-edit"></i></a>
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
