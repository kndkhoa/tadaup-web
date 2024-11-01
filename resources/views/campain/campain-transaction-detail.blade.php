@extends('layouts.layout')

@section('campain')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campain Transaction Detail</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Transaction Detail</li>
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
                      <th>Ewallet Customer ID</th>
                      <th>Txn Type	</th>
                      <th>Amount</th>
                      <th>Percent</th>
                      <th>Transaction Hash</th>
                      <th>Status</th>
                      @if($CampainFX_ID->status == 'ORIG')
                      <th>Approve</th>
                      <th>Reject</th>
                      @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($CampainFXTXN_ID as $campainFxTxn)
                    <tr>
                      <td>{{$campainFxTxn->customerID ?? ''}}</td>
                      <td>{{ maskString($campainFxTxn->ewalletCustomerID, 4, 4) }}</td>
                      <td>{{$campainFxTxn->txnType ?? ''}}</td>
                      <td>{{$campainFxTxn->amount ?? ''}}</td>
                      <td>{{$campainFxTxn->percent ?? ''}}</td>
                      <td>{{$campainFxTxn->transactionHash ?? ''}}</td>
                      <td>{{$campainFxTxn->status ?? ''}}</td>
                      @if(auth()->check() && auth()->user()->hasLevel('0'))
                      @if($CampainFX_ID->status == 'ORIG')
                        <td>
                          <form action="{{ route('campainFXTXN.approve', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Approve" class="btn btn-success float-right" style="margin-right: 5px;">
                          </form>
                        </td>
                        <td>
                          <form action="{{ route('campainFXTXN.reject', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Reject" class="btn btn-primary float-right" style="margin-right: 5px;">
                          </form>
                        </td>
                        @endif
                      @endif
                    </tr>
                    @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead">Amount Due</p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th>Campain Amount:</th>
                        <td>{{$CampainFX_ID->campain_amount ?? 0}} USD</td>
                      </tr>
                      <tr>
                        <th>Total Deposit:</th>
                        <td>{{$sumsAmount[0]->total_amount ?? 0}} USD</td>
                      </tr>
                      <tr>
                        <th>Percent:</th>
                        <td>{{$sumsAmount[0]->total_percent ?? 0}}%</td>
                      </tr>
                      <tr>
                        <th>Profit Amount:</th>
                        <td>{{$CampainFX_ID->profitMLM ?? 0}} USD</td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">
                @if($CampainFX_ID->status == 'DONE')
                  @if(auth()->check() && auth()->user()->hasLevel('0'))
                    <form action="{{ route('campainFXTXN.submit-payment', $CampainFX_ID->campainID) }}" method="POST" style="display: inline;">
                    @csrf
                      <button type="submit" value = "Submit Payment" class="btn btn-success float-right" name="action"><i class="far fa-credit-card"></i>Submit Payment</button>
                    </form>
                    <!-- <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                      <i class="fas fa-download"></i> Generate PDF
                    </button> -->
                  @endif
                  @endif
                  @if($CampainFX_ID->status == 'ORIG')
                    @if(auth()->check() && auth()->user()->hasLevel('0'))
                      <form action="{{ route('campainFX.run', $CampainFX_ID->campainID) }}" method="POST" style="display: inline;">
                      @csrf
                        <button type="submit" class="btn btn-success float-right" value="RUN" name="action" >Run</button>
                      </form>
                    @endif
                  @endif
                </div>
              </div>
              </br>
              <!-- Table row -->
              @if($CampainFX_ID->status == 'DONE' && $CampainFXTXN_ID_Done->isNotEmpty())
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>Customer ID</th>
                      <th>Ewallet Customer ID</th>
                      <th>Txn Type	</th>
                      <th>Amount</th>
                      <th>Percent</th>
                      <th>Transaction Hash</th>
                      <th>Status</th>
                      <th>Approve</th>
                      <th>Reject</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($CampainFXTXN_ID_Done as $campainFxTxn)
                    <tr>
                      <td>{{$campainFxTxn->customerID ?? ''}}</td>
                      <td>{{ $campainFxTxn->ewalletCustomerID ?? '' }}</td>
                      <td>{{$campainFxTxn->txnType ?? ''}}</td>
                      <td>{{$campainFxTxn->amount ?? ''}}</td>
                      <td>{{$campainFxTxn->percent ?? ''}}</td>
                      <td>{{$campainFxTxn->transactionHash ?? ''}}</td>
                      <td>{{$campainFxTxn->status ?? ''}}</td>
                      @if(auth()->check() && auth()->user()->hasLevel('0'))
                        <td>
                          <form action="{{ route('campainFXTXN.approve', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Approve" class="btn btn-success float-right" style="margin-right: 5px;">
                          </form>
                        </td>
                        <td>
                          <form action="{{ route('campainFXTXN.reject', $campainFxTxn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="submit" value="Reject" class="btn btn-primary float-right" style="margin-right: 5px;">
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
              @endif
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