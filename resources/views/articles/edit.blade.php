@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Edit articles</title>
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Modifier l'Article</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
                            <li class="breadcrumb-item active">Modifier l'Article</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('articles.update', $article->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nom de l'Article</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $article->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" required>{{ $article->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="stock">Quantité</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="{{ $article->stock }}" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock min</label>
                            <input type="number" class="form-control" id="stockmin" name="stockmin" value="{{ $article->stockmin }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Mis a jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
