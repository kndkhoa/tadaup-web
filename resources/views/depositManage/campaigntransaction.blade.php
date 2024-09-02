@extends('layouts.layout')

@section('transaction')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campaign Transaction</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Campaign Transaction</li>
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
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Campain List</h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body p-0">
                  <table class="table table-striped projects">
                      <thead>
                          <tr>
                              <th style="width: 1%">
                                  ID
                              </th>
                              <th style="width: 20%">
                                  Name
                              </th>
                              <th style="width: 30%">
                                  Description
                              </th>
                              <th>
                                  Amount
                              </th>
                              <th>
                                  Status
                              </th>
                              <th>
                                  Create At
                              </th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($campaigns as $campain)
                          <tr>
                              <td>
                                {{$campain['campainID']}}
                              </td>
                              <td>
                                  <a>
                                    {{$campain['campainName']}}
                                  </a>
                                  <br/>
                              </td>
                              <td>{{$campain['campainDescription']}}</td>
                              <td>{{$campain['campain_amount']}}</td>
                              <td class="project-state">
                                  <span class="badge badge-success">{{$campain['status']}}</span>
                              </td>
                              <td>{{$campain['created_at']}}</td>
                              <td class="project-actions text-right">
                                  <form action="{{ route('depositDetail', $campain['campainID']) }}" method="POST" style="display: inline;">
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