
<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">


<a class="navbar-brand me-1 me-sm-3" href="{{ url('/') }}">
  <div class="row gx-0 align-items-center">
    <h6 class="text-primary fs--1 mb-0">Bienvenido a </h6>
    <h4 class="text-primary fw-bold mb-0">PLATAFORMA <span class="text-info fw-medium">SAC</span></h4>
  </div>
</a>

<ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center ">
  <li class="nav-item">
    <div class="theme-control-toggle fa-icon-wait px-2">
      <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
      <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema claro"><span class="fas fa-sun fs-0"></span></label>
      <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Cambiar a tema oscuro"><span class="fas fa-moon fs-0"></span></label>
    </div>
  </li>
  <li class="nav-item dropdown" style="display:none">
    <a class="nav-link notification-indicator notification-indicator-primary px-0 fa-icon-wait" id="navbarDropdownNotification" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span></a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-card dropdown-menu-notification" aria-labelledby="navbarDropdownNotification">
      <div class="card card-notification shadow-none">
        <div class="card-header">
          <div class="row justify-content-between align-items-center">
            <div class="col-auto">
              <h6 class="card-header-title mb-0">Notificaciones</h6>
            </div>
          </div>
        </div>
        <div class="scrollbar-overlay" style="max-height:19rem">
          <div class="list-group list-group-flush fw-normal fs--1">          

            <div class="list-group-item">
              <a class="notification notification-flush notification-unread" href="#!">
                <div class="notification-avatar">
                  <div class="avatar avatar-2xl me-3">
                    <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />

                  </div>
                </div>
                <div class="notification-body">
                  <p class="mb-1"><strong>Usuario</strong> Actualizo el Contendido de : NOMBRE DEL PRODUCOT y del CAMPO</p>
                    <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">💬</span>Just now</span>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="card-footer text-center border-top"><a class="card-link d-block" href="../../../app/social/notifications.html">Ver Todas</a></div>
      </div>
    </div>

  </li>
  <li class="nav-item dropdown"><a class="nav-link pe-0" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      
    <div class="d-flex align-items-center position-relative">
      <div class="flex-1">
        <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-900">{{Session::get('name_session')}}</div></h6>
        <p class="text-500 fs--2 mb-0">{{ Session::get('Rutas') }}</p>
      </div>
      <div class="avatar avatar-xl ms-3">
        <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}"   />
      </div>
    </div>


    

      
    </a>
    <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
      <div class="bg-white dark__bg-1000 rounded-2 py-2"> 
        @if( Session::get('rol') == '1' || Session::get('rol') == '2')
          <a class="dropdown-item fw-bold text-warning" href="{{ route('ArticuloFavorito') }}"><span class="fas fa-crown me-1"></span><span>Art. Favs.</span></a>
          <a class="dropdown-item" href="{{ route('Comiciones') }}"><span class="fas fa-calendar"></span><span> Comisión</span></a>
          <a class="dropdown-item" href="{{ route('CalendarPromocion') }}"><span class="fas fa-calendar"></span><span> Promociones</span></a>
          <a class="dropdown-item" href="{{ route('Usuarios') }}"> <span class="fas fa-user-tie me-1"></span>Usuario </a>
          <div class="dropdown-divider"></div>
        @endif
        
        
        <a class="dropdown-item" href="{{ route('logout') }}" >Salir</a>
        
      </div>
    </div>
  </li>
</ul>
</nav>