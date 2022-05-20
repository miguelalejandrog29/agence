@extends('layouts.app')

@section('extra-js')
<script type="text/javascript" src="{{ asset('assets/apexcharts/apexcharts.min.js') }}"></script>
@endsection

@section('content')
<div class="container-xl">
    <div class="row">
        <div class="col-md-10">
            <div class="row justify-content-start mb-3">
                <div class="col-md-2">
                    <h6><strong>{{ __('Período') }}</strong></h6>
                </div>
                <div class="col-md-auto">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <select id="startmonth" class="form-select" aria-label="Meses del año">
                                    @foreach ($meses as $key => $mes)
                                    <option value="{{ $key }}">{{ $mes }}</option>
                                    @endforeach
                                </select>
                                <select id="startyear" class="form-select" aria-label="Años">
                                    @for ($year = $yearRange->yearMin; $year <= $yearRange->yearMax; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto">
                    <p>{{ __('a') }}</p>
                </div>
                <div class="col-md-auto">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <select id="endmonth" class="form-select" aria-label="Meses del año">
                                    @foreach ($meses as $key => $mes)
                                    <option value="{{ $key }}">{{ $mes }}</option>
                                    @endforeach
                                </select>
                                <select id="endyear" class="form-select" aria-label="Años">
                                    @for ($year = $yearRange->yearMin; $year <= $yearRange->yearMax; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <h6><strong>{{ __('Consultores') }}</strong></h6>
                </div>
                <div class="col-md">
                    <div class="row">
                        <div class="col-md mb-2">
                            <select id="slcConsultoresAvailables" class="form-select" multiple aria-label="Consultores disponibles">
                                @foreach ($consultores as $consultor)
                                <option value="{{ $consultor->co_usuario }}">{{ $consultor->no_usuario }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto mb-2">
                            <div class="btn-group-vertical" role="group">
                                <button type="button" class="btn btn-light" onclick="Assign_Comercial()"><span class="fas fa-angle-double-right text-muted"></span></button>
                                <button type="button" class="btn btn-light" onclick="Remove_Comercial()"><span class="fas fa-angle-double-left text-muted"></button>
                            </div>
                        </div>
                        <div class="col-md">
                            <select id="slcConsultoresSelected" class="form-select" multiple aria-label="Consultores seleccionados">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="mb-3 py-5 d-grid gap-2">
                <button type="button" class="btn btn-secondary mb-2" onclick="Asinc_Get_Relatorio()"><span class="fas fa-list-alt text-info"></span> {{ __('Relatório') }}</button>
                <button type="button" class="btn btn-secondary mb-2" onclick="Asinc_Get_Grafico()"><span class="fas fa-line-chart text-info"></span> {{ __('Gráfico') }}</button>
                <button type="button" class="btn btn-secondary" onclick="Asinc_Get_Pizza()"><span class="fas fa-chart-pie text-info"></span> {{ __('Pizza') }}</button>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid text-center" id="loadingContent" style="display: none;">
    <div class="spinner-grow text-info" style="width: 4rem; height: 4rem;" role="status">
        <span class="sr-only">{{ __('Cargando...') }}</span>
    </div>
    <h5>
        <small class="text-muted">{{ __('Cargando...') }}</small>
    </h5>
</div>

<div class="container-xl" id="ajaxContent"></div>

<script type="text/javascript">
    $(document).ready(function() {
        $("a#nbDpdwnComercial").addClass("active");
    });

    function Assign_Comercial() {
        $('#slcConsultoresSelected').append($('#slcConsultoresAvailables option:selected'));
        $('#slcConsultoresAvailables option:selected').remove();
    }

    function Remove_Comercial() {
        $('#slcConsultoresAvailables').append($('#slcConsultoresSelected option:selected'));
        $('#slcConsultoresSelected option:selected').remove();
    }

    function Asinc_Get_Relatorio() {
        let data = [];
        $('#slcConsultoresSelected > option').each(function() {
            data.push(this.value);
        });

        let startDate = $('#startyear').val() + '-' + $('#startmonth').val();
        let endDate = $('#endyear').val() + '-' + $('#endmonth').val();

        $.ajax({
            data: "consultores=" + JSON.stringify(data) + "&startdate=" + startDate + "&enddate=" + endDate,
            type: "POST",
            dataType: "html",
            url: "{{ route('comercial.relatorio') }}",
            beforeSend: function() {
                $('#loadingContent').show();
                $("#ajaxContent").html("");
            },
            complete: function() {
                $('#loadingContent').hide();
            },
            success: function(resultdata) {
                $("#ajaxContent").html(resultdata);
            }
        });
    }

    function Asinc_Get_Grafico() {
        let data = [];
        $('#slcConsultoresSelected > option').each(function() {
            data.push(this.value);
        });

        let startDate = $('#startyear').val() + '-' + $('#startmonth').val();
        let endDate = $('#endyear').val() + '-' + $('#endmonth').val();

        $.ajax({
            data: "consultores=" + JSON.stringify(data) + "&startdate=" + startDate + "&enddate=" + endDate,
            type: "POST",
            dataType: "html",
            url: "{{ route('comercial.grafico') }}",
            beforeSend: function() {
                $('#loadingContent').show();
                $("#ajaxContent").html("");
            },
            complete: function() {
                $('#loadingContent').hide();
            },
            success: function(resultdata) {
                $("#ajaxContent").html(resultdata);
            }
        });
    }

    function Asinc_Get_Pizza() {
        let data = [];
        $('#slcConsultoresSelected > option').each(function() {
            data.push(this.value);
        });

        let startDate = $('#startyear').val() + '-' + $('#startmonth').val();
        let endDate = $('#endyear').val() + '-' + $('#endmonth').val();

        $.ajax({
            data: "consultores=" + JSON.stringify(data) + "&startdate=" + startDate + "&enddate=" + endDate,
            type: "POST",
            dataType: "html",
            url: "{{ route('comercial.pizza') }}",
            beforeSend: function() {
                $('#loadingContent').show();
                $("#ajaxContent").html("");
            },
            complete: function() {
                $('#loadingContent').hide();
            },
            success: function(resultdata) {
                $("#ajaxContent").html(resultdata);
            }
        });
    }

    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection