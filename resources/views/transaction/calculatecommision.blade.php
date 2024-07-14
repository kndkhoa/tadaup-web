@extends('layouts.layout')

@section('transaction')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Withdraw Commission</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Withdraw Commission</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <form action="{{ route('calculate') }}" method="post">
      @csrf
      @method('PUT')
        <div class="row">
          

          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Bank Info</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="inputEstimatedBudget">Amount</label>
                  <input type="text" id="inputEstimatedBudget" class="form-control" name="amount" >
                </div>
                <div class="form-group">
                  <label for="inputSpentBudget">FullName</label></br>
                  <select id="user_id" name="user_id">
                    <option value="">Select FullName</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer['user_id'] }}">{{ $customer['user_id'] }} - {{ $customer['full_name'] }}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('user_id'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first('user_id') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  @endif
                </div>
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

        </div>
        <div class="row">
          <div class="col-12">
            <a href="{{ route('transaction-history') }}" class="btn btn-secondary">Cancel</a>
            <input type="submit" value="Deposit" class="btn btn-success float-right">
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection