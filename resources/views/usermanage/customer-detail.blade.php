@extends('layouts.layout')

@section('campain')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Customer Detail</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Customer Detail</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      
      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row">
            @foreach($Customer_connection as $cutomer)
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
              <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    <b>Type: </b> {{$cutomer->type ?? ''}}
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <p class="text-muted text-sm"><b>Cutomer ID: </b> {{$cutomer->customer_id ?? ''}} </p>
                      <p class="text-muted text-sm"><b>Cutomer Name: </b> {{$cutomer->customer_name ?? ''}} </p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Link URL:</b> {{$cutomer->link_url ?? ''}}</li>
                          </br>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Username:</b> {{$cutomer->user_name ?? ''}}</li>
                        </br>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> <b>Password:</b> {{$cutomer->password ?? ''}}</li>
                      </ul>
                    </div>
                    <!-- <div class="col-5 text-center">
                      <img src="{{ asset('layout/dist/img/images.png')}}" alt="user-avatar" class="img-circle img-fluid">
                    </div> -->
                  </div>
                </div>
                <form action="{{ route('deleteConnection', $cutomer['id'])  }}" method="post">
                @csrf
                <input type="submit" value="Delete" class="btn btn-danger float-right">
                </form>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        
        <!-- /.card-footer -->
      </div>


      @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
      @endif

        <div class="row">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Customer Connection</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <form action="{{ route('creatConnection') }}" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="inputName">CustomerID</label>
                  <input type="text" readonly id="inputid" class="form-control" name="customerid" value="{{$customerByID->customer_id}}">
                </div>
                <div class="form-group">
                  <label for="inputName">FullName</label>
                  <input type="text" readonly id="inputName" class="form-control" name="fullname" value="{{$customerByID->full_name}}">
                </div>
                <div class="form-group">
                  <label for="inputEstimatedDuration">Type</label><br/>
                  <select id="bankList" name="type">
                    <option value="">Select type</option>
                    <option value="FUND">FUND</option>
                    <option value="MT4">MT4</option>
                    <option value="VPS">VPS</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="inputName">Link</label>
                  <input type="text" id="inputLink" class="form-control" name="link" value="">
                </div>
                <div class="form-group">
                  <label for="inputDescription">UserName</label>
                  <input type="text" id="inputUserName" class="form-control" name="username" value="">
                </div>
                <div class="form-group">
                  <label for="inputName">Password</label>
                  <input type="text" id="inputpassword" class="form-control" name="password" value="">
                </div>
               
              </div>
              <input type="submit" value="Update" class="btn btn-success float-right">
              </form>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Chat History</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="inputEstimatedBudget">Chat Bot</label>
                  <textarea id="inputDescription" class="form-control" rows="20" name="chathistory"></textarea>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">CCCD Front</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
              <div class="form-group  text-center">
                  <img src="{{ asset('storage/' . $customerByID->image_font_id) }}"  width = '500px' height='350px'>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">CCCD Back</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group  text-center">
                  <img src="{{ asset('storage/' . $customerByID->image_back_id )}}" width = '500px' height='350px' >
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                      <div class="col-12">
                        <h4>
                          <i class="fas fa-globe"></i>TreeView Member
                        </h4>
                      </div>
                      <!-- /.col -->
                  </div>
                    <div id="tree-container" class="tree">
                        <!-- Tree will be rendered here -->
                    </div>
              
                  <script>
                    var treeData = @json($tree);
                    if (!Array.isArray(treeData)) {
                        treeData = [treeData];
                    }

                    function createTreeView(data) {
                      if (!Array.isArray(data)) {
                        console.error("Data is not an array:", data);
                        return '';
                      }
                      if(!data) return '';
                      var html = '<ul>';
                      data.forEach(function(item) {
                        console.log("Processing item:", item);
                          html += '<li>';
                          html += '<a href="#">' + (item.text || 'No Text') + ' - ' + (item.id) + '</a>';
                          if (item.children && item.children.length > 0) {
                              html += createTreeView(item.children);
                          }
                          html += '</li>';
                      });
                      html += '</ul>';
                      return html;
                    }

                    $(document).ready(function(){
                        $('#tree-container').html(createTreeView(treeData));
                    });
                  </script>
                  </table>
          </div>




      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection