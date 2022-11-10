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
                                    <input type="text" class="form-control" id="exampleInputPassword1" autocomplete="off" placeholder="Existing Password" value="{{Hash::make($user->password)}}">
                                </div>
                                <form action="{{route('voyager.reset.password.store',['user'=>$user->id])}}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Yangi Parol</label>
                                        <input type="text" value="" id="reset_password" required class="form-control" name="password" placeholder="New Password">
                                    </div>
                                    <div class="form-group">
                                        <label>Yangi parolni takrorlang</label>
                                        <input type="password" id="password_confirmation" required class="form-control" name="password_confirmation" placeholder="Confirm New Password">
                                    </div>
                                    @error('password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>

                                <div class="modal-footer">
                                    <button type="button" onClick="randomPassword(10);" class="btn btn-success generate_password">Generate Password</button>
                                    <button onclick="new_password()" type="button" class="btn btn-warning new_password">
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
    <script>
        function new_password() {
            var copyText = document.getElementById("reset_password");
            copyText.select();
            document.execCommand("copy");
            alert("Yangi paroldan nusxa olindi: " + copyText.value);
        }
        function randomPassword(length) {
            var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>ABCDEFGHIJKLMNOPQRSTU1234567890";
            var pass = "";
            for (var x = 0; x < length; x++) {
                var i = Math.floor(Math.random() * chars.length);
                pass += chars.charAt(i);
            }
            document.getElementById('reset_password').value = pass;
            document.getElementById('password_confirmation').value = pass;
        }
    </script>
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
