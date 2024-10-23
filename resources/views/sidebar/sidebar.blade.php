<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="{{ request()->is('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="la la-dashboard"></i>
                        <span> ACCUEIL</span>
                    </a>
                </li>

            
                <li class="{{ request()->is('form/estimates/page') ? 'active' : '' }}">
                    <a href="{{ route('form/estimates/page') }}">
                        <i class="la la-files-o"></i>
                        <span> Liste Des Demandes </span>
                    </a>
                </li>
                @if(auth()->user()->gestionnaire=== 1)
                <li class="{{ request()->is('articles/index') ? 'active' : '' }}">
                    <a href="{{ route('articles.index') }}">
                        <i class="la la-warehouse"></i>
                        <span> Gestion de Stock </span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role_name === 'Acheteur')
                <li class="{{ request()->is('demanderecu') ? 'active' : '' }}">
                    <a href="{{ route('demanderecu') }}">
                        <i class="la la-files-o"></i>
                        <span> Les Demandes Reçues</span>
                    </a>
                </li>
                <li class="{{ request()->is('articles/index') ? 'active' : '' }}">
                    <a href="{{ route('articles.index') }}">
                        <i class="la la-warehouse"></i>
                        <span> Gestion de Stock </span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role_name == 'Validateur') <!-- Check if user role_name is validator -->
                <li class="{{ request()->is('validator/requests') ? 'active' : '' }}">
                    <a href="{{ route('validator.requests') }}">
                        <i class="la la-check-circle"></i>
                        <span>Interface Des Validateurs</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->admin == 1) <!-- Check if user is admin by admin attribute -->
                <li class="{{ request()->is('users/index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}">
                        <i class="la la-user"></i>
                        <span>Liste Des Utilisateurs </span>
                    </a>
                </li>
                @endif
                
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
