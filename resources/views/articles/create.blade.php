@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Creation Articles</title>
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Ajouter un Article</h3>
                       
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('articles.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nom de l'Article</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Quantité</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Stock min</label>
                            <input type="number" name="stockmin" class="form-control" required>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
