@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Deposit Win</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deposit Win</li>
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
                      <ul>
                          @foreach($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
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
                        <i class="fas fa-globe"></i> Deposit Win
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>Campaign Name</th>
                      <th>Customer ID</th>
                      <th>Customer Name</th>
                      <th>Amount</th>
                      <th>Orig Person</th>
                      <th>Status</th>
                      <th>Updated</th>
                      <th>Interest Amount</th>
                      <th>Share MLM</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @foreach($CampainFXTXN as $transaction_temp)     
                    <tr>
                      <td>{{$transaction_temp['campainName']}}</td>
                      <td>{{$transaction_temp['customerID']}}</td>
                      <td>{{$transaction_temp['customer_name']}}</td>
                      <td>{{$transaction_temp['amount']}}</td>
                      <td>{{$transaction_temp['origPerson']}}</td>
                      <td>{{$transaction_temp['status']}}</td>
                      <td>{{$transaction_temp['updated_at']}}</td>
                      <td>
                        <form action="{{ route('depositIncome' , $transaction_temp->customerID ) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="number" id="amount" class="form-control" name="amount" step="any" min="0" style="width: 100px;">
                            <input type="submit" value="Deposit" class="btn btn-primary btn-sm">
                       </form>
                      </td>
                      <td>
                        <form action="{{ route('calculateMLM' , $transaction_temp->id ) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Share MLM" class="btn btn-primary btn-sm">
                       </form>
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