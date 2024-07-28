@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campain Detail</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Campain Detail</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
    @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Campain Detail</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
              <div class="row">
                <div class="col-12 col-sm-4">
                  <div class="info-box bg-light">
                    <div class="info-box-content">
                      <span class="info-box-text text-center text-muted">Campain budget</span>
                      <span class="info-box-number text-center text-muted mb-0">{{$CampainFX_ID->campain_amount ?? ''}}</span>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-4">
                  <div class="info-box bg-light">
                    <div class="info-box-content">
                      <span class="info-box-text text-center text-muted">Campain amount spent</span>
                      <span class="info-box-number text-center text-muted mb-0">{{$sumsAmount[0]->total_amount ?? 0}}</span>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-4">
                  <div class="info-box bg-light">
                    <div class="info-box-content">
                      <span class="info-box-text text-center text-muted">Campain Status</span>
                      <span class="info-box-number text-center text-muted mb-0">{{$CampainFX_ID->status ?? ''}}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <h4>Recent Activity</h4>
                    <div class="post">
                    {!! $CampainFX_ID->content !!}
                    </div>
                    
                </div>
              </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
              <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{$CampainFX_ID->campainName ?? ''}}</h3>
              <p class="text-muted">{{$CampainFX_ID->campainDescription ?? ''}}</p>
              <br>
              <div class="text-muted">
                <p class="text-sm">Ewallet Address
                  <b class="d-block">{{$CampainFX_ID->ewalletAddress ?? ''}}</b>
                </p>
                <p class="text-sm">Ewallet Network
                  <b class="d-block">{{$CampainFX_ID->network ?? ''}}</b>
                </p>
                <p class="text-sm">Status
                  <b class="d-block">{{$CampainFX_ID->status ?? ''}}</b>
                </p>
                <p class="text-sm">Campain Duration
                  <b class="d-block">{{$CampainFX_ID->fromDate ?? ''}} - {{$CampainFX_ID->toDate ?? ''}}</b>
                </p>
                <p class="text-sm">Profit Amount Balance
                  <b class="d-block">{{$CampainFX_ID->profitAmount ?? ''}}</b>
                </p>
                <p class="text-sm">Profit Percent
                  <b class="d-block">{{$CampainFX_ID->profirPercent ?? ''}}</b>
                </p>
                <p class="text-sm">Link Referal
                  <b class="d-block"><a href = "https://tadaup.com/campaign/{{$CampainFX_ID->campainID}}?sponserid={{$customer->user_id}}">https://tadaup.com/campaign/{{$CampainFX_ID->campainID}}?sponserid={{$customer->user_id}}</a></b>
                </p>
              </div>
              
              <div class="text-center mt-5 mb-3">
                @if($CampainFX_ID->status == 'ORIG')
                  <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Deposit</button>
                  @if(auth()->check() && auth()->user()->hasLevel('0'))
                    <form action="{{ route('campainFX.run', $CampainFX_ID->campainID) }}" method="POST" style="display: inline;">
                    @csrf
                      <button type="submit" class="btn btn-sm btn-warning" value="run" name="action" >Run</button>
                    </form>
                  @endif
                @endif
                @if($CampainFX_ID->status == 'RUN')
                  @if(auth()->check() && auth()->user()->hasLevel('0'))
                    <form action="{{ route('campainFX.done', $CampainFX_ID->campainID) }}" method="POST" style="display: inline;">
                    @csrf
                      <button type="submit" class="btn btn-sm btn-warning" value="run" name="action" >Done1</button>
                    </form>
                  @endif
                @endif
                
                
              </div>

              <!-- Button trigger modal -->
              

              <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Deposit Campain ID {{$CampainFX_ID->campainID ?? ''}}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                     <form action="{{ route('campainFX.deposit') }}" method="POST" style="display: inline;">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                              <label for="recipient-name" class="col-form-label">Campain ID:</label>
                              <input readonly type="text" class="form-control" id="recipient-name" name="campain_id" value="{{$CampainFX_ID->campainID ?? ''}}">
                            </div>
                            <div class="form-group">
                              <label for="recipient-name" class="col-form-label">Ewallet Customer ID:</label>
                              <input readonly type="text" class="form-control" id="recipient-name" name="ewallet_cutomer_id" value="{{$customer->ewalletAddress ?? ''}}">
                            </div>
                            <div class="form-group">
                              <label for="message-text" class="col-form-label">Ewallet Campain ID:</label>
                              <input readonly type="text" class="form-control" id="recipient-name" name="ewallet_campain_id" value="{{$CampainFX_ID->ewalletAddress ?? ''}}">
                            </div>
                            <div class="form-group">
                              <label for="message-text" class="col-form-label">Ewallet Network:</label>
                              <input readonly type="text" class="form-control" id="recipient-name" name="ewallet_network"  value = "{{$CampainFX_ID->network ?? ''}}">
                            </div>
                            <div class="form-group">
                              <label for="message-text" class="col-form-label">Amount</label>
                              <input type="number" class="form-control" name="amount_deposit" id="recipient-name">
                            </div>
                            <div class="form-group">
                              <label for="message-text" class="col-form-label">Transaction Hash</label>
                              <input type="text" class="form-control" name="transactionHash" id="recipient-name">
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <input type="submit" value="Deposit" class="btn btn-primary">
                        </div>
                      </form>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection