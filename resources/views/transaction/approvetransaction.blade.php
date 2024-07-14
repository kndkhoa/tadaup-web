@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaction Pending</h1>
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
              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Giao dịch chờ xử lý
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>ID</th>
                      <th>User ID</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Bank Name</th>
                      <th>Bank Account</th>
                      <th>FullName</th>
                      <th>Status</th>
                      <th>Create_At</th>
                      <th>Approve</th>
                      <th>Cancel</th>
                    </tr>
                    </thead>
                    <tbody>          
                    @foreach($transactions_temp as $transaction_temp)     
                    <tr>
                      <td>{{$transaction_temp['id']}}</td>
                      <td>{{$transaction_temp['user_id']}}</td>
                      <td>{{$transaction_temp['type']}}</td>
                      <td>{{$transaction_temp['amount']}}</td>
                      <td>{{$transaction_temp['bank_name']}}</td>
                      <td>{{$transaction_temp['bank_account']}}</td>
                      <td>{{$transaction_temp['fullname']}}</td>
                      <td>{{$transaction_temp['status']}}</td>
                      <td>{{$transaction_temp['created_at']}}</td>
                      <td><form action="{{ route('transaction.approve', $transaction_temp['id']) }}" method="POST" style="display: inline;">
                          @csrf
                            <button type="submit" name="action" value="approve">Approve</button>
                          </form>
                      </td>
                      <td><form action="{{ route('transaction.reject', $transaction_temp['id']) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" name="action" value="reject">Reject</button>
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