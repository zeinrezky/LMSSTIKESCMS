@if (is_string($item))
    <li class="header text-white text-center" style="font-size: 16px; color: #fff">{{ $item }}</li>
@elseif (isset($item['header']))
    <li class="header">{{ $item['header'] }}</li>
@elseif (isset($item['search']) && $item['search'])
    <form action="{{ $item['href'] }}" method="{{ $item['method'] }}" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="{{ $item['input_name'] }}" class="form-control" placeholder="
            {{ $item['text'] }}
          ">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                  <i class="fas fa-search"></i>
                </button>
              </span>
        </div>
      </form>
@else

    @php
        $show = 'display: none';
        $thisRole = session()->get('user.dosen.role_id');

        // superadmin
        if($thisRole == 63){
            $show = '';

        // sme

        }else{

            if($thisRole == 1 && $item['when_sme']){
                $show = '';
            }

            if($thisRole == 3 && ($item['when_approv'] ?? 0)){
                $show = '';
            }

            if($thisRole != 1 && $item['when_reviewer_approv']){
                $show = '';
            }

            if($thisRole == 0 && !$item['admin_only']){
                $show = '';
            }
        }

    @endphp

    <li class="{{ $item['class'] }}" style="{{$show}}">
        <a href="{{ $item['href'] }}"
           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
        >
            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
            <span>
                {{ $item['text'] }}
            </span>

            @if (isset($item['label']))
                <span class="pull-right-container">
                    <span class="label label-{{ $item['label_color'] ?? 'primary' }} pull-right">{{ $item['label'] }}</span>
                </span>
            @elseif (isset($item['submenu']))
                <span class="pull-right-container">
                    <i class="fas fa-angle-left pull-right"></i>
                </span>
            @endif
        </a>
        @if (isset($item['submenu']))
            <ul class="{{ $item['submenu_class'] }}">
                @each('adminlte::partials.menu-item', $item['submenu'], 'item')
            </ul>
        @endif
    </li>
@endif
