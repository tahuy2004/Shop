@extends('client.index')

@section('main')
    <!-- Breadcrumb Section Start -->
    <div class="section">
        <!-- Breadcrumb Area Start -->
        <div class="breadcrumb-area bg-light">
            <div class="container-fluid">
                <div class="breadcrumb-content text-center">
                    <h1 class="title">Hỗ trợ</h1>
                    <ul>
                        <li><a href="/">Trang chủ</a></li>
                        <li class="active">Hỗ trợ</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Breadcrumb Area End -->
    </div>
    <!-- Breadcrumb Section End -->

    <!-- Contact Us Section Start -->
    <div class="section section-margin">
        <div class="container">
            <div class="row mb-n10">
                <div class="col-12 col-lg-8 mb-10">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h2 class="title pb-3">Phòng số ({{$chat->id}}) </h2>
                      
                        
                        <span></span>
                        <div class="title-border-bottom"></div>
                    </div>
                   <div style="border: 2px solid #ddd;padding: 15px 25px">

                 
                    <div class="chat-box" >
                        <div class="contentBlock" style="min-height: 200px">
                     
                        @foreach ($messages as $value)
                        <div style="margin-bottom: 10px; text-align: {{ $value->sender_id == Auth::id() ? 'right' : 'left' }};">
                             
                            <img src="{{  $value->sender->avatar ? Storage::url($value->sender->avatar) : asset('/theme/client/assets/images/logo/avata.jpg') }}" alt="User Image" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;margin-right: 5px">
                     
                            <span style="display: inline-block; max-width: 80%; line-height: 1.4; font-size: 14px; background-color: #f4f4f4; padding: 5px 10px; border-radius: 10px;color:black">{{ $value->content }}</span>                              
                            <span style="font-size: 10px; color: gray; margin-top: 5px; align-self: flex-end;">{{ $value->created_at->format('H:i') }}</span>
                        </div>
                                    @endforeach
                        </div>
                       
                    </div>
                    <p id="userOnline"></p>
                    <div class="d-flex mt-2">
                        <input type="text" placeholder="Gửi tin nhắn..." class="form-control" id="inputMessage" style="margin-right: 20px">
                        <button class="btn btn-dark" id="btnSendMessage">   <i class="fa fa-paper-plane"></i></button>
                    </div>
                   </div>
                </div>

                <div class="col-12 col-lg-4 mb-10">
                    
                    <!-- Contact Info Section -->
                    <div class="section-title">
                        <h2 class="title pb-3">Nhân viên hỗ trợ</h2>
                        <span></span>
                        <div class="title-border-bottom"></div>
                        <div class="avatar-wrapper" style="display: flex; justify-content: center; align-items: center; margin-top: 15px;">
                            <img style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;" src="{{ Storage::url($chat->staff->avatar) }}" alt="Avatar">
                        </div>                 
                      </div>
                    <div class="contact-info-wrapper mb-n6">
                        <div class="single-contact-info mb-6">
                            <div class="single-contact-icon">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="single-contact-title-content">
                                <h4 class="title">Tên nhân viên</h4>
                                <p>{{$chat->staff->name}}</p>
                            </div>
                        </div>
                        <div class="single-contact-info mb-6">
                            <div class="single-contact-icon">
                                <i class="fa fa-mobile"></i>
                            </div>
                            <div class="single-contact-title-content">
                                <h4 class="title">Điện Thoại</h4>
                                <p>{{$chat->staff->phone}}</p>
                            </div>
                        </div>
                        <div class="single-contact-info mb-6">
                            <div class="single-contact-icon">
                                <i class="fa fa-envelope-o"></i>
                            </div>
                            <div class="single-contact-title-content">
                                <h4 class="title">Email</h4>
                                <p><a href="mailto:fashionwave@gmail.com">{{$chat->staff->email}}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
<script>
    let chatId="{{$chat->id}}"
    let userSignIn = '{{ Auth::id() }}'
    let routeMessage = "{{ route('chat.sendMessage', $chat) }}"
 </script>
 @vite('resources/js/comment.js')
@endsection
