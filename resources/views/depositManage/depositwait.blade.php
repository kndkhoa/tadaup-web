@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Deposit Wait</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deposit Wait</li>
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
              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Deposit Wait
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>Customer ID</th>
                      <th>Customer Name</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>eWallet</th>
                      <th>Hash</th>
                      <th>Status</th>
                      <th>Create_At</th>
                      <th>Process</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @foreach($transactions_temps as $transaction_temp)     
                    <tr>
                      <td>{{$transaction_temp['customer_id']}}</td>
                      <td>{{$transaction_temp['full_name']}}</td>
                      <td>{{$transaction_temp['type']}}</td>
                      <td>{{$transaction_temp['amount']}}</td>
                      <td>{{$transaction_temp['eWallet']}}</td>
                      <td>{{$transaction_temp['transactionHash']}}</td>
                      <td>{{$transaction_temp['status']}}</td>
                      <td>{{$transaction_temp['created_at']}}</td>
                        
                        <td>
                          @if($transaction_temp->status == 'WAIT')
                          <form action="{{ route('approveWallet', $transaction_temp->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Approve" class="btn btn-success float-right" style="margin-right: 5px;">
                          </form>
                          @endif
                        </td>
                       
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