@extends('layouts.layout')

@section('userManage')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Wallet Tadaup</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Wallet Tadaup</li>
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
          <h3 class="card-title">Wallet Tadaup</h3>
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
                      Wallet Name
                      </th>
                      <th>
                          Value
                      </th>
                      <th>
                          Address
                      </th>
                      <th>
                          Deposit Amount
                      </th>
                  </tr>
              </thead>
              <tbody>
              @foreach($walletTadaups as $walletTadaup)
                  <tr>
                      <td>
                        {{$walletTadaup->id}}
                      </td>
                      <td>
                        {{$walletTadaup->walletName}}
                      </td>
                      <td>
                        {{$walletTadaup->value}}
                      </td>
                      <td>
                        {{$walletTadaup->address}}
                      </td>
                      <td class="project-actions text-left">
                        @if($walletTadaup['id'] == '1')
                        <form action="{{ route('depositWalletTadaIncome') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="number" id="amount" class="form-control" name="amount" step="any" min="0" style="width: 100px;"><br/>
                            <input type="submit" value="Deposit" class="btn btn-primary btn-sm">
                            @endif   
                       </form>
                      </td>
                      <td class="project-actions text-left">
                            
                       @if($walletTadaup['id'] == '2')
                          <form action="{{ route('showWalletTadaHistory') }}" method="GET" style="display: inline;">
                            @csrf
                            <input type="submit" value="View History" class="btn btn-primary btn-sm">
                          @endif       
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