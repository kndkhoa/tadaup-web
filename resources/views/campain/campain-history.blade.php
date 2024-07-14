@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campain History</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Campain History</li>
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

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Campain Running</h3>

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
                          Campain Amount
                      </th>
                      <th style="width: 8%" class="text-center">
                          Status
                      </th>
                      <th style="width: 20%">
                      </th>
                  </tr>
              </thead>
              <tbody>
                @foreach($listCampainRUN as $campain)
                  <tr>
                      <td>
                        {{$campain['campainID']}}
                      </td>
                      <td>
                          <a>
                            {{$campain['campainName']}}
                          </a>
                          <br/>
                          <small>
                              Created {{$campain['created_at']}}
                          </small>
                      </td>
                      <td>{{$campain['campainDescription']}}</td>
                      <td>{{$campain['campain_amount']}}</td>
                      <td class="project-state">
                          <span class="badge badge-success">{{$campain['status']}}</span>
                      </td>
                      <td class="project-actions text-right">
                          <form action="{{ route('campainFX.detail', $campain['campainID']) }}" method="POST" style="display: inline;">
                              @csrf
                              <input type="submit" value="View" class="btn btn-primary btn-sm">
                          </form>
                          @if(auth()->check() && auth()->user()->hasLevel('0'))
                          <form action="{{ route('campainFX.edit', $campain['campainID']) }}" method="POST" style="display: inline;">
                              @csrf
                              <input type="submit" value="Edit" class="btn btn-info btn-sm">
                          </form>
                          <form action="{{ route('campainFX.delete', $campain['campainID']) }}" method="POST" style="display: inline;">
                              @csrf
                              <input type="submit" value="Delete" class="btn btn-danger btn-sm float-right">
                          </form>
                         
                          @endif
                      </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->


      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Campain Done</h3>

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
                          Campain Description
                      </th>
                      <th>
                          Profit Percent
                      </th>
                      <th style="width: 8%" class="text-center">
                          Status
                      </th>
                      <th style="width: 20%">
                      </th>
                  </tr>
              </thead>
              <tbody>
                @foreach($listCampainDONE as $campain)
                  <tr>
                      <td>
                        {{$campain['campainID']}}
                      </td>
                      <td>
                          <a>
                            {{$campain['campainName']}}
                          </a>
                          <br/>
                          <small>
                              Created {{$campain['created_at']}}
                          </small>
                      </td>
                      <td>
                        {{$campain['campainDescription']}}
                      </td>
                      <td class="project_progress">
                          <div class="progress progress-sm">
                              <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{$campain['profirPercent']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$campain['profirPercent']}}%">
                              </div>
                          </div>
                          <small>
                              {{$campain['profirPercent']}}%
                          </small>
                      </td>
                      <td class="project-state">
                          <span class="badge badge-success">{{$campain['status']}}</span>
                      </td>
                      <td class="project-actions text-right">
                          <form action="{{ route('campainFX.detail', $campain['campainID']) }}" method="POST" style="display: inline;">
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
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection