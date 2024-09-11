@foreach($pages as $page)
    <tr @if($depth > 0) id="children-{{ $prefix }}" class="collapse child-page" @else class="parent-page"
        data-toggle="collapse" data-target="#children-{{ $page->id }}" @endif
        @if($collapse) data-toggle="collapse" data-target="#children-{{ $page->id }}" @endif>

        <td @if($depth > 0) style="padding-left: {{ $depth * 40 }}px" @endif >
            <i class="fas {{ $page->children->isNotEmpty() ? 'fa-chevron-down' : 'fa-list' }} mr-1"></i>
            {{ $page->getTranslation(app()->getLocale())->title }}
        </td>

        <td>{{ $page->slug }}</td>
        <td>{{ $page->order }}</td>
        <td class="text-right">
            <a href="{{ route('admin.pageplus.change_order', ['page' => $page->id, 'action' => 'down']) }}"
               class="btn btn-info btn-sm" data-toggle="tooltip" title="{!! __('pageplus::messages.move_up') !!}">
                <i class="fas fa-arrow-up"></i>
            </a>
            <a href="{{ route('admin.pageplus.change_order', ['page' => $page->id, 'action' => 'up']) }}"
               class="btn btn-info btn-sm" data-toggle="tooltip" title="{!! __('pageplus::messages.move_down') !!}">
                <i class="fas fa-arrow-down"></i>
            </a>
            <a href="{{ route($page->slug) }}" class="btn btn-warning btn-sm" target="_blank"
               data-toggle="tooltip" title="{!! __('pageplus::messages.go_to_page') !!}">
                <i class="fas fa-link"></i>
            </a>
            <a href="{{ route('admin.pageplus.translate', $page->id) }}" class="btn btn-primary btn-sm"
               data-toggle="tooltip" title="{!! __('pageplus::messages.translate') !!}">
                <i class="fas fa-language"></i>
            </a>
            <a href="{{ route('admin.pageplus.edit', $page->id) }}" class="btn btn-primary btn-sm" data-toggle="tooltip"
               title="{!! __('pageplus::messages.edit') !!}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="{{ route('admin.pageplus.delete', $page->id) }}"
               onclick="return confirm('{{ __('client.sure_you_want_delete') }}')" class="btn btn-danger btn-sm"
               data-toggle="tooltip" title="{!! __('pageplus::messages.delete') !!}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
    @if($page->children->isNotEmpty())
        @include(AdminTheme::moduleView('pageplus', 'page_children'), ['pages' => $page->children, 'depth' => $depth + 1, 'prefix' => $page->id, 'collapse' => true])
    @endif

@endforeach



