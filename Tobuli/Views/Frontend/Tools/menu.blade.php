@extends('Frontend.Layouts.modal')
@section('modal_class', 'modal-lg')

@section('title')
<i class="icon tools"></i> {{ trans('front.tools') }}
@stop
<style>
    .p-4 {
        padding: 1.5rem;
        /* Puedes ajustar el valor según el espacio de relleno deseado */
    }

    .card {
        border: 1px solid #e0e0e0;
        /* Color del borde de la tarjeta */
        border-radius: 8px;
        /* Radio de borde para esquinas redondeadas */
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        /* Sombra para efecto de elevación */
        transition: box-shadow 0.3s ease-in-out;
        /* Efecto de transición al pasar el mouse */

        /* Margen inferior entre tarjetas */
        margin-bottom: 20px;
        /* Ajusta el valor según el espacio deseado */
    }

    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
        /* Efecto de sombra al pasar el mouse */
    }

    .mb-4 {
        margin-bottom: 1.5rem;
        /* Puedes ajustar el valor según el espacio de margen deseado */
    }

    .mb-3 {
        margin-bottom: 1rem;
        /* Puedes ajustar el valor según el espacio de margen deseado */
    }

    h5 {
        font-size: 1.25rem;
        /* Tamaño de fuente */
        /* Grosor de la fuente (negrita) */
        margin-bottom: 0.5rem;
        /* Margen inferior */
        /* Otros estilos que desees aplicar */
    }
</style>

@section('body')
<div class="row">
    @php
    $user = Auth::user();
    @endphp

    @if ( Auth::User()->perm('alerts', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{!! route('alerts.index_modal') !!}" data-modal="alerts" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 alerts"></i>
                <h5 class="text">{!!trans('front.alerts')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    @if ( Auth::User()->perm('geofences', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:"  data-toggle="modal" data-target="#miModal" onclick="app.geofences.list();app.openTab('geofencing_tab'); cerrarModal()" >
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 geofences"></i>
                <h5 class="text">{!!trans('front.geofencing')!!}</h5>
            </div>
        </a>
    </div>

    @endif

    @if ( Auth::User()->perm('routes', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" onclick="app.routes.list();app.openTab('routes_tab'); cerrarModal()">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 routes"></i>
                <h5 class="text">{!!trans('front.routes')!!}</h5>
            </div>
        </a>
    </div>

    @endif

    @if ( Auth::User()->perm('camera', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('device_media.create') }}" data-modal="camera_photos" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 camera"></i>
                <h5 class="text">{!!trans('front.camera')!!}</h5>
            </div>
        </a>
    </div>

    @endif
    @if ( Auth::User()->perm('reports', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{!!route('reports.create')!!}" data-modal="reports_create" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 reports"></i>
                <h5 class="text">{!!trans('front.reports')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    <div class="col-md-3 mb-4">
        <a href="#objects_tab" data-toggle="tab" onclick="app.ruler(); cerrarModal()">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 ruler"></i>
                <h5 class="text">{!!trans('front.ruler')!!}</h5>
            </div>
        </a>
    </div>

    @if ( Auth::User()->perm('poi', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" onClick="app.pois.list();app.openTab('pois_tab'); cerrarModal()">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 poi"></i>
                <h5 class="text">{!!trans('front.poi')!!}</h5>
            </div </a>
    </div>
    @endif

    @if ( Auth::User()->perm('maintenance', 'view') )
    <div class="col-md-3 mb-4">
        <a href="{!!route('maintenance.index')!!}" target="_blank" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 services"></i>
                <h5 class="text">{!!trans('front.maintenance')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-toggle="modal" data-target="#showPoint">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 point"></i>
                <h5 class="text">{!!trans('front.show_point')!!}</h5>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-toggle="modal" data-target="#showAddress">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 address"></i>
                <h5 class="text">{!! trans('front.show_address') !!}</h5>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('send_command.create') }}" data-modal="send_command">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 send-command"></i>
                <h5 class="text">{!!trans('front.send_command')!!}</h5>
            </div>
        </a>
    </div>

    @if( Auth::User()->perm('device_expenses', 'view') && expensesTypesExist())

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('device_expenses.modal') }}" data-modal="devices_expenses">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 money"></i>
                <h5 class="text">{!!trans('front.expenses')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    <div class="col-md-3 mb-4">
        <a href="{{ route('dashboard') }}" onClick="event.preventDefault(); app.dashboard.init();">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 dashboard"></i>
                <h5 class="text">{!!trans('front.dashboard')!!}</h5>
            </div>
        </a>
    </div>@if ( Auth::User()->perm('sharing', 'view') )

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('sharing.index') }}" data-modal="sharing">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 sharing"></i>
                <h5 class="text">{!!trans('front.sharing')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    @if (Auth::user()->able('configure_device'))
    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('device_config.index') }}" data-modal="device_config" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 devices"></i>
                <h5 class="text">{!!trans('front.device_configuration')!!}</h5>
            </div>
        </a>
    </div>
    @endif
    @if ( Auth::User()->perm('call_actions', 'view') )

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('call_actions.index') }}" data-modal="call_actions">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 call_action"></i>
                <h5 class="text">{!! trans('front.call_actions') !!}</h5>
            </div>
        </a>
    </div>
    @endif


    @if ( Auth::User()->perm('forwards', 'view') )
    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('forwards.index') }}" data-modal="forwards">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 forwards"></i>
                <h5 class="text">{!! trans('front.forwards') !!}</h5>
            </div>
        </a>
    </div>
    @endif
    @if ( Auth::User()->perm('tasks', 'view') )

    <div class="col-md-3 mb-4">
        <a href="javascript:" data-url="{{ route('tasks.index') }}" data-modal="tasks" role="button">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 task"></i>
                <h5 class="text">{!!trans('front.tasks')!!}</h5>
            </div>
        </a>
    </div>
    @endif

    <div class="col-md-3 mb-4">
        <a href="https://www.knoxtrack.com/tutorialesknoxtrack">
            <div class="card bg-primary text-white text-center p-4">
                <i class="icon fa-2x mb-3 play"></i>
                <h5 class="text">Tutoriales</h5>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-4">
        <div class="text-white text-center p-4">
            <a href="https://play.google.com/store/apps/details?id=com.knoxgps.app" target="_blank">
                <img id="android" src="https://knoxgps.com/assets/images/google-play.png" class="img-responsive">
                <h5 class="text bold">Descargar Andorid</h5>
            </a>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="text-white text-center p-4">
            <a href="https://apps.apple.com/app/knox-track/id6466385571" target="_blank">
                <img id="ios" src="https://knoxgps.com/assets/images/apple-store.png" class="img-responsive">
                <h5 class="text bold">Descargar IOS</h5>
            </a>
        </div>
    </div>
    <!-- Agrega más filas según sea necesario -->
</div>

<script>
    function cerrarModal() {        
        $('.modal').modal('hide');
    }

    $(document).ready(function() {
                
        var screenWidth = $(window).width();
        var mobileWidth = 767;

        if (screenWidth <= mobileWidth) {   
            //console.log("Entre")         
            $('#android').removeClass('img-responsive');
            $('#ios').removeClass('img-responsive');
        } else {            
            $('#android').addClass('img-responsive');
            $('#ios').addClass('img-responsive');
        }
    });
</script>

@stop
@section('buttons')
@stop