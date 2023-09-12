<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm p-2">
    <div class="container">
        
        <a class="navbar-brand text-primary font-weight-bold text-uppercase" href="{{ url('/') }}">
            GQuiOse
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Apps <span class="caret"></span>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @can('view-any', App\Models\User::class)
                            <a class="dropdown-item" href="{{ route('users.index') }}">Users</a>
                            @endcan
                            @can('view-any', App\Models\Permission::class)
                            <a class="dropdown-item" href="{{ route('permissions.index') }}">Permissions</a>
                            @endcan
                            @can('view-any', App\Models\Role::class)
                            <a class="dropdown-item" href="{{ route('roles.index') }}">Roles</a>
                            @endcan
                            @can('view-any', App\Models\Thematique::class)
                            <a class="dropdown-item" href="{{ route('thematiques.index') }}">Thematiques</a>
                            @endcan
                            @can('view-any', App\Models\Question::class)
                            <a class="dropdown-item" href="{{ route('questions.index') }}">Questions</a>
                            @endcan
                            @can('view-any', App\Models\Response::class)
                            <a class="dropdown-item" href="{{ route('responses.index') }}">Responses</a>
                            @endcan
                            @can('view-any', App\Models\Rubrique::class)
                            <a class="dropdown-item" href="{{ route('rubriques.index') }}">Rubriques</a>
                            @endcan
                            @can('view-any', App\Models\Article::class)
                            <a class="dropdown-item" href="{{ route('articles.index') }}">Articles</a>
                            @endcan
                            @can('view-any', App\Models\TypeAlerte::class)
                            <a class="dropdown-item" href="{{ route('type-alertes.index') }}">Type Alertes</a>
                            @endcan
                            @can('view-any', App\Models\Alerte::class)
                            <a class="dropdown-item" href="{{ route('alertes.index') }}">Alertes</a>
                            @endcan
                            @can('view-any', App\Models\Ville::class)
                            <a class="dropdown-item" href="{{ route('villes.index') }}">Villes</a>
                            @endcan
                            @can('view-any', App\Models\TypeStructure::class)
                            <a class="dropdown-item" href="{{ route('type-structures.index') }}">Type Structures</a>
                            @endcan
                            @can('view-any', App\Models\Structure::class)
                            <a class="dropdown-item" href="{{ route('structures.index') }}">Structures</a>
                            @endcan
                            @can('view-any', App\Models\Suivi::class)
                            <a class="dropdown-item" href="{{ route('suivis.index') }}">Suivis</a>
                            @endcan
                            @can('view-any', App\Models\Utilisateur::class)
                            <a class="dropdown-item" href="{{ route('utilisateurs.index') }}">Utilisateurs</a>
                            @endcan
                        </div>

                    </li>
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>