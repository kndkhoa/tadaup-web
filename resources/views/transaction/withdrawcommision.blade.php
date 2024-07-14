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
      @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
        @endif
    <!-- Main content -->
    <section class="content">
      <form action="{{ route('withdraw') }}" method="post">
      @csrf
      @method('PUT')
        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Info</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="inputName">Fullname</label>
                  <input type="text" id="inputName" class="form-control" name="fullname" disabled="disabled" value="{{$customer->full_name ?? ''}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Phone</label>
                  <input type="text" id="inputName" class="form-control" name="phone" disabled="disabled" value="{{$customer->phone ?? ''}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Email</label>
                  <input type="text" id="inputName" class="form-control" name="phone" disabled="disabled" value="{{$user->email ?? ''}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Commission Balance</label>
                  <input type="text" id="inputName" class="form-control" name="phone" disabled="disabled"value="{{$transactions[0]->balance_after ?? 0}}">
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

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
                  <label for="inputEstimatedBudget">Amount withdraw</label>
                  <input type="number" id="inputEstimatedBudget" class="form-control" name="amount" >
                </div>
                <div class="form-group">
                  <label for="inputSpentBudget">BankName</label></br>
                  <select id="bankList" name="bankList">
                    <option value="">Select a Bank</option>
                    @foreach ($banks as $bank)
                        <!-- <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option> -->
                        <option value="{{ $bank['code'] }}" @if ($customer->bank_name == $bank['code']) selected @endif>
                        {{ $bank['name'] }}
                        </option>
                    @endforeach
                  </select>
                  @if ($errors->has('bank_name'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first('bank_name') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  @endif
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Bank Account Number</label>
                  <input type="text" id="inputEstimatedDuration" class="form-control" name="bank_account" value="{{$customer->bank_account ?? ''}}">
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Bank City</label>
                  <input type="text" id="inputEstimatedDuration" class="form-control" name="bank_city" >
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Fullname</label>
                  <input type="text" id="inputEstimatedDuration" class="form-control" name="fullname" value="{{$customer->full_name ?? ''}}">
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
            <input type="submit" value="Withdraw" class="btn btn-success float-right">
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection