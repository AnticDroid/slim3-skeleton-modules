@extends('layouts.admin')

@section('content')
    <ol class="breadcrumb">
        <li><a href="/admin">Admin</a></li>
        <li class="active">Articles</li>
    </ol>

    <form action="/admin/articles" method="post">
        <button type="submit" class="btn btn-default">New article</button>
    </form>

    <table class="table table-striped">
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Published</th>
            <th>&nbsp;</th>
        </tr>
        @foreach ($articles as $article)
            <tr>
                <td><a href="/admin/articles/{{ $article->id }}">{{ $article->title }}</a></td>
                <td>{{ $article->type }}</td>
                <td>{{ $article->published_at }}</td>
                <td width="10%" class="text-right">
                    @if ($article->featured)
                        <a href="#"><i class="glyphicon glyphicon-star"></i></a>
                    @else
                        <a href="#"><i class="glyphicon glyphicon-star-empty"></i></a>
                    @endif
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
