@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Les Demandes</title>
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <!-- Page Wrapper -->
    <div class="page-wrapper">
    
        <!-- Page Content -->
        <div class="content container-fluid">
        
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                    
                        <h3 class="page-title">Demandes d'achat</h3>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="{{ route('create/estimate/page') }}" class="btn add-btn"><i class="fa fa-plus"></i> Nouvelle demande</a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <!-- Filter Panel -->
            <div class="row pb-3">
                <a href="#" class="btn add-btn-filter" data-toggle="collapse" data-target="#filter-panel"><i class="fa fa-filter"></i> Filter</a>
            </div>

            <div id="filter-panel" class=" filter-panel">
                <div class="panel panel-default">
                    <div class="panel-body pb-3">
                        <form action="{{ route('estimates.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Type de demande</label>
                                        <select class="form-control" name="type_demande">
                                            <option value="">Toutes les demandes</option>
                                            <option value="fourniture" {{ request('type_demande') == 'fourniture' ? 'selected' : '' }}>Fourniture</option>
                                            <option value="achat" {{ request('type_demande') == 'achat' ? 'selected' : '' }}>Achat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Date de</label>
                                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Date à</label>
                                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="">Toutes les demandes</option>
                                            <option value="Validée" {{ request('status') == 'Validée' ? 'selected' : '' }}>Validée</option>
                                            <option value="Refusée" {{ request('status') == 'Refusée' ? 'selected' : '' }}>Refusée</option>
                                            <option value="En cours" {{ request('status') == 'En cours' ? 'selected' : '' }}>En cours de validation</option>
                                            <option value="livrer" {{ request('status') == 'livrer' ? 'selected' : '' }}>Livrer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary">Filtrer</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Numero de demande</th>
                                    <th>Type de demande</th>
                                    <th>Date de création</th>
                                    <th>Date du besoin</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estimates as $item)
                                    <tr>
                                        <td hidden class="ids">{{ $item->id }}</td>
                                        <td hidden class="estimate_number">{{ $item->estimate_number }}</td>
                                        <td><a href="{{ url('estimate/view/'.$item->estimate_number) }}">{{ $item->estimate_number }}</a></td>
                                        <td>{{ $item->type_demande }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->estimate_date)->translatedFormat('d F, Y') }}</td>
                                         <td>{{ \Carbon\Carbon::parse($item->expiry_date)->translatedFormat('d F, Y') }}</td>
 <td>
                                            <span class="badge 
                                                @if($item->status == 'Validée') status-validée
                                                @elseif($item->status == 'Validation partielle') status-validée
                                                @elseif($item->status == 'Refusée') status-refusée
                                                @elseif($item->status == 'En cours') status-en-cours
                                                @elseif($item->status == 'livrer') status-validée
                                                @else text
                                                @endif">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            @if($item->status == 'En cours')
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item delete_estimate" href="#" data-toggle="modal" data-target="#delete_estimate"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $estimates->links() }}
                </div>
            </div>
        </div>
        <!-- /Page Content -->
        
        <!-- Delete Estimate Modal -->
        <div class="modal custom-modal fade" id="delete_estimate" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-header">
                            <h3>Supprimer la demande d'achat</h3>
                            <p>Voulez-vous vraiment supprimer la demande d'achat?</p>
                        </div>
                        <form action="{{ route('estimate/delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" class="e_id" value="">
                            <input type="hidden" name="estimate_number" class="estimate_number" value="">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary continue-btn submit-btn">Delete</button>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Delete Estimate Modal -->
    
    </div>
    <!-- /Page Wrapper -->
 
    @section('script')
         {{-- delete model --}}
         <script>
            $(document).on('click','.delete_estimate',function()
            {
                var _this = $(this).parents('tr');
                $('.e_id').val(_this.find('.ids').text());
                $('.estimate_number').val(_this.find('.estimate_number').text());
            });
        </script>
    @endsection
@endsection
