@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Edit</title>
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
        
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Modification de demande d'achat</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            {{-- message --}}
            {!! Toastr::message() !!}

            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('create/estimate/update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Type de demande <span class="text-danger">*</span></label>
                                    <select class="form-control" id="type_demande" name="type_demande" onchange="toggleAdditionalOptions(this.value)">
                                        <option value="fourniture" {{ $estimates->type_demande == 'fourniture' ? 'selected' : '' }}>Fourniture</option>
                                        <option value="achat" {{ $estimates->type_demande == 'achat' ? 'selected' : '' }}>Achat</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                          document.addEventListener("DOMContentLoaded", function() {
                             var currentDate = new Date().toISOString().slice(0, 10);
                             document.getElementById("estimate_date").value = currentDate;});
                            </script>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Date de création <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" id="estimate_date" name="estimate_date" value="{{ $estimates->estimate_date }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Date de besoin <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" id="expiry_date" name="expiry_date" value="{{ $estimates->expiry_date }}">
                                    </div>
                                </div>
                            </div>

                            
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-white" id="tableEstimate">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px">#</th>
                                                <th class="col-sm-2">Article</th> <!-- Hia item f db estimate_adds-->
                                                <th class="col-md-6">Description</th> <!-- Hia description f db estimate_adds-->
                                                <!--<th style="width:100px;">Prix unitaire</th>  Hia unit_cost f db estimate_adds
                                                -->
                                                <th style="width:80px;">Quantité</th>  <!-- Hia qty f db estimate_adds-->
                                                <th>MOTIF DE DEMANDE</th>
                                                <th> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                     <!-- le + pour ajouter ligne-->
                                        <td><a href="javascript:void(0)" class="text-success font-18" title="Add" id="addBtn"><i class="fa fa-plus"></i></a></td> 
                                        <tr>
                                
                                            <td>1</td>
                                            <td><input class="form-control" style="min-width:200px" type="text" id="item" name="item[]"></td>
                                            <td><textarea class="form-control" style="min-width:300px" id="description" name="description[]"></textarea></td>
                                            <td><input class="form-control qty" style="width:80px" type="number" id="qty" name="qty[]"></td>
                                            <td><textarea class="form-control" style="min-width:200px" type="text" id="motif" name="motif[]"></textarea></td>
                                            <td><a href="javascript:void(0)" class="text-danger font-18 remove" title="Remove"><i class="fa fa-trash-o"></i></a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                    </div>
                            </div>
                            <div class="row" id="additionalOptions" style="display:none;">
                               <!-- Option cards -->
                               <div class="option-cards-stack">
                        <div class="option-card">
                            <div>
                            <div class="checkbox-group">
                                <div class="checkbox-group-title-div">
                                <label class="checkbox-group-title"
                                    >Pièces à demander lors de la consultation:</label
                                ><br />
                                </div>
                                <input
                                type="checkbox"
                                id="Echantillons"
                                name="piece_joint[]"
                                value="Echantillons"
                                />
                                <label for="Echantillons">Echantillons</label><br />
                                <input
                                type="checkbox"
                                id="Catalogues"
                                name="piece_joint[]"
                                value="Catalogues"
                                />
                                <label for="Catalogues">Catalogues</label><br />
                                <input
                                type="checkbox"
                                id="Facture proforma"
                                name="piece_joint[]"
                                value="Facture proforma"
                                />
                                <label for="Facture proforma">Facture proforma</label><br />
                            </div>
                            </div>
                        </div>

                        <div class="option-card">
                            <div>
                            <div class="checkbox-group">
                                <div class="checkbox-group-title-div">
                                <label class="checkbox-group-title"
                                    >Elements exigés lors de la réception :</label
                                ><br />
                                </div>
                                <input
                                type="checkbox"
                                id="piece1"
                                name="element_exiges_lors_de_la_reception[]"
                                value="Certificats"
                                />
                                <label for="piece1">Certificats</label><br />
                                <input
                                type="checkbox"
                                id="piece2"
                                name="element_exiges_lors_de_la_reception[]"
                                value="Formation"
                                />
                                <label for="piece2">Formation</label><br />
                                <input
                                type="checkbox"
                                id="piece3"
                                name="element_exiges_lors_de_la_reception[]"
                                value="Manuels d'utilisation"
                                />
                                <label for="piece3">Manuels d'utilisation</label><br />
                            </div>
                            </div>
                        </div>

                        <div class="option-card">
                            <div>
                            <div class="checkbox-group">
                                <div class="checkbox-group-title-div">
                                <label class="checkbox-group-title"
                                    >Participation à la consultation / selection :</label
                                ><br />
                                </div>
                                <input type="radio" id="Oui" name="participation_a_la_consultation_selection" value="Oui">
                        <label for="Oui">Oui</label><br>
                        <input type="radio" id="Non" name="participation_a_la_consultation_selection" value="Non">
                        <label for="Non">Non</label><br>
                        </div>
                            </div>
                        </div>

                        <div class="option-card">
                            <div>
                            <div class="checkbox-group">
                                <div class="checkbox-group-title-div">
                                <label class="checkbox-group-title"
                                    >L'achat demandé est-il</label
                                ><br />
                                </div>
                                <input
                                type="checkbox"
                                id="piece1"
                                name="achat_demande[]"
                                value="Budgétisé"
                                />
                                <label for="piece1">Budgétisé</label><br />
                                <input
                                type="checkbox"
                                id="piece2"
                                name="achat_demande[]"
                                value="Non Budgétisé"
                                />
                                <label for="piece2">Non Budgétisé</label><br />
                                <input
                                type="checkbox"
                                id="piece3"
                                name="achat_demande[]"
                                value="Sponsorisé"
                                />
                                <label for="piece3">Sponsorisé</label><br />
                                <input
                                type="checkbox"
                                id="piece4"
                                name="achat_demande[]"
                                value="Non Sponsorisé"
                                />
                                <label for="piece4">Non Sponsorisé</label><br />
                            </div>
                            </div>
                        </div>
                        </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Enregistrer et envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Page Content -->
    </div>
    <!-- /Page Wrapper -->

    @section('script')
    <script>
            var rowIdx = 1;
            $("#addBtn").on("click", function () {
    // Adding a row inside the tbody.
    $("#tableEstimate tbody").append(`
    <tr id="R${++rowIdx}">
        <td class="row-index text-center"><p> ${rowIdx}</p></td>
        <td><input class="form-control" style="min-width:200px" type="text" id="item" name="item[]"></td>
        <td><textarea class="form-control" style="min-width:300px" id="description" name="description[]"></textarea></td>
        <td><input class="form-control qty" style="width:80px" type="number" id="qty" name="qty[]"></td>
        <td><textarea class="form-control" style="min-width:200px" type="text" id="motif" name="motif[]"></textarea></td>
    <td><a href="javascript:void(0)" class="text-danger font-18 remove" title="Remove"><i class="fa fa-trash-o"></i></a></td>
    </tr>`);
});

            $("#tableEstimate tbody").on("click", ".remove", function ()
            {
                // Getting all the rows next to the row
                // containing the clicked button
                var child = $(this).closest("tr").nextAll();
                // Iterating across all the rows
                // obtained to change the index
                child.each(function () {
                // Getting <tr> id.
                var id = $(this).attr("id");

                // Getting the <p> inside the .row-index class.
                var idx = $(this).children(".row-index").children("p");

                // Gets the row number from <tr> id.
                var dig = parseInt(id.substring(1));

                // Modifying row index.
                idx.html(`${dig - 1}`);

                // Modifying row id.
                $(this).attr("id", `R${dig - 1}`);
            });
    
                // Removing the current row.
                $(this).closest("tr").remove();
    
                // Decreasing total number of rows by 1.
                rowIdx--;
            });
              function toggleAdditionalOptions(value) {
                const additionalOptions = document.getElementById('additionalOptions');
                additionalOptions.style.display = (value === 'achat') ? 'block' : 'none';
            }
        </script>
    @endsection
@endsection
