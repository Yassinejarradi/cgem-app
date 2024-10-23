@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View</title>
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="page-header">
                            <div class="row justify-content-center">
                                <div class="col text-center">
                                    <div class="title">
                                        <h1>{{ $estimate->type_demande === 'fourniture' ? 'Demande de Fourniture' : 'Demande d\'Achat' }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Radio Button for Gérer -->
                        @if($isAcheteur)
                            @if($estimate->status != 'Livré')
                            <div class="form-group" id="manage-section">
                                <label for="manageToggle" class="font-weight-bold mr-2">Gérer:</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary {{ $isManager ? 'active' : '' }}">
                                        <input type="radio" name="manageToggle" value="yes" {{ $isManager ? 'checked' : '' }}> Oui
                                    </label>
                                    <label class="btn btn-outline-secondary {{ !$isManager ? 'active' : '' }}">
                                        <input type="radio" name="manageToggle" value="no" {{ !$isManager ? 'checked' : '' }}> Non
                                    </label>
                                </div>
                                @if ($manager)
                                    <p class="mt-2">Actuellement géré par: <strong>{{ $manager->name }}</strong></p>
                                @endif
                            </div>
                            @endif
                        @endif

                    </div>
                    
                    <div class="col-auto float-right ml-auto no-print">
                        <!-- <div class="btn-group btn-group-sm" id="main-buttons" style="{{ !$isManager ? 'display:none;' : '' }}"> -->
                        <div class="btn-group btn-group-sm" id="main-buttons" >

                            @if($isValidator && !$hasValidated && $estimate->status != 'Refusée')
                                <form id="validate-form" action="{{ route('estimates.validate', ['estimate_number' => $estimate->estimate_number]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" id="validate-button">Valider</button>
                                </form>
                                <form id="refuse-form" action="{{ route('estimates.refuse', ['estimate_number' => $estimate->estimate_number]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger mr-4" id="refuse-button">Refuser</button>
                                </form>
                            @endif

                            @if($isAcheteur)
                                <div class="mr-2" style="{{ !$isManager ? 'display:none;' : '' }}">
                                    @if($estimate->type_demande === 'achat')
                                        @if($estimate->status == 'Validée')
                                            <button type="button" class="btn btn-outline-primary" id="commande-button">Commander</button>
                                        @elseif($estimate->status == 'Commandé')
                                            <button type="button" class="btn btn-outline-success" id="receive-button">Réceptionner</button>
                                        @elseif($estimate->status == 'Reçu')
                                            <button type="button" class="btn btn-outline-success" id="livrer-button">Livrer</button>
                                        @endif
                                    @elseif($estimate->type_demande === 'fourniture')
                                        @if($estimate->status == 'Validée')
                                            <button type="button" class="btn btn-outline-success" id="livrer-button">Livrer</button>
                                        @endif
                                    @endif
                                    @if($estimate->status != 'Livré')
                                    <button class="btn btn-outline-danger no-print" id="main-refuse-button">Refuser</button>
                                    @endif
                                </div>
                                <!-- @if($estimate->status != 'Livré')
                                    <button class="btn btn-outline-danger no-print" id="main-refuse-button">Refuser</button>
                                @endif -->
                            @endif
                        </div>
                        <button class="btn btn-outline-primary no-print" id="print-button"><i class="fa fa-print fa-lg"></i> Imprimer</button>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body print-section">
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <img src="{{ URL::to('assets/img/logo2.png') }}" class="inv-logo" alt="Logo">
                                    <ul class="list-unstyled">
                                        <li><strong>Nom:</strong> {{ $estimate->user->name }}</li>
                                        <li><strong>Département:</strong> {{ $estimate->user->department }}</li>
                                        <li><strong>Status:</strong> {{ $estimate->status }}</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <h3 class="text-uppercase">Demande #{{ $estimate->estimate_number }}</h3>
                                    <ul class="list-unstyled">
                                        <li><strong>Date de création:</strong> {{ \Carbon\Carbon::parse($estimate->estimate_date)->locale('fr')->isoFormat('LL') }}</li>
                                        <li><strong>Date souhaitée:</strong> {{ \Carbon\Carbon::parse($estimate->expiry_date)->locale('fr')->isoFormat('LL') }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-hover" style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Article</th>
                                            <th class="d-none d-sm-table-cell">Description</th>
                                            <th>Quantité</th>
                                            <th>Motif de demande</th>
                                            @if($isAcheteur && $isManager)
                                                <th>Status</th>
                                                @if($estimate->type_demande === 'achat')
                                                    @if($estimate->status != 'Livré')
                                                        <th>Actions</th>
                                                    @endif
                                                @endif
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($estimatesJoin as $key => $item)
                                            <tr style="height: auto;">
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $item->item }}</td>
                                                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ strtok($item->description, "\n") }}
                                                    @if(strpos($item->description, "\n") !== false)
                                                        <span class="text-primary show-more" style="cursor:pointer;" data-description="{{ $item->description }}">... voir plus</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->qty }}</td>
                                                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ strtok($item->motif, "\n") }}
                                                    @if(strpos($item->motif, "\n") !== false)
                                                        <span class="text-primary show-more-motif" style="cursor:pointer;" data-motif="{{ $item->motif }}">... voir plus</span>
                                                    @endif
                                                </td>
                                                @if($isAcheteur && $isManager)
                                                    <td>{{ $item->status }}</td>
                                                    @if($estimate->status != 'Livré')
                                                        @if($estimate->type_demande === 'achat')
                                                        <td class="item-action-buttons">
                                                            <div class="dropdown">
                                                                <a href="#" class="action-icon" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    @if($item->status == 'Validée')
                                                                        <a class="dropdown-item commande-item-button" href="#" data-item-id="{{ $item->id }}">Commander</a>
                                                                    @elseif($item->status == 'Commandé')
                                                                        <a class="dropdown-item receive-item-button" href="#" data-item-id="{{ $item->id }}">Réceptionner</a>
                                                                    @elseif($item->status == 'Reçu')
                                                                        <a class="dropdown-item livrer-item-button" href="#" data-item-id="{{ $item->id }}">Livrer</a>
                                                                    @endif
                                                                    @if($estimate->status != 'Livré')
                                                                        <a class="dropdown-item refuse-item-button" href="#" data-item-id="{{ $item->id }}">Refuser</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($estimate->type_demande === 'achat')
                                <hr>
                                <h4 class="mb-4">Détails de la demande:</h4>
                                @foreach ($estimateDetails as $type => $details)
                                    <p><strong>{{ $type }}:</strong> {{ implode(', ', $details->pluck('detail_value')->toArray()) }}</p>
                                @endforeach
                                <hr>
                            @endif

                            @if ($estimate->type_demande === 'achat')
                                <h4>Actions par les validateurs</h4>
                                @foreach ($estimateActions as $action)
                                    <p>
                                        La demande a été {{ $action->action === 'validated' ? 'validée' : 'refusée' }} par {{ $action->user->name }}, 
                                        le {{ \Carbon\Carbon::parse($action->created_at)->locale('fr')->isoFormat('LL à HH:mm:ss') }}.
                                    </p>
                                @endforeach
                                
                            @endif
                            @if (isset($acheteurActions) && $acheteurActions->count() > 0)
                                    <hr>
                                    <h4>Actions par l'acheteur</h4>
                                    @foreach ($acheteurActions as $acheteurAction)
                                        <p>
                                            La demande a été {{ $acheteurAction->action }} par {{ $acheteurAction->acheteur->name }}, 
                                            le {{ \Carbon\Carbon::parse($acheteurAction->created_at)->locale('fr')->isoFormat('LL à HH:mm:ss') }}.
                                        </p>
                                    @endforeach
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for showing full description -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Description Complète</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="fullDescription" style="white-space: pre-wrap;"></pre>
                    <pre id="fullMotif" style="white-space: pre-wrap;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <iframe id="printFrame" style="display:none;"></iframe>

    @section('script')
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-section {
                margin: 0;
                padding: 0;
            }
        }

        #manage-section {
            margin-bottom: 20px;
        }

        #manage-section .btn-group-toggle .btn {
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #manage-section .btn-group-toggle .btn.active {
            background-color: #007bff;
            color: white;
        }

        .current-manager-info {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }

        .current-manager-info strong {
            color: #007bff;
        }

        .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }

        .modal-title {
            margin: 0;
            line-height: 1.5;
            font-weight: bold;
        }

        .modal-body {
            padding: 20px;
            background-color: #ffffff;
            max-height: 500px;
            overflow-y: auto;
        }

        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
        }

        .modal-lg {
            max-width: 800px;
        }

        .show-more, .show-more-motif {
            color: #007bff;
            cursor: pointer;
            font-weight: bold;
            text-decoration: underline;
        }

        .show-more:hover, .show-more-motif:hover {
            text-decoration: none;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Toggle button visibility based on radio selection
            $('input[name="manageToggle"]').on('change', function() {
                var action = $(this).val() === 'yes' ? 'set' : 'unset';
                updateManageBy(action);
            });

            function updateManageBy(action) {
                var estimateNumber = "{{ $estimate->estimate_number }}";
                $.ajax({
                    type: 'POST',
                    url: "{{ route('estimates.updateManageBy', ['estimate_number' => $estimate->estimate_number]) }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Manage_by field updated successfully.');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            console.error('Error updating manage_by field:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating manage_by field:', error);
                    }
                });
            }

            $('.show-more, .show-more-motif').on('click', function() {
                var description = $(this).data('description') || $(this).data('motif');
                $('#fullDescription').text('');
                $('#fullMotif').text('');
                if ($(this).hasClass('show-more')) {
                    $('#fullDescription').text(description);
                } else if ($(this).hasClass('show-more-motif')) {
                    $('#fullMotif').text(description);
                }
                $('#descriptionModal').modal('show');
            });

            $('#validate-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var form = $(this);
                handleFormSubmit(form, 'Validation échouée');
            });

            $('#refuse-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var form = $(this);
                handleFormSubmit(form, 'Refus échouée');
            });

            $('#livrer-button').on('click', function() {
                var url = "{{ route('estimates.deliver', ['estimate_number' => $estimate->estimate_number]) }}";
                handleButtonClick(url, 'Êtes-vous sûr de vouloir livrer ces articles?', 'Livraison échouée');
            });
            $('#receive-button').on('click', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var url = "{{ route('estimates.receive', ['estimate_number' => $estimate->estimate_number]) }}";
                handleButtonClick(url, 'Êtes-vous sûr de vouloir réceptionner ces articles?', 'Réception échouée');
            });

            $('#commande-button').on('click', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var url = "{{ route('estimates.commande', ['estimate_number' => $estimate->estimate_number]) }}";
                handleButtonClick(url, 'Êtes-vous sûr de vouloir commander ces articles?', 'Commande échouée');
            });

            $('#main-refuse-button').on('click', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var url = "{{ route('estimates.annuler', ['estimate_number' => $estimate->estimate_number]) }}";
                handleButtonClick(url, 'Êtes-vous sûr de vouloir refuser ces articles?', 'Annulation échouée');
            });

            $('#print-button').on('click', function() {
                var printFrame = document.getElementById('printFrame');
                var printContentUrl = "{{ route('estimate.print', ['estimate_number' => $estimate->estimate_number]) }}";
                printFrame.src = printContentUrl;

                printFrame.onload = function() {
                    printFrame.contentWindow.print();
                };
            });

            // Item-specific actions with AJAX
            $('.commande-item-button').on('click', function() {
                var itemId = $(this).data('item-id');
                var url = "{{ route('estimates.commandeItem', ['estimate_number' => $estimate->estimate_number, 'item_id' => ':item_id']) }}".replace(':item_id', itemId);
                handleButtonClick(url, 'Êtes-vous sûr de vouloir commander cet article?', 'Commande échouée');
            });

            $('.receive-item-button').on('click', function() {
                var itemId = $(this).data('item-id');
                var url = "{{ route('estimates.receiveItem', ['estimate_number' => $estimate->estimate_number, 'item_id' => ':item_id']) }}".replace(':item_id', itemId);
                handleButtonClick(url, 'Êtes-vous sûr de vouloir réceptionner cet article?', 'Réception échouée');
            });

             // Item-specific 'Livrer' button click event
            $('.livrer-item-button').on('click', function() {
                var itemId = $(this).data('item-id');
                var url = "{{ route('estimates.deliverItem', ['estimate_number' => $estimate->estimate_number, 'item_id' => ':item_id']) }}".replace(':item_id', itemId);
                handleButtonClick(url, 'Êtes-vous sûr de vouloir livrer cet article?', 'Livraison échouée');
            });

            $('.refuse-item-button').on('click', function() {
                var itemId = $(this).data('item-id');
                var url = "{{ route('estimates.refuseItem', ['estimate_number' => $estimate->estimate_number, 'item_id' => ':item_id']) }}".replace(':item_id', itemId);
                handleButtonClick(url, 'Êtes-vous sûr de vouloir refuser cet article?', 'Refus échouée');
            });

            function handleFormSubmit(form, errorMessage) {
                var url = form.attr('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert(response.success);
                            location.reload(); // Reload the page after successful action
                        } else if (response.warning) {
                            alert(response.warning);
                        } else {
                            alert('Unexpected response format.');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.status + ': ' + xhr.statusText + '\n' + xhr.responseText;
                        alert(errorMessage);
                    }
                });
            }

            function handleButtonClick(url, confirmMessage, errorMessage) {
                if (confirm(confirmMessage)) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.success);
                                location.reload(); // Reload the page to reflect changes
                            } else if (response.warning) {
                                alert(response.warning);
                            } else {
                                alert('Unexpected response format.');
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.status + ': ' + xhr.statusText + '\n' + xhr.responseText;
                            alert(errorMessage);
                        }
                    });
                }
            }
        });
    </script>
    @endsection
@endsection
