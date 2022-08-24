
<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">


<a class="navbar-brand me-1 me-sm-3" href="{{ url('/Importacion') }}">
  <div class="row gx-0 align-items-center">
    <h6 class="text-primary fs--1 mb-0">Bienvenido a </h6>
    <h4 class="text-primary fw-bold mb-0">PLATAFORMA <span class="text-info fw-medium">SAC</span></h4>
  </div>
</a>

<img class="ms-n4 d-md-none d-lg-block" src="../assets/img/illustrations/crm-line-chart.png" alt="" width="150" />
<ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center ">
  <li class="nav-item dropdown invisible">
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
      <div class="avatar avatar-xl">
        <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" />

      </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
      <div class="bg-white dark__bg-1000 rounded-2 py-2">      
      <a class="dropdown-item fw-bold text-primary" href="#!" id="id_btn_new_po"><span class="fab fa-docker me-1"></span><span>Crear Nº P . O </span></a>

      <div class="dropdown-divider"></div>
      <div class="dropdown-divider"></div>
      
      </div>
    </div>
  </li>
</ul>
</nav>