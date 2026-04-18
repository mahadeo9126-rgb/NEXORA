@php
    $leftChild = $user->children->firstWhere('position', 'left');
    $rightChild = $user->children->firstWhere('position', 'right');
@endphp

<ul>
    <!-- Left Child -->
    <li>
        @if($leftChild)
            <a href="/genealogy/{{ $leftChild->id }}">{{ $leftChild->username }} (L)</a>
            @if($level < 3)
                @include('partials.tree_node', ['user' => $leftChild, 'level' => $level + 1])
            @endif
        @else
            <a href="#" style="border-style: dashed; color: #aaa;">Empty (L)</a>
        @endif
    </li>

    <!-- Right Child -->
    <li>
        @if($rightChild)
            <a href="/genealogy/{{ $rightChild->id }}">{{ $rightChild->username }} (R)</a>
            @if($level < 3)
                @include('partials.tree_node', ['user' => $rightChild, 'level' => $level + 1])
            @endif
        @else
            <a href="#" style="border-style: dashed; color: #aaa;">Empty (R)</a>
        @endif
    </li>
</ul>
