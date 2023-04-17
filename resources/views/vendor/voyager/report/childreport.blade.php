@extends("vendor.voyager.report.appreport")

@section("reportContent")

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
            <th class="border border-dark" style="text-align: center" colspan="2">Открытые Ответ</th>
            <th class="border border-dark" style="text-align: center" colspan="2">В исполнении</th>
            <th class="border border-dark" style="text-align: center" colspan="2">Закрытые</th>
            <th class="border border-dark" style="text-align: center" colspan="2">Не завершено</th>
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
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
            <th class="border border-dark">Кол-во</th>
            <th class="border border-dark">Сумма</th>
        </tr>
        </thead>
    </table>



    <script>
        $(document).ready(function() {
            var tableTitle = 'Отчет подкатегории';
            function export_format(data, columnIdx){
                switch (columnIdx) {
                    case 3:
                    case 4:
                        return data + ' открытых';
                    case 5:
                    case 6:
                        return data + ' открытых ответ';
                    case 7:
                    case 8:
                        return data + ' в исполнении';
                    case 9:
                    case 10:
                        return data + ' закрытых';
                    case 11:
                    case 12:
                        return data + ' не завершено';
                    case 13:
                    case 14:
                        return data + ' отмененных';
                    case 15:
                    case 16:
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
                    {data: 'response_count', name: 'response_count'},
                    {data: 'response_sum', name: 'response_sum'},
                    {data: 'process_count', name: 'process_count'},
                    {data: 'process_sum', name: 'process_sum'},
                    {data: 'finished_count', name: 'finished_count'},
                    {data: 'finished_sum', name: 'finished_sum'},
                    {data: 'not_complete_count', name: 'not_complete_count'},
                    {data: 'not_complete_sum', name: 'not_complete_sum'},
                    {data: 'cancelled_count', name: 'cancelled_count'},
                    {data: 'cancelled_sum', name: 'cancelled_sum'},
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


@endsection
