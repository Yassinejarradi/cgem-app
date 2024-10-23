@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Les Demandes recues</title>
@section('content')
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Demandes Reçues</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">Demandes Reçues</li>
                        </ul>
                        <a href="{{ route('estimates.showCreateForUser') }}" class="btn btn-primary">Créer une demande pour un utilisateur</a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
         
            <!-- Content -->
            <div class="row">
                <div class="col-md-12">
                    @if($estimates->isEmpty())
                        <p>Aucune demande validée reçue.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Numero de demande</th>
                                        <th>Type de demande</th>
                                        <th>Date de création</th>
                                        <th>Date du besoin</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($estimates as $estimate)
                                        <tr>
                                            <td><a href="{{ url('estimate/view/'.$estimate->estimate_number) }}">{{ $estimate->estimate_number }}</a></td>
                                            <td>{{ $estimate->type_demande }}</td>
                                            <td>{{ \Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d F, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($estimate->expiry_date)->translatedFormat('d F, Y') }}</td>
                                            <td><span class="badge bg-inverse-success">{{ $estimate->status }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Content -->
        </div>
        <!-- /Page Content -->
    </div>
@endsection
