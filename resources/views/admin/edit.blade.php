@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Edit users</title>
@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2>Modifier l'utilisateur</h2>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('estimates.index') }}">Liste des utilisateurs</a></li>
                                <li class="breadcrumb-item active">Modifier l'utilisateur</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-success" style="display:none;"></div>
                    
                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" name="prenom" class="form-control" value="{{ $user->prenom }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role_name">Rôle</label>
                                    <select class="select form-control" name="role_name" id="role_name" required>
                                        <option selected disabled>-- Sélectionnez un Rôle --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->role_type }}" {{ $user->role_name == $role->role_type ? 'selected' : '' }}>{{ $role->role_type }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">S'il vous plaît sélectionnez un rôle.</div>
                                </div>
                                <div class="form-group mb-3" id="acheteur-options" style="{{ $user->role_name == 'Acheteur' ? '' : 'display:none;' }}">
                                    <label class="form-label">Acheteur IT</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="acheteur_it" id="acheteurItYes" value="yes" {{ $user->acheteur_it == 'yes' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="acheteurItYes">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="acheteur_it" id="acheteurItNo" value="no" {{ $user->acheteur_it == 'no' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="acheteurItNo">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control" name="position" value="{{ $user->position }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3 pt-4">
                                    <label for="admin">Admin</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="admin" id="adminYes" value="1" {{ $user->admin ? 'checked' : '' }}>
                                            <label class="form-check-label" for="adminYes">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="admin" id="adminNo" value="0" {{ !$user->admin ? 'checked' : '' }}>
                                            <label class="form-check-label" for="adminNo">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="department">Département</label>
                                    <input type="text" name="department" class="form-control" value="{{ $user->department }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" {{ $user->status == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusActive">Actif</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" {{ $user->status == 'inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusInactive">Inactif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
    <label for="gestionnaire">Gestionnaire de stock</label>
    <div class="d-flex">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gestionnaire" id="gestionnaireYes" value="1" {{ $user->gestionnaire ? 'checked' : '' }}>
            <label class="form-check-label" for="gestionnaireYes">Oui</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gestionnaire" id="gestionnaireNo" value="0" {{ !$user->gestionnaire ? 'checked' : '' }}>
            <label class="form-check-label" for="gestionnaireNo">Non</label>
        </div>
    </div>
</div>

                        
    
                                <button type="button" id="reset-password-button" class="btn btn-warning mt-4">Réinitialiser le mot de passe</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#role_name').change(function() {
        if ($(this).val() === 'Acheteur') {
            $('#acheteur-options').show();
        } else {
            $('#acheteur-options').hide();
        }
    });

    $('#reset-password-button').click(function() {
        $('#password').val('');
        alert('Mot de passe réinitialisé.');
    });
});
</script>
@endsection
