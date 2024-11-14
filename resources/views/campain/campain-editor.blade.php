@extends('layouts.layout')

@section('campain')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Campaign Form</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Campaign Editor Form</li>
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
      <div class="container-fluid">
      <form action="{{ route('campain.save') }}" method="POST" style="display: inline;">
        @csrf
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Campaign Form</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">Campaign Name</label>
                    <input type="hidden" class="form-control" id="campainID" name="campainID" value=" {{ $CampainFX_ID->campainID ?? '' }}">
                    <input type="text" class="form-control" id="campain_name" name="campain_name" placeholder="Enter campain name" value=" {{ $CampainFX_ID->campainName ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Profit MLM</label>
                    <input type="text" class="form-control" id="profit_mlm" name="profit_mlm" placeholder="Enter profit MLM" value="{{$CampainFX_ID->profitMLM ?? ''}}">
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
              <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputPassword1">origPerson</label>
                    <input type="text" class="form-control" id="origPerson" name="origPerson" placeholder="Enter telegram id" value="{{$CampainFX_ID->origPerson ?? ''}}">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Profit Percent</label>
                    <input type="text" class="form-control" id="profit_percent" name="profit_percent" placeholder="Enter profit percent" value="{{$CampainFX_ID->profitPercent ?? ''}}">
                </div>
                <!-- /.form-group -->
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label for="inputDescription">Description</label>
                  <textarea id="inputDescription" class="form-control" rows="4" name="campain_description" >{{$CampainFX_ID->campainDescription ?? ''}}</textarea>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Content</label>
                    <textarea id="summernote" name="campain_content" rows="4">
                        {{$CampainFX_ID->content ?? ''}}
                    </textarea>
                </div>
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('campain-new') }}" class="btn btn-secondary">Cancel</a>
                            <input type="submit" value="Submit" class="btn btn-success float-right">
                    </div>
                </div>
                <!-- /.form-group -->
              </div>

              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            
          </div>
        </div>
        </form>
        <!-- /.card -->

        <!-- /.card -->
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection