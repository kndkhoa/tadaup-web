@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Share Commission MLM</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Share Commission MLM</li>
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
                <h3 class="card-title">Share Commission MLM</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <form action="{{ route('calculateMLM') }}" method="post">
                  @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="inputEstimatedBudget">Amount</label>
                    <input type="text" id="inputEstimatedBudget" class="form-control" name="amount" >
                  </div>
                  <div class="form-group">
                    <label for="inputSpentBudget">FullName</label></br>
                    <select id="customer_id" name="customer_id">
                      <option value="">FullName</option>
                      @foreach($customers as $customer)
                      <option value="{{$customer['customer_id']}}">{{$customer['customer_id']}} - {{$customer['full_name']}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12">
                    <input type="submit" value="Share" class="btn btn-success float-right">
                  </div>
                </div>
              </form>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="card">
        <div class="card-header">
          <h3 class="card-title">User Management</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped projects">
              <thead>
                <tr>
                    <th>
                     ID
                    </th>
                    <th>
                     Customer ID
                    </th>
                    <th>
                      Customer Name
                    </th>
                    <th>
                      Type
                    </th>
                    <th>
                      Amount
                    </th>
                    <th>
                    eWallet
                    </th>
                    <th>
                      Description
                    </th>
                    <th>
                      Create At
                    </th>
                 </tr>
              </thead>
              <tbody>
                  @foreach($transaction_temp_mlm as $transaction_temp)
                  <tr>
                      <td>
                        {{$transaction_temp->id}}
                      </td>
                      <td>
                        {{$transaction_temp->user_id}}
                      </td>
                      <td>
                        {{$transaction_temp->customer_name}}
                      </td>
                      <td>
                        {{$transaction_temp->type}}
                      </td>
                      <td>
                        {{$transaction_temp->amount}}
                      </td>
                      <td>
                        {{$transaction_temp->eWallet}}
                      </td>
                      <td>
                        {{$transaction_temp->description}}
                      </td>
                      <td>
                        {{$transaction_temp->created_at}}
                      </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>

      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection