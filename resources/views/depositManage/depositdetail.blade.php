@extends('layouts.layout')

@section('campain')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Deposit Manage</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deposit Manage</li>
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
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                <strong>Campain Name</strong>
                  <address>
                    {{$CampainFX_ID->campainName}}</strong><br>
                    <strong>CampainID:</strong> {{$CampainFX_ID->campainID}}<br>
                    <strong>Campain Amount:</strong> {{$CampainFX_ID->campain_amount}}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                <strong>Ewallet Address</strong>
                  <address>
                    {{$CampainFX_ID->ewalletAddress}}<br>
                    <strong>Network:</strong> {{$CampainFX_ID->network}}<br>
                    <strong>Status:</strong> {{$CampainFX_ID->status}}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>FromDate:</b>{{$CampainFX_ID->fromDate}}<br>
                  <br>
                  <b>ToDate:</b> {{$CampainFX_ID->toDate}}<br>
                  <b>Currency:</b> {{$CampainFX_ID->currency}}
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>Customer ID</th>
                      <th>Customer Name</th>
                      <th>Order Code</th>
                      <th>Order Code Partner</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Approve</th>
                      <th>Search</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($CampainFXTXN_ID as $campainFxTxn)
                    <tr>
                      <td>{{$campainFxTxn->customerID ?? ''}}</td>
                      <td>{{$campainFxTxn['customer_name']}}</td>
                      <td>{{$campainFxTxn->transactionHash ?? '' }}</td>
                      <td>{{$campainFxTxn->transactionHashPartner ?? ''}}</td>
                      <td>{{$campainFxTxn->amount ?? ''}}</td>
                      <td>{{$campainFxTxn->txnDescription ?? ''}}</td>
                      <td>{{$campainFxTxn->status ?? ''}}</td>
                      @if($campainFxTxn->status == 'WAIT')
                        <td>
                          <form action="{{ route('depositApprove', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Approve" class="btn btn-success float-right" style="margin-right: 5px;">
                          </form>
                        </td>
                        <td>
                          <form action="{{ route('depositReject', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Search" class="btn btn-primary float-right" style="margin-right: 5px;">
                          </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

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