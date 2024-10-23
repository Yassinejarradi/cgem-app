@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Dashboard</title>
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Tableau de bord</h3>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Widget -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Total des demandes</h3>
                            <p>{{ $totalEstimates }}</p>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes commandées</h3>
                            <p></p>
                        </div>
                    </div>
                    </div> -->
                
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes validées</h3>
                            <p>{{ $validatedEstimates }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes refusées</h3>
                            <p>{{ $rejectedEstimates }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes réceptionnées</h3>
                            <p></p>
                        </div>
                    </div>
                </div> -->
<!--             
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes livrées</h3>
                            <p>{{ $livrerEstimates }}</p>
                        </div>
                    </div>
                </div> -->
            
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Demandes en cours</h3>
                            <p>{{ $pendingEstimates }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <!-- Recent Estimates -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Mes Demandes récentes</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Numéro de demande</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentEstimates as $estimate)
                                            <tr>
                                                <td><a href="{{ url('estimate/view/'.$estimate->estimate_number) }}">{{ $estimate->estimate_number }}</a></td>
                                                <td>{{ $estimate->type_demande }}</td>
                                                <td>{{ \Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d F, Y') }}</td><td>
                                                    <span class="badge 
                                                        @if($estimate->status == 'Validée') status-validée
                                                        @elseif($estimate->status == 'Refusée') status-refusée
                                                        @elseif($estimate->status == 'En cours') status-en-cours
                                                        @elseif($estimate->status == 'livrer') status-validée
                                                        @elseif($estimate->status == 'Validation partielle') status-validée
                                                        @else text-secondary
                                                        @endif">
                                                        {{ $estimate->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Recent Estimates -->
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#delete_estimate').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var estimateId = button.data('id')
            var estimateNumber = button.data('number')
            var modal = $(this)
            modal.find('.modal-body .e_id').val(estimateId)
            modal.find('.modal-body .estimate_number').val(estimateNumber)
        })
    });
</script>
@endsection
