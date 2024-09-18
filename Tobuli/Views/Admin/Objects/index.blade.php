@extends('Admin.Layouts.default')

@section('content')
    @if (Session::has('messages'))
        <div class="alert alert-success">
            <ul>
                @foreach (Session::get('messages') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel panel-default" id="table_{{ $section }}">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                @if( Auth::User()->perm('devices', 'edit') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="devices_create" data-url="{{ route("devices.create") }}">
                        <i class="icon device-add" title="{{ trans('admin.add_new_device') }}"></i>
                    </a>
                </li>
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_import" data-url="{{ route("admin.objects.import") }}">
                        <i class="icon device-import" title="{{ trans('front.import_devices') }}"></i>
                    </a>
                </li>
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_export_modal" data-url="{{ route("admin.objects.export_modal") }}">
                        <i class="fa fa-download" title="{{ trans('admin.export_devices') }}"></i>
                    </a>
                </li>
                @endif
                @if(config('addon.devices_bulk_delete') && Auth::User()->isAdmin())
                    <li role="presentation" class="">
                        <a href="javascript:" type="button" data-modal="{{ $section }}_bulk_delete"
                           data-url="{{ route('admin.objects.bulk_delete') }}">
                            <i class="fa fa-trash" title="Bulk delete"></i>
                        </a>
                    </li>
                @endif
            </ul>

            <div class="panel-title"><i class="icon device"></i> {{ trans('admin.'.$section) }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
@stop

@section('javascript')
<script>


$(document).ready(function() {
        // Función para verificar el ancho de la pantalla al cargar la página
        removeClassICO();
        checkScreenWidth();
     
        // Función que se ejecuta cuando cambia el tamaño de la pantalla
    });


    tables.set_config('table_{{ $section }}', {
        url: '{{ route("admin.{$section}.index") }}',
        do_destroy: {
            url: '{{ route("admin.objects.do_destroy") }}',
            modal: '{{$section}}_delete',
            method: 'GET'
        },
        assign: {
            url: '{{ route("admin.objects.assignForm") }}',
            modal: '{{$section}}_assign',
            method: 'GET'
        },
        set_active: {
            url: '{{ route("admin.objects.set_active", 1) }}',
            method: 'POST'
        },
        set_inactive: {
            url: '{{ route("admin.objects.set_active", 0) }}',
            method: 'POST'
        }
    });

    function {{ $section }}_assign_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_edit_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_create_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_import_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_delete_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    $(document).on('bulk_delete_object', function (e, res) {
        $('#objects_bulk_delete .alert-success').css('display', 'block').html(res.content);
    });


    function checkScreenWidth() {
        // Obtener el ancho de la pantalla
        var screenWidth = $(window).width();

        // Ancho común para dispositivos móviles (puedes ajustar este valor)
        var mobileWidth = 767;

        // Verificar si el ancho de la pantalla es menor que el ancho definido para dispositivos móviles
        if (screenWidth <= mobileWidth) {            
            // Agregar la clase "collapsed" al div de la barra lateral si es un dispositivo móvil
            $('#sidebar').addClass('collapsed');    
            $('#android').removeClass('collapsed');
            $('#ios').removeClass('img-responsive');
            //$('#widgets').addClass('collapsed');            
        } else {            
            $('#sidebar').removeClass('collapsed');
            $('#android').addClass('collapsed');
            $('#ios').addClass('img-responsive');
            //$('#widgets').removeClass('collapsed');
            // Eliminar la clase "collapsed" si no es un dispositivo móvil
            
        }
    }

    function removeClassICO() {
        
        console.log("RemoveClassICO");
        
        var screenWidth = $(window).width();
        var mobileWidth = 767;

        if (screenWidth <= mobileWidth) {            
            $('#android').removeClass('collapsed');
            $('[id="ios"]').removeClass('img-responsive');
        } else {            
            $('#android').addClass('collapsed');
            $('[id="ios"]').addClass('img-responsive');
        }
    }

</script>
@stop