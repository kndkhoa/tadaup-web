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
                      No
                      </th>
                      <th>
                      Customer ID
                      </th>
                      <th>
                        Customer Name
                      </th>
                      <th>
                          Phone
                      </th>
                      <th>
                          Email
                      </th>
                      <th>
                          Role
                      </th>
                      <th>
                        Active ProTrader
                      </th>
                      <th>
                         Detail
                      </th>
                  </tr>
              </thead>
              <tbody>
                @php $i = 0
                @endphp
              @foreach($customers as $customer)
                  @php
                    $i = $i + 1
                  @endphp
                  <tr>
                      <td>
                        {{$i}}
                      </td>
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
                        {{$customer->email}}
                      </td>
                      <td>
                        @php
                            if ($customer->role_id == 1) {
                                $role = 'admin';
                            } elseif ($customer->role_id == 2) {
                                $role = 'user';
                            } elseif ($customer->role_id == 3) {
                                $role = 'proTrader';
                            } else {
                                $role = 'unknown'; // Optional fallback
                            }
                        @endphp
                        {{ $role }}
                      </td>

                      
                      <td class="project-actions text-center">
                        @php
                          if ($customer->role_id == 2) {  @endphp 
                            <form action="{{ route('activeProTrader', $customer['customer_id']) }}" method="POST" style="display: inline;">
                                      @csrf
                                      <input type="submit" value="Active ProTrader" class="btn btn-primary btn-sm">
                                  </form>
                          @php  }
                        @endphp    
                      </td>
                              
                      <td class="project-actions text-center">
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