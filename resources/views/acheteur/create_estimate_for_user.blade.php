<!-- resources/views/create_estimate_for_user.blade.php -->
@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>creation demande</title>

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Nouvelle demande d'achat pour un utilisateur</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('estimates.createForUser') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label for="user_id">Utilisateur <span class="text-danger">*</span></label>
                                    <select class="form-control" id="user_id" name="user_id" required>
                                        <option value="" disabled selected>Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Type de demande <span class="text-danger">*</span></label>
                                    <select class="form-control" id="type_demande" name="type_demande" required>
                                        <option value="fourniture">Fourniture</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Date de création <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" id="estimate_date" name="estimate_date" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Date de besoin <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" id="expiry_date" name="expiry_date">
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
                                                <th>Article</th>
                                                <th>Description</th>
                                                <th>Quantité</th>
                                                <th>Motif de demande</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Table rows will be appended here by JavaScript -->
                                        </tbody>
                                    </table>
                                    <a href="javascript:void(0)" class="text-success font-18" title="Add" id="addBtn"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Page Content -->
    </div>
    <!-- /Page Wrapper -->

    <!-- Add/Edit Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Ajouter un article</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        <div class="form-group">
                            <label for="modal_item">Article</label>
                            <select class="form-control" id="modal_item" name="modal_item">
                                <option value="" disabled selected>Select an article</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modal_description">Description</label>
                            <textarea class="form-control" id="modal_description" name="modal_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="modal_qty">Quantité</label>
                            <input type="number" class="form-control" id="modal_qty" name="modal_qty">
                        </div>
                        <div class="form-group">
                            <label for="modal_motif">Motif de demande</label>
                            <textarea class="form-control" id="modal_motif" name="modal_motif"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add/Edit Item Modal -->

@endsection

@section('script')
<script>
    var rowIdx = 0; // Start with 0
    var articles = @json($articles);
    var editingRow = null;

    // Function to populate items dropdown
    function populateItems(selectElement) {
        if (selectElement.options.length > 1) return; // If options are already populated, don't do it again
        articles.forEach(article => {
            var option = document.createElement("option");
            option.value = article.name;
            option.text = article.name;
            selectElement.add(option);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        populateItems(document.getElementById('modal_item'));
    });

    $("#addBtn").on("click", function () {
        editingRow = null;
        $('#addItemModalLabel').text('Ajouter un article');
        $('#addItemForm')[0].reset();
        $('#addItemModal').modal('show');
    });

    $("#tableEstimate").on("click", ".edit", function () {
        editingRow = $(this).closest("tr");
        var item = editingRow.find('select[name="item[]"]').val();
        var description = editingRow.find('textarea[name="description[]"]').val();
        var qty = editingRow.find('input[name="qty[]"]').val();
        var motif = editingRow.find('textarea[name="motif[]"]').val();

        $('#modal_item').val(item);
        $('#modal_description').val(description);
        $('#modal_qty').val(qty);
        $('#modal_motif').val(motif);

        $('#addItemModalLabel').text('Modifier un article');
        $('#addItemModal').modal('show');
    });

    $("#saveItemBtn").on("click", function () {
        var item = $("#modal_item").val();
        var description = $("#modal_description").val();
        var qty = $("#modal_qty").val();
        var motif = $("#modal_motif").val();

        if (item && description && qty && motif) {
            if (editingRow) {
                editingRow.find('select[name="item[]"]').val(item);
                editingRow.find('textarea[name="description[]"]').val(description).addClass('truncated-text');
                editingRow.find('input[name="qty[]"]').val(qty);
                editingRow.find('textarea[name="motif[]"]').val(motif).addClass('truncated-text');
            } else {
                $("#tableEstimate tbody").append(`
                <tr id="R${++rowIdx}">
                    <td class="row-index text-center"><p>${rowIdx}</p></td>
                    <td><select class="form-control item-select" name="item[]" readonly><option value="${item}">${item}</option></select></td>
                    <td><textarea class="form-control truncated-text" style="min-width:300px" name="description[]" readonly>${description}</textarea></td>
                    <td><input class="form-control qty" style="width:80px" type="number" name="qty[]" value="${qty}" readonly></td>
                    <td><textarea class="form-control truncated-text" style="min-width:200px" type="text" name="motif[]" readonly>${motif}</textarea></td>
                    <td>
                        <a href="javascript:void(0)" class="text-success font-18 edit" title="Edit"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:void(0)" class="text-danger font-18 remove" title="Remove"><i class="fa fa-trash-o"></i></a>
                    </td>
                </tr>`);
                initializeTextareas();
            }
            $('#addItemModal').modal('hide');
        } else {
            alert('Please fill all fields');
        }
    });

    $("#tableEstimate tbody").on("click", ".remove", function () {
        var child = $(this).closest("tr").nextAll();
        child.each(function () {
            var id = $(this).attr("id");
            var idx = $(this).children(".row-index").children("p");
            var dig = parseInt(id.substring(1));
            idx.html(`${dig - 1}`);
            $(this).attr("id", `R${dig - 1}`);
        });
        $(this).closest("tr").remove();
        rowIdx--;
    });

    function initializeTextareas() {
        const textareas = document.querySelectorAll('.truncated-text');
        textareas.forEach(textarea => {
            if (textarea.scrollHeight > textarea.clientHeight) {
                textarea.classList.add('truncated');
                const showMore = document.createElement('span');
                showMore.classList.add('show-more');
                showMore.textContent = '... Show more';
                showMore.addEventListener('click', toggleTextVisibility);
                textarea.parentNode.appendChild(showMore);
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        initializeTextareas();
    });
</script>
@endsection

<style>
    .truncated-text {
        display: block;
        overflow: hidden;
        max-height: 3em;
        position: relative;
        padding-right: 1.5em;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .truncated-text.show-more-visible {
        max-height: none;
        white-space: normal;
    }

    .show-more {
        color: blue;
        cursor: pointer;
        position: absolute;
        right: 0;
        bottom: 0;
        background-color: white;
        padding-left: 5px;
    }
</style>
