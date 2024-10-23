@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Les utilisateurs</title>
@section('content')
    {!! Toastr::message() !!} <!-- Flash message display -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Liste des utilisateurs</h3>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="{{ route('user.create') }}" class="btn add-btn"><i class="fa fa-plus"></i> Créer un utilisateur</a>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <!-- Filter Panel -->
            <!-- Filter Panel -->
            <div class="row filter-panel">
                <div class="col-md-12">
                    <form action="{{ route('users.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Nom</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" name="prenom" id="prenom" class="form-control" value="{{ request('prenom') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="role_name">Rôle</label>
                                    <input type="text" name="role_name" id="role_name" class="form-control" value="{{ request('role_name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="admin">Admin</label>
                                    <select name="admin" id="admin" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="1" {{ request('admin') == '1' ? 'selected' : '' }}>Oui</option>
                                        <option value="0" {{ request('admin') == '0' ? 'selected' : '' }}>Non</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Filter Panel -->
            <!-- /Filter Panel -->

            <!-- Users Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Rôle</th>
                                    <th>Admin</th>
                                    <th>Nombre des Validateurs</th>
                                    <th>Nom des Validateurs</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->prenom }}</td>
                                        <td>{{ $user->role_name }}</td>
                                        <td>{{ $user->admin ? 'Oui' : 'Non' }}</td>
                                        <td>{{ $user->validators->count() }}</td>  <!-- Display the count of validators -->
                                        <td>
                                            @foreach($user->validators as $validator)
                                                {{ $validator->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </td>
                                        <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="{{ route('user.edit', $user->id) }}"><i class="fa fa-pencil m-r-5"></i> Editer</a>
                                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Supprimer</button>
                                                    </form>
                                                    <a class="dropdown-item" href="{{ url('modify/user/'.$user->id.'/validators') }}"><i class="fa fa-users m-r-5"></i> Modifier les validateurs</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Aucun utilisateur trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('script')
    <script>
        $(document).on('click','.delete_user',function() {
            var _this = $(this).parents('tr');
            $('.e_id').val(_this.find('.ids').text());
            $('.estimate_number').val(_this.find('.estimate_number').text());
        });
    </script>
@endsection

    <!-- @section('script')
        {{-- delete model --}}
        <script>
            $(document).on('click','.delete_estimate',function()
            {
                var _this = $(this).parents('tr');
                $('.e_id').val(_this.find('.ids').text());
                $('.estimate_number').val(_this.find('.estimate_number').text());
            });
        </script>
    @endsection -->