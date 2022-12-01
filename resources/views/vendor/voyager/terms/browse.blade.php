@extends('voyager::master')

@section('content')
    <script src="https://cdn.ckeditor.com/ckeditor5/12.0.0/classic/ckeditor.js"></script>
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title" id="exampleModalLongTitle">Правила сервиса</h2>
                            </div>
                            <form action="{{'terms.store'}}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <h4>Text UZ</h4>
                                    <textarea name="text_uz" id="editor1">
                                        {{$terms->text_uz}}
                                    </textarea>
                                </div>
                                <div class="modal-body">
                                    <h4>Text RU</h4>
                                    <textarea name="text_ru" id="editor2">
                                         {{$terms->text_ru}}
                                    </textarea>
                                </div>
                                <button type="submit" class="btn btn-primary float-right" style="margin-top: 15px">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor1' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor2' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
@endsection
