@extends('layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Liste de demande à valider</h3>
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
                                            @if($item->statut_v  == 'Validée') status-validée
                                            @elseif($item->statut_v  == 'Refusée') status-refusée
                                            @elseif($item->status == 'En cours') status-en-cours
                                            @elseif($item->status == 'livrer') status-validée
                                            @endif">
                                            {{ $item->statut_v }}
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#validate-form').on('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: 'POST',
                url: url,
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        alert(response.success);
                        location.reload(); // Reload the page to reflect changes
                    } else if (response.warning) {
                        alert(response.warning);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText + '\n' + xhr.responseText;
                    alert('Validation échouée: ' + errorMessage);
                }
            });
        });
    });
</script>
@endsection
