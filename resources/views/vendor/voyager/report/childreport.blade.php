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
    <x-laravelYajra tableId="example1" stateSave="true" :dtColumns=$dtColumns :dtHeaders=$dtHeaders dom="Qlfrtip" serverSide="true" getData="{{ route('report_sub') }}" tableTitle="{{ __('Отчет подкатегории') }}" startDate="{{request()->input('date')}}" endDate="{{request()->input('date_1')}}" language="ru"></x-laravelYajra>

    <script>
        $(document).ready(function() {
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
        });
    </script>
@endif


@endsection
