@extends('layouts.admin')

@section('content')
    <ol class="breadcrumb">
        <li><a href="/admin">Admin</a></li>
        <li><a href="/admin/data">Data</a></li>
        <li class="active">Import</li>
    </ol>

    <form method="post" action="/admin/data/import" enctype="multipart/form-data">

        <div class="homebox">
            <div class="body">
                <div class="form-group">
                    <input type="file" name="file"><br>
                </div>

                <button type="submit" class="btn btn-default">Import</button>
            </div>
        </div>

    </form>
@stop
