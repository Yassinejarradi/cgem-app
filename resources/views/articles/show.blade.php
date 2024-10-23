@extends('layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Détails de l'Article</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
                            <li class="breadcrumb-item active">Détails de l'Article</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $article->name }}</h4>
                            <p class="card-text">{{ $article->description }}</p>
                            <p class="card-text"> {{ $article->stock }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
