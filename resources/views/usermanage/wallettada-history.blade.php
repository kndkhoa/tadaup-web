@extends('layouts.layout')

@section('userManage')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Wallet Tadaup History</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Wallet Tadaup History</li>
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
          <h3 class="card-title">Wallet Tadaup History</h3>
          @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
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
                      Wallet Tadaup
                      </th>
                      <th>
                      Type
                      </th>
                      <th>
                          Amount
                      </th>
                      <th>
                      Wallet
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
              @foreach($walletTadaupHist as $walletTadaup)
                  <tr>
                      <td>
                        {{$walletTadaup->user_id}}
                      </td>
                      <td>
                        {{$walletTadaup->type}}
                      </td>
                      <td>
                        {{$walletTadaup->amount}}
                      </td>
                      <td>
                        INCOME
                      </td>
                      <td>
                      {{$walletTadaup->description}}
                      </td>
                      <td>
                        {{$walletTadaup->created_at}}
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