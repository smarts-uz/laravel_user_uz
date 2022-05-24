<!doctype html>
<html lang="en">
<head>
    <link href="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.css" rel="stylesheet">
    <!--Regular Datatables CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.3.2/css/searchBuilder.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchpanes/2.0.0/css/searchPanes.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css"/>
</head>

   <div class="container">
    <table id="example2" class="display nowrap" style="width: 100%">
        <thead>
        <tr style="text-align: center;">
            
        </tr>
            <th></th>
            <th colspan="2" style="text-align: center">Открытые</th>
            <th colspan="2" style="text-align: center">В исполнении</th>
            <th colspan="2" style="text-align: center">Закрытые</th>
            <th colspan="2" style="text-align: center">Отмененные</th>
            <th colspan="2" style="text-align: center">Всего</th>
        </tr>
        </tr>
            <th>Категории</th>
            <th>Кол-во</th>
            <th>Сумма</th>
            <th>Кол-во</th>
            <th>Сумма</th>
            <th>Кол-во</th>
            <th>Сумма</th>
            <th>Кол-во</th>
            <th>Сумма</th>
            <th>Кол-во</th>
            <th>Сумма</th>
        </tr>
        </thead>
    </table>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.3.2/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.0.0/js/dataTables.searchPanes.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example2').DataTable( {
                // dom: 'PQlfrtip',
                dom: 'Qlfrtip',
                ajax:
                    "{{ route('child.report'), '$id' }}",

                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'open_count', name: 'open_count'},
                    {data: 'open_sum', name: 'open_sum'},
                    {data: 'process_count', name: 'process_count'},
                    {data: 'process_sum', name: 'process_sum'},
                    {data: 'finished_count', name: 'finished_count'},
                    {data: 'finished_sum', name: 'finished_sum'},
                    {data: 'finished_sum', name: 'finished_sum'},
                    {data: 'finished_sum', name: 'finished_sum'},
                    {data: 'total_count', name: 'total_count'},
                    {data: 'total_sum', name: 'total_sum'},

                ]
            });
        });
    </script>

<div class="pl-4 pt-4">
    <a href="/" class="btn btn-danger">{{__('Назад')}}</a>
</div>
</div>
