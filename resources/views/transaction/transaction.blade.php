@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaction History</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
    @endif
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">


            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fas fa-globe"></i> Profile.
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <address>
                  <b>FullName:</b> {{$customer->full_name ?? ''}}<br>
                  <b>Address:</b> {{$customer->address ?? ''}}<br>
                  <b>Phone:</b> {{$customer->phone ?? ''}}<br>
                  <b>User_ID:</b> {{$customer->user_id ?? ''}}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <address>
                  <b>Email:</b> {{$user->email ?? ''}}<br>
                  <b>Bank Account:</b> {{$customer->bank_account ?? ''}}<br>
                  <b>Bank Name:</b> {{$customer->bank_name ?? ''}}<br>
                  <b>User_Sponser_ID:</b> {{$customer->user_sponser_id ?? ''}}<br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>User_name:</b> {{$user->username ?? ''}}<br>
                  <b>Status:</b> {{$user->status ?? ''}}<br>
                  <b>Current Balace:</b> {{$transactions[0]->balance_after ?? 0}} USD<br>
                  <b>Ewallet Address:</b> {{$customer->ewalletAddress ?? ''}}<br>
                  <b>Ewallet Network:</b> {{$customer->ewalletNetwork ?? ''}}<br>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Transaction History
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>No</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Balance After</th>
                      <th>Currency</th>
                      <th>Create_At</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @php
                        $count = 0
                    @endphp
                    @foreach($transactions as $transaction)
                        @php
                            $count = $count + 1;
                        @endphp      
                    <tr>
                      <td>{{$count}}</td>
                      <td>{{$transaction['type'] ?? ''}}</td>
                      <td>{{$transaction['amount' ?? '']}}</td>
                      <td>{{$transaction['balance_after'] ?? ''}}</td>
                      <td>{{$transaction['currency'] ?? ''}}</td>
                      <td>{{$transaction['created_at'] ?? ''}}</td>
                    </tr>     
                    @endforeach
                    </tbody>
                  </table>

                    </br></br></br>
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Transaction Waitting
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>No</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Bank Name</th>
                      <th>Bank Account</th>
                      <th>FullName</th>
                      <th>Status</th>
                      <th>Create_At</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @php
                        $count = 0
                    @endphp
                    @foreach($transactions_temp as $transaction_temp)
                        @php
                            $count = $count + 1;
                        @endphp      
                    <tr>
                      <td>{{$count}}</td>
                      <td>{{$transaction_temp['type'] ?? ''}}</td>
                      <td>{{$transaction_temp['amount'] ?? ''}}</td>
                      <td>{{$transaction_temp['bank_name'] ?? ''}}</td>
                      <td>{{$transaction_temp['bank_account'] ?? ''}}</td>
                      <td>{{$transaction_temp['fullname'] ?? ''}}</td>
                      <td>{{$transaction_temp['status'] ?? ''}}</td>
                      <td>{{$transaction_temp['created_at'] ?? ''}}</td>
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