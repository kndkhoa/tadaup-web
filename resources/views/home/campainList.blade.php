@extends('layouts.home')
@section('campainList')
<section>
          <div class="container">
               <div class="text-center">
                    <h1>{{ request()->routeIs('campainNew') ? 'Campain New' : '' }}</h1>
                    <h1>{{ request()->routeIs('campainRun') ? 'Campain Running' : '' }}</h1>
                    <h1>{{ request()->routeIs('campainDone') ? 'Campain Done' : '' }}</h1>
                    <br>

                    <p class="lead">Discover Forex Freedom: Unlock high returns with easy access to Forex trading. Join our free webinars, get expert tips, and start trading securely with our top-rated platform. Begin your journey today!</p>
               </div>
          </div>
     </section>

     <section class="section-background">
          <div class="container">
               <div class="row">
                    <div class="col-lg-3 pull-right col-xs-12">
                         <div class="form">
                              <form action="#">
                                   <div class="form-group">
                                        <label class="control-label">Blog Search</label>

                                        <div class="input-group">
                                             <input type="text" class="form-control" placeholder="Search for...">
                                             <span class="input-group-btn">
                                                  <button class="btn btn-default" type="button">Go!</button>
                                             </span>
                                        </div>
                                   </div>
                              </form>
                         </div>

                         <br>

                         <label class="control-label">List campain</label>

                         <ul class="list">
                            @foreach($listCampain as $campain)
                              <li><a href="blog-post-details.html">{{$campain['campainName']}}</a></li>
                              @endforeach
                         </ul>
                    </div>

                    <div class="col-lg-9 col-xs-12">
                         <div class="row">
                             @foreach($listCampain as $campain)
                              <div class="col-sm-6">
                                   <div class="courses-thumb courses-thumb-secondary">
                                        <div class="courses-top">
                                             <div class="courses-image">
                                                  <img src="{{ asset('layout/home/images/tadaup.png')}}" class="img-responsive" alt="">
                                             </div>
                                             <div class="courses-date">
                                                  <span title="Author"><i class="fa fa-user"></i> {{$campain['campainID']}}</span>
                                                  <span title="Date"><i class="fa fa-calendar"></i> {{$campain['fromDate']}} - {{$campain['toDate']}}</span>
                                                  <span title="Views"><i class="fa fa-eye"></i> {{$campain['status']}}</span>
                                             </div>
                                        </div>

                                        <div class="courses-detail">
                                             <h3><a href="{{ route('campainDetail', ['id' => $campain['campainID']]) }}">{{$campain['campainName']}}</a></h3>
                                        </div>

                                        <div class="courses-info">
                                             <a href="{{ route('campainDetail', ['id' => $campain['campainID']]) }}" class="section-btn btn btn-primary btn-block">Read More</a>
                                        </div>
                                   </div>
                              </div>
                              @endforeach
                         </div>
                    </div>
               </div>
          </div>
     </section>
@endsection