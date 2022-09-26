<!doctype html>
<html lang="en">
<head>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css"/>
    <link href="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.css" rel="stylesheet">
    <!--Regular Datatables CSS-->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.3.2/css/searchBuilder.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchpanes/2.0.0/css/searchPanes.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.3.3/css/searchBuilder.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"/>

    <style>
        html{
            padding: 0 5px 0 5px;
        }
        #example_filter{
            display: none;
        }
        #example_paginate{
            display: none;
        }
        #example_info{
            display: none;
        }
        .dt-buttons{
            width: 60%;
            justify-content: center;
            column-gap: 10px;
        }
        .dataTables_length{
            width: 20%;
        }
        .dataTables_length label select{
            width: 33% !important;
        }
        .dataTables_filter{
            width: 20%;
        }
        .database_length, .dt-buttons, .dataTables_filter{
            margin-bottom: 12px;
        }
        .buttons-html5, .buttons-print{
            font-size: 1.1rem;
        }
        .z-index{
            z-index: 1000;
        }
        .dtsb-searchBuilder{
            width: fit-content;
        }
    </style>
</head>
<a href="/admin/report"><button class="btn btn-outline-danger back-button mt-2 position-fixed z-index"><i class="fas fa-arrow-left"></i></button></a>
<a href="/admin"><button class="btn btn-outline-danger back-button mt-5 position-fixed z-index"><i class="fas fa-arrow-left"></i> Main</button></a>
<div id="fortext"></div>
{{ Aire::open()
  ->route('request')
  ->enctype("multipart/form-data")
  ->post()
  ->class('aire-picker')
  ->id('aire-picker')
   }}
<div style="text-align: center; display: flex; justify-content: end; align-items: baseline; column-gap: 10px; margin-right: 20px;">
    <div class="align-content-center"><strong>За период с</strong></div>
    {{Aire::month('m', '')->value(Illuminate\Support\Facades\Cache::get('date'))->name('date')}}
    <div class="align-content-center"><strong>до</strong></div>
    {{Aire::month('m', '')->value(Illuminate\Support\Facades\Cache::get('date_1'))->name('date_1')}}

    <button type="submit" class="btn btn-success flex" >Выбрать</button>
</div>
{{ Aire::close() }}
@if((Illuminate\Support\Facades\Cache::get('date') && Illuminate\Support\Facades\Cache::get('date_1')) != null)

    <table id="example" class="stripe wrap hover order-column cell-border" style="width: 100%; border-collapse: collapse !important;">
        <thead class="border border-dark">
        <tr>
            <th class="border border-dark" rowspan="2"> ID</th>
            <th class="border border-dark" rowspan="2">Категории</th>
            <th class="border border-dark" style="text-align: center" colspan="2">Открытые</th>
            <th class="border border-dark" style="text-align: center" colspan="2">В исполнении</th>
            <th class="border border-dark" style="text-align: center" colspan="2">Закрытые</th>
            <th class="border border-dark" style="text-align: center" colspan="2" >Отмененные</th>
            <th class="border border-dark" style="text-align: center" colspan="2">Всего</th>
        </tr>
        <tr>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
        </tr>
        </thead>
    </table>

    <script type="text/javascript" src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.3.2/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.0.0/js/dataTables.searchPanes.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.4.1/js/all.js" integrity="sha384-L469/ELG4Bg9sDQbl0hvjMq8pOcqFgkSpwhwnslzvVVGpDjYJ6wJJyYjvG3u8XW7" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/autofill/2.3.9/js/dataTables.autoFill.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.3.3/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>


    <script>
        $(document).ready(function() {
            var tableTitle = 'Отчет подкатегории';
            function export_format(data, columnIdx){
                switch (columnIdx) {
                    case 2:
                    case 3:
                        return data + ' открытых';
                    case 4:
                    case 5:
                        return data + ' в исполнении';
                    case 6:
                    case 7:
                        return data + ' закрытых';
                    case 8:
                    case 9:
                        return data + ' отмененных';
                    case 10:
                        return 'Общее ' + data;
                    case 11:
                        return 'Общая ' + data;
                    default:
                        return data;
                }
            }
            $('#example').DataTable( {
                columnDefs: [
                    {
                        targets: "_all",
                        className: 'dt-body-center dt-head-center'
                    }
                ],
                "language": {
                    "lengthMenu": "Показать _MENU_ записей",
                    "info":      'Показаны записи в диапазоне от _START_ до _END_ (В общем _TOTAL_)',
                    "search":  'Поиск',
                    "paginate": {
                        "previous": "Назад",
                        "next": "Дальше"
                    }

                },
                "processing": false,
                pageLength: 10,
                // dom: 'PQlfrtip',
                dom: 'Qlfrtip' + 'QBfrtip',

                ajax:
                    "{{ route('report_sub') }}",

                columns: [
                    {data: "id", name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'open_count', name: 'open_count'},
                    {data: 'open_sum', name: 'open_sum'},
                    {data: 'process_count', name: 'process_count'},
                    {data: 'process_sum', name: 'process_sum'},
                    {data: 'finished_count', name: 'finished_count'},
                    {data: 'finished_sum', name: 'finished_sum'},
                    {data: 'cencelled_count', name: 'cencelled_count'},
                    {data: 'cencelled_sum', name: 'cencelled_sum'},
                    {data: 'total_count', name: 'total_count'},
                    {data: 'total_sum', name: 'total_sum'},

                ],

                buttons: {
                    buttons: [
                        { extend: 'copyHtml5',
                            text: '<i class="fas fa-copy"></i>',
                            title: tableTitle,
                            titleAttr: 'Copy to Clipboard',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible',
                                format: {
                                    header: function ( data, columnIdx ) {
                                        return export_format(data, columnIdx);
                                    }
                                }
                            },
                        },
                        { extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i>',
                            title: tableTitle,
                            titleAttr: 'Export to Excel',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible',
                                format: {
                                    header: function ( data, columnIdx ) {
                                        return export_format(data, columnIdx);
                                    }
                                }
                            },
                        },
                        { extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i>',
                            title: tableTitle,
                            titleAttr: 'Export to PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible',
                                format: {
                                    header: function ( data, columnIdx ) {
                                        return export_format(data, columnIdx);
                                    }
                                }
                            },
                        },
                        { extend: 'print',
                            text: '<i class="fas fa-print"></i>',
                            title: tableTitle,
                            titleAttr: 'Print Table',
                            exportOptions: {
                                columns: ':visible:Not(.not-exported)',
                                rows: ':visible',
                                format: {
                                    header: function ( data, columnIdx ) {
                                        return export_format(data, columnIdx);
                                    }
                                }
                            },
                        },
                    ],
                    dom: {
                        button: {
                            className: 'btn btn-outline-primary'
                        }
                    }
                },

            });
            var divTitle = ''
                + '<div class="col-12 text-center text-md-left pt-4 display-2" style="text-align: center !important;">'
                + '<h1 class="text-dark">' + tableTitle + '</h1>'
                + '</div>';

            $("#fortext").append(divTitle);

        });
    </script>
@endif

