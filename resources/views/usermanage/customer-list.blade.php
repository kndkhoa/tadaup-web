@extends('layouts.layout')

@section('userManage')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Management</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
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
                        Customer Name
                      </th>
                      <th>
                          Phone
                      </th>
                      <th>
                      Interest Ewallet
                      </th>
                      <th>
                          Email
                      </th>
                      <th>
                          Ewallet
                      </th>
                      <th>
                          Detail
                      </th>
                      <th>
                          SponserID
                      </th>
                  </tr>
              </thead>
              <tbody>
              @foreach($customers as $customer)
                  <tr>
                      <td>
                        {{$customer->customer_id}}
                      </td>
                      <td>
                        {{$customer->full_name}}
                      </td>
                      <td>
                        {{$customer->phone}}
                      </td>
                      <td>
                        {{$customer->interestEwallet}}
                      </td>
                      <td>
                        {{$customer->email}}
                      </td>
                      <td>
                        {{$customer->ewalletAddress}}
                      </td>
                      <td>
                        {{$customer->user_sponser_id}}
                      </td>
                      <td class="project-actions text-right">
                                  <form action="{{ route('showCustomerDetail', $customer['customer_id']) }}" method="POST" style="display: inline;">
                                      @csrf
                                      <input type="submit" value="View" class="btn btn-primary btn-sm">
                       </form>
                      </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>



    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection