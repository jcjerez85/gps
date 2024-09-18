<?php $uid = uniqid(); ?>
<div class="input-group">
    <div class="has-feedback">
        <i class="icon address form-control-feedback"></i>
        {!! Form::text($type.'_address', $address, ['class' => 'form-control', 'placeholder' => trans('front.search_address'), 'autocomplete' => 'off', 'uid' => $uid]) !!}

    </div>
    <span class="input-group-btn">
            <a href="javascript:"
               id="{{ $type }}_address_button"
               uid="{{ $uid }}"
               class="form-control {{ $type }}_address"
               data-url="{!! route('address.map') !!}"
               data-modal="address_map"
               data-type="{{ $type }}"
               data-parent="{{ $parent }}"
               data-address="{{ $address }}"
               data-lat="{{ $lat }}"
               data-lng="{{ $lng }}"
               type="button">
                <i class="icon search"></i>
            </a>
    </span>
</div>
<script>
    $(document).ready(function() {
        $('input[uid="{{ $uid }}"]').on('click', function() {
            $(this).blur();
            $('a[uid="{{ $uid }}"]').trigger('click');
        });
    });
</script>
