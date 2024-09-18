@extends('Frontend.Reports.partials.layout')

@section('content')
    <?php $line = 0; ?>
    <?php $metas_count = count($report->metas('device')) + 1; ?>
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')
            <?php $line += $metas_count; ?>

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else

                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <?php $line += 1; ?>
                            <tr>
                                <th>{{ trans('front.driver') }}</th>
                                <th>{{ trans('front.distance_driver') }}</th>
                                <th>{{ trans('front.overspeed_duration') }}</th>
                                <th>{{ trans('front.overspeed_score') }}</th>
                                <th>{{ trans('front.harsh_acceleration_count') }}</th>
                                <th>{{ trans('front.harsh_acceleration_score') }}(/100kms)</th>
                                <th>{{ trans('front.harsh_braking_count') }}</th>
                                <th>{{ trans('front.harsh_braking_score') }}(/100kms)</th>
                                <th>RAG</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <?php $line += 1; ?>
                                <?php
                                    $backgroundColor = $row['rag'] > 5 ? '#FF0000' : ($row['rag'] < 2 ? '#00d400' : '#FFFF00');
                                    $xlsStyle = "text-align: center; background-color: {$backgroundColor}; color: #000000;";
                                ?>
                                <tr style="background-color: {{ $backgroundColor }}; color: #000000;">
                                    @if ($report->getFormat() == 'xls')
                                        <td style="{{ $xlsStyle }}">{{ $row['drivers'] }}</td>
                                        <td style="{{ $xlsStyle }}">{{ $row['distance'] }}</td>
                                        <td style="{{ $xlsStyle }}">{{ $row['duration'] }}</td>
                                        <td style="{{ $xlsStyle }}">=C{{ $line }}/10/B{{ $line }}*100</td>
                                        <td style="{{ $xlsStyle }}">{{ $row['ha'] }}</td>
                                        <td style="{{ $xlsStyle }}">=E{{ $line }}/B{{ $line }}*100</td>
                                        <td style="{{ $xlsStyle }}">{{ $row['hb'] }}</td>
                                        <td style="{{ $xlsStyle }}">=G{{ $line }}/B{{ $line }}*100</td>
                                        <td style="{{ $xlsStyle }}">=D{{ $line }}+F{{ $line }}+H{{ $line }}</td>
                                    @else
                                        <td style="text-align: center;">{{ $row['drivers'] }}</td>
                                        <td style="text-align: center;">{{ $row['distance'] }}</td>
                                        <td style="text-align: center;">{{ $row['duration'] }}</td>
                                        <td style="text-align: center;">{{ $row['score_overspeed'] }}</td>
                                        <td style="text-align: center;">{{ $row['ha'] }}</td>
                                        <td style="text-align: center;">{{ $row['score_harsh_a'] }}</td>
                                        <td style="text-align: center;">{{ $row['hb'] }}</td>
                                        <td style="text-align: center;">{{ $row['score_harsh_b'] }}</td>
                                        <td style="text-align: center;">{{ $row['rag'] }}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <?php $line += 1; ?>
                    </div>
                @endif
            @endif
        </div>
    @endforeach

    <div class="panel panel-default">
        <div class="panel-body no-padding">
            <table class="table " style="color: #000000;">
                <tbody>
                <tr>
                    <td style="background-color: #FF0000;">{{ strtoupper(trans('front.above')) }} 5</td>
                </tr>
                <tr>
                    <td style="background-color: #FFFF00;">{{ strtoupper(trans('front.between')) }} 2 {{ strtoupper(trans('front.and')) }} 5</td>
                </tr>
                <tr>
                    <td style="background-color: #00d400;">{{ strtoupper(trans('front.less_than')) }} 2</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($report->getFormat() != 'xls')
        <div class="panel panel-default">
            <div class="panel-body no-padding" style="padding: 0px;">
                <table class="table" style="table-layout: auto; margin-bottom: 0px;">
                    <tbody>
                    <tr>
                        <td style="width: 150px;">D</td>
                        <td>{{ trans('front.distance_driver') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">OD</td>
                        <td>{{ trans('front.overspeed_duration') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">AC</td>
                        <td>{{ trans('front.harsh_acceleration_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">AS = AC / D * 100</td>
                        <td>{{ trans('front.harsh_acceleration_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">BC</td>
                        <td>{{ trans('front.harsh_braking_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">BS = BC / D * 100</td>
                        <td>{{  trans('front.harsh_braking_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">OS = OD / 10 / D * 100</td>
                        <td>{{ trans('front.overspeed_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">R = OS + AS + BS</td>
                        <td>RAG</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@stop