@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Report Trading</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Active Core</li>
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

        

        <div class="card">
        <div class="card-header">
          <h3 class="card-title">User Active Core</h3>
 
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
                     Customer ID
                    </th>
                    <th>
                      Customer Name
                    </th>
                    <th>
                    Activity Latest
                    </th>
                    <th>
                    Activity Offline
                    </th>
                    <th>
                    Active Core
                    </th>
                    <th>
                    Updated At
                    </th>
                  
                 </tr>
              </thead>
              <tbody>
                  @foreach($customerReport as $transaction_temp)
                  <tr>
                      <td>
                        {{$transaction_temp->customer_id}}
                      </td>
                      <td>
                        {{$transaction_temp->customer_name}}
                      </td>
                      <td>
                        {{$transaction_temp->activeLastest}}
                      </td>
                      <td>
                        {{$transaction_temp->activeOffline}}
                      </td>
                      <td>
                        {{$transaction_temp->activeScore}}
                      </td>
                      <td>
                        {{$transaction_temp->updated_at}}
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