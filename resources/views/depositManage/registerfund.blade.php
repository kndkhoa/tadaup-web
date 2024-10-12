@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Register Fund</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Register Fund</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->

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

        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Register Fund</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <form action="{{ route('registerFundByID') }}" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="inputName">Campaign ID</label>
                  <input type="text" readonly id="inputid" class="form-control" name="campaign_id" value="{{$CampainFX_ID->campainID}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Campaign Name</label>
                  <input type="text" readonly id="inputName" class="form-control" name="campaign_name" value="{{$CampainFX_ID->campainName}}">
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Customer</label><br/>
                  <select id="customer_id" name="customer_id">
                    <option value="">FullName</option>
                    @foreach($customers as $customer)
                    <option value="{{$customer['customer_id']}}">{{$customer['customer_id']}} - {{$customer['full_name']}}</option>
                    @endforeach
                  </select>
                  
                </div>
                <div class="form-group">
                  <label for="inputName">Amount</label>
                  <input type="number" id="amount" class="form-control" name="amount" value="">
                </div>
               
              </div>
              <input type="submit" value="Register" class="btn btn-success float-right">
              </form>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
  
        </div>

      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection