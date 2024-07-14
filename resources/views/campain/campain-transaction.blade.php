@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campain Transaction</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Campain Transaction</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      
      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row">
            @foreach($listCampainNEW as $campainNew)
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
              <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    Campain ID: {{$campainNew->campainID ?? ''}}
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>{{$campainNew->campainName ?? ''}}</b></h2>
                      <p class="text-muted text-sm"><b>About: </b> {{$campainNew->campainDescription ?? ''}} </p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Amount:</b> {{$campainNew->campain_amount ?? ''}}</li>
                          </br>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Status:</b> {{$campainNew->status ?? ''}}</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="{{ asset('layout/dist/img/images.png')}}" alt="user-avatar" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                    @if(auth()->check() && auth()->user()->hasLevel('0'))
                    <form action="{{ route('campainFXTXN.campain-transaction-detail', $campainNew->campainID) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="submit" value="View" class="btn btn-sm btn-primary">
                    </form>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <nav aria-label="Contacts Page Navigation">
            <ul class="pagination justify-content-center m-0">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item"><a class="page-link" href="#">5</a></li>
              <li class="page-item"><a class="page-link" href="#">6</a></li>
              <li class="page-item"><a class="page-link" href="#">7</a></li>
              <li class="page-item"><a class="page-link" href="#">8</a></li>
            </ul>
          </nav>
        </div>
        <!-- /.card-footer -->
      </div>


      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row">
            @foreach($listCampainDONE as $campainDONE)
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
              <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                Campain ID: {{$campainDONE->campainID ?? ''}}
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>{{$campainDONE->campainName ?? ''}}</b></h2>
                      <p class="text-muted text-sm"><b>About: </b> {{$campainDONE->campainDescription ?? ''}}</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Amount:</b> {{$campainDONE->campain_amount ?? ''}}</li>
                        </br>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Status:</b> {{$campainDONE->status ?? ''}}</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="{{ asset('layout/dist/img/done1.png')}}" alt="user-avatar" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                  @if(auth()->check() && auth()->user()->hasLevel('0'))
                    <form action="{{ route('campainFXTXN.campain-transaction-detail', $campainDONE->campainID) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="submit" value="View" class="btn btn-sm btn-primary">
                    </form>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <nav aria-label="Contacts Page Navigation">
            <ul class="pagination justify-content-center m-0">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item"><a class="page-link" href="#">5</a></li>
              <li class="page-item"><a class="page-link" href="#">6</a></li>
              <li class="page-item"><a class="page-link" href="#">7</a></li>
              <li class="page-item"><a class="page-link" href="#">8</a></li>
            </ul>
          </nav>
        </div>
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection