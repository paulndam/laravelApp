<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if(backpack_user()->role >= 1) {{-- Only display to admin --}}
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class='nav-icon fa fa-user'></i> Users</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('product') }}'><i class='nav-icon fa fa-shopping-cart'></i> Products</a></li>
@endif
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('order') }}'><i class='nav-icon fa fa-check'></i> Orders</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('orderitem') }}'><i class='nav-icon fa fa-th-list'></i> OrderItems</a></li>
@if(backpack_user()->role >= 1){{-- Only display to admin --}}
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('guest') }}'><i class='nav-icon fa fa-hourglass'></i> Guests</a></li>
@endif