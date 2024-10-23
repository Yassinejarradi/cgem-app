@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Les articles</title>
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Gestion de Stock</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">Liste des Articles</li>
                        </ul>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="{{ route('articles.create') }}" class="btn btn-primary"><i class="la la-plus"></i> Ajouter des Articles</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom de l'Article</th>
                            <th>Description</th>
                            <th>Stock réél</th>
                            <th>Stock min</th>
                            <th>Demande en cours</th>
                            <th>Reste</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articlesWithRequests as $article)
                            <tr>
                                <td>{{ $article->id }}</td>
                                <td>{{ $article->name }}</td>
                                <td>{{ $article->description }}</td>
                                <td>{{ $article->stock }}</td>
                                <td>{{ $article->stockmin }}</td>
                                <td>{{ $article->demand }}</td>
                                <td>{{ $article->ad }}</td>
                                <td class="text-right">
                                    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-sm btn-info">Editer</a>
                                    <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>  {{ $articles->links() }}
        </div>
    </div>
@endsection
