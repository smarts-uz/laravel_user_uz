@extends('voyager::master')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Foydalanuvchi parolini o'zgartirish</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Mavjud parol</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" autocomplete="off" placeholder="Existing Password" value="">
                                </div>
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Yangi Parol</label>
                                        <input type="text" class="form-control" id="exampleInputPassword1" name="password" autocomplete="off" placeholder="New Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Yangi parolni tasdiqlang</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" autocomplete="off" placeholder="Confirm New Password">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success generate_password">Generate Password</button>
                                    <button type="button" class="btn btn-warning new_password">
                                        <i class="fas fa-copy"></i>
                                        <span>Copy new password</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .new_password{
            background-color: #f0ad4e;
            box-shadow: 0 6px #473317;
        }
        .new_password:hover{
            box-shadow: 0 4px #473317;
            top: 2px;
        }
        .new_password:active{
            box-shadow: 0 0 #473317;
            top: 6px;
        }
        .generate_password{
            background-color: #5cb85c;
            box-shadow: 0 6px #1B371C;
            margin-top: 10px;
        }
        .generate_password:hover{
            box-shadow: 0 4px #1B371C;
            top: 2px;
        }
        .generate_password:active{
            box-shadow: 0 0 #1B371C;
            top: 6px;
        }
    </style>

@endsection
