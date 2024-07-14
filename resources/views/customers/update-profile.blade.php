@extends('layouts.layout')

@section('profile')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Change Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Change Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <form action="{{ route('update') }}" method="post">
      @csrf
      @method('PUT')
        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Info</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="inputName">FullName</label>
                  <input type="text" id="inputName" class="form-control" name="fullname" value="{{$customer->full_name}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Phone</label>
                  <input type="number" id="inputName" class="form-control" name="phone" value="{{$customer->phone}}">
                </div>
                <div class="form-group">
                  <label for="inputDescription">Address</label>
                  <textarea id="inputDescription" class="form-control" rows="4" name="address">{{$customer->address}}</textarea>
                </div>
                <div class="form-group">
                  <label for="inputName">Ewallet Address</label>
                  <input type="text" id="inputName" class="form-control" name="ewallet_adress" value="{{$customer->ewalletAddress}}">
                </div>
                <div class="form-group">
                  <label for="inputName">Ewallet Network</label>
                  <input type="text" readonly id="inputName" class="form-control" name="ewallet_network" value="BEP20">
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Info</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="inputEstimatedBudget">ID/Passport Front</label>
                  <input type="text" id="inputEstimatedBudget" class="form-control" name="id_font" value="{{$customer->image_font_id}}">
                </div>
                <div class="form-group">
                  <label for="inputSpentBudget">ID/Passport Back</label>
                  <input type="text" id="inputSpentBudget" class="form-control" name="id_back" value="{{$customer->image_back_id}}">
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Bank Number</label>
                  <input type="text" id="inputEstimatedDuration" class="form-control" name="bank_number" value="{{$customer->bank_account}}">
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Bank Name</label>
                  <select id="bankList" name="bankList">
                    <option value="">Select a Bank</option>
                    @foreach ($banks as $bank)
                    <option value="{{ $bank['code'] }}" @if ($customer->bank_name == $bank['code']) selected @endif>
                        {{ $bank['name'] }}
                    </option>
                    @endforeach
                  </select>
                  @if ($errors->has('bank_name'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first('bank_name') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  @endif
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Sponser ID</label>
                  <input readonly type="number" id="inputEstimatedDuration" class="form-control" name="userid_sponser" value="{{$customer->user_sponser_id}}">
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <a href="{{ route('profile') }}" class="btn btn-secondary">Cancel</a>
            <input type="submit" value="Update" class="btn btn-success float-right">
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    @endsection