@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Creation users</title>
@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Créer un utilisateur</h3>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-success" style="display:none;"></div>
                    <form method="POST" action="{{ route('user.store') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback">S'il vous plaît entrez un nom.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">S'il vous plaît entrez un email valide.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="col-form-label">Rôle Nom</label>
                                    <select class="select form-control" name="role_name" id="role_name" required>
                                        <option selected disabled>-- Sélectionnez un Rôle --</option>
                                        @foreach ($role as $name)
                                            <option value="{{ $name->role_type }}">{{ $name->role_type }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">S'il vous plaît sélectionnez un rôle.</div>
                                </div>
                                <div class="form-group mb-3" id="acheteur-options" style="display:none;">
                                    <label class="form-label">Acheteur IT</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="acheteur_it" id="acheteurItYes" value="yes">
                                            <label class="form-check-label" for="acheteurItYes">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="acheteur_it" id="acheteurItNo" value="no" checked>
                                            <label class="form-check-label" for="acheteurItNo">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="position" name="position">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3 pt-4">
                                    <label class="form-label">Admin</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="admin" id="adminYes" value="yes">
                                            <label class="form-check-label" for="adminYes">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="admin" id="adminNo" value="no" checked>
                                            <label class="form-check-label" for="adminNo">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="department" class="form-label">Département</label>
                                    <input type="text" class="form-control" id="department" name="department">
                                </div>
                                <div class="form-group mb-3">
    <label for="gestionnaire">Gestionnaire De Stock</label>
    <div class="d-flex">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gestionnaire" id="gestionnaireYes" value="1" >
            <label class="form-check-label" for="gestionnaireYes">Oui</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gestionnaire" id="gestionnaireNo" value="0" >
            <label class="form-check-label" for="gestionnaireNo">Non</label>
        </div>
    </div>
</div>
    </div>
</div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback">S'il vous plaît entrez un mot de passe.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <div class="invalid-feedback">S'il vous plaît confirmez le mot de passe.</div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn bg-custom-blue text-white">Création</button>
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
    // Configuration AJAX avec le token CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Gestion de la soumission du formulaire avec AJAX
    $('form').submit(function(event) {
        event.preventDefault(); // Empêcher la soumission du formulaire via le navigateur.

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('.alert-success').text(response.message).show();
                    $('form').trigger('reset'); // Optionnellement réinitialiser le formulaire
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').hide();
                } else {
                    alert('Erreur: ' + (response.message || 'Une erreur est survenue sans message.'));
                }
            },
            error: function(xhr) {
                $('.is-invalid').removeClass('is-invalid');  // Supprimer les points rouges des entrées invalides existantes
                $('.invalid-feedback').hide();              // Cacher les messages d'erreur existants

                if (xhr.status === 422) {  // Erreurs de validation Laravel
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid').next('.invalid-feedback').text(value[0]).show();
                    });
                } else {  // Autres types d'erreurs (500, 503, etc.)
                    alert('Erreur: ' + (xhr.responseJSON.message || xhr.statusText));
                }
            }
        });
    });

    // Afficher des options supplémentaires pour le rôle "Acheteur"
    $('#role_name').change(function() {
        if ($(this).val() === 'Acheteur') {
            $('#acheteur-options').show();
        } else {
            $('#acheteur-options').hide();
        }
    });
});
</script>
@endsection
