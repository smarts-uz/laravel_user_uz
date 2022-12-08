@extends('layouts.app2')

@section('content')

    <form method="POST" action="https://checkout.paycom.uz">
      <input type="text" hidden name="merchant" value="{{ config('payments.payme.merchant_id') }}"/>
      <input type="text" hidden name="amount" value="{{ $transaction->amount * 100 }}"/>
      <input type="text" hidden name="account[user_id]" value="{{ $transaction->user_id }}"/>
      <input type="submit" hidden id="btn" name="" value=""/>
    </form>
    <script>
    window.onload = function(){
      document.getElementById('btn').click();
    }
    </script>

@endsection
