@extends('layouts.layout')

@section('profile')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fas fa-globe"></i> Infomation
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <address>
                  <b>FullName:</b> {{$customer->full_name}}<br>
                  <b>Address:</b> {{$customer->address}}<br>
                  <b>Phone:</b> {{$customer->phone}}<br>
                  <b>User_ID:</b> {{$customer->user_id}}<br>
                  <b>Link Ref:</b> <a href = "https://tadaup.com/register?sponserid={{$user->user_id}}">https://tadaup.com/register?sponserid={{$user->user_id}}</a>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <address>
                  <b>Email:</b> {{$user->email}}<br>
                  <b>Bank Account:</b> {{$customer->bank_account}}<br>
                  <b>Bank Name:</b> {{$customer->bank_name}}<br>
                  <b>User_Sponser_ID:</b> {{$customer->user_sponser_id}}<br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>User_name:</b> {{$user->username}}<br>
                  <b>Status:</b> {{$user->status}}<br>
                  <b>Ewallet Address:</b> {{$customer->ewalletAddress}}<br>
                  <b>Ewallet Network:</b> {{$customer->ewalletNetwork}}<br>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Member
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                    <thead>
                    <tr>
                      <th>Tier</th>
                      <th>User ID</th>
                      <th>Fullname</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>User Sponser ID</th>
                    </tr>
                    </thead>
                    <tbody>                
                    @php
                        $count = 0
                    @endphp
                    @foreach($customers as $row)
                        @php
                            $count = $count + 1;
                        @endphp
                    <tr>
                      <td>{{$count}}</td>
                      <td>{{$row['user_id']}}</td>
                      <td>{{$row['full_name']}}</td>
                      <td>{{$row['phone']}}</td>
                      <td>{{$row['address']}}</td>
                      <td>{{$row['user_sponser_id']}}</td>
                    </tr>
                    @endforeach          
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->

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
              </div>
              <!-- /.row -->

                
              
              <!-- /.row -->

              

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