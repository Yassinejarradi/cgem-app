@extends('layouts.master')
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Ajout,edit validateurs</title>
@section('content')
<div class="page-wrapper mt-5 pl-5">
<h2 class="mb-5">Modifier les validateurs pour {{ $user->name }}</h2>
    
    <!-- Display currently assigned validators -->
    <h4>Validateurs assignés actuellement</h4>
    @if($assignedValidators->isEmpty())
        <p>Aucun validateur assigné à cet utilisateur.</p>
    @else
        <ul>
            @foreach($assignedValidators as $validator)
                <li>
                    {{ $validator->name }} {{ $validator->prenom }}
                    <form action="{{ route('remove.validator', ['userId' => $user->id, 'validatorId' => $validator->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Form to assign validators -->
    <h4 class="mt-5">Assigner de nouveaux validateurs</h4>
    <form action="{{ url('modify/user/'.$user->id.'/validators') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="validators">Sélectionnez les validateurs :</label>
            <select name="validators[]" id="validators" class="form-control" multiple>
                @foreach($validators as $validator)
                    <option value="{{ $validator->id }}">
                        {{ $validator->name }} {{ $validator->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter des validateurs</button>
    </form>
</div>
@endsection
