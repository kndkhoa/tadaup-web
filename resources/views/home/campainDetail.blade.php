@extends('layouts.home')
@section('campainList')
<section>
          <div class="container">
               <h2>{{$CampainFX_ID['campainName'] ?? ''}}</h2>

               <p class="lead">
                    <i class="fa fa-user"></i> {{$CampainFX_ID['campainID'] ?? ''}} &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-calendar"></i> {{$CampainFX_ID['fromDate'] ?? ''}} - {{$CampainFX_ID['toDate'] ?? ''}} &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-eye"></i> {{$CampainFX_ID['status'] ?? ''}}
               </p>

               <img src="images/other-image-fullscreen-1-1920x700.jpg" class="img-responsive" alt="">

               <br>

               <h3>{{$CampainFX_ID['campainDescription'] ?? ''}}</h3>
               

               <p>{!! $CampainFX_ID->content !!}</p>

               <br>
               <br>

               <div class="row">

                    <div class="col-md-8 col-xs-12">
                         <h4>Comments</h4>

                         <p>No comments found.</p>

                         <br>
                         
                         <h4>Leave a Comment</h4>

                         <form action="#" class="form">

                              <div class="row">
                                   <div class="col-sm-6 col-xs-6">
                                        <div class="form-group">
                                             <label class="control-label">Name</label>

                                             <input type="text" name="name" class="form-control">
                                        </div>
                                   </div>

                                   <div class="col-sm-6 col-xs-6">
                                        <div class="form-group">
                                             <label class="control-label">Email</label>

                                             <input type="email" name="email" class="form-control">
                                        </div>
                                   </div>
                              </div>

                              <div class="form-group">
                                   <label class="control-label">Message</label>

                                   <textarea name="comment" class="form-control" rows="10" autocomplete="off"></textarea>
                              </div>

                              <button type="submit" class="section-btn btn btn-primary">Submit</button>
                         </form>
                    </div>
               </div>
          </div>
     </section>
@endsection