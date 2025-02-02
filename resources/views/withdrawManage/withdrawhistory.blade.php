@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Withdraw History</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Withdraw History</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">


            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
            
              @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      @foreach ($errors->all() as $error)
                          {{ $error }}
                        @endforeach
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                </div>
                @endif
              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Withdraw History
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>Customer ID</th>
                      <th>Customer Name</th>
                      <th>Amount</th>
                      <th>Currency</th>
                      <th>Hash</th>
                      <th>eWallet</th>
                      <th>Orig Person</th>
                      <th>Status</th>
                      <th>Error Msg</th>
                      <th>Create_At</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @foreach($transactions_temp as $transaction_temp)     
                    <tr>
                      <td>{{$transaction_temp['user_id']}}</td>
                      <td>{{$transaction_temp['customer_name']}}</td>
                      <td>{{$transaction_temp['amount']}}</td>
                      <td>{{$transaction_temp['currency']}}</td>
                      <td>{{$transaction_temp['transactionHash'] ?? null}}</td>
                      <td>{{$transaction_temp['eWallet']}}</td>
                      <td>{{$transaction_temp['origPerson']}}</td>
                      <td>{{$transaction_temp['status']}}</td>
                      <td>{{$transaction_temp['errorMsg']}}</td>
                      <td>{{$transaction_temp['created_at']}}</td>
                    </tr>     
                    @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              
              <!-- /.row -->

              <!-- this row will not appear when printing -->
             
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection