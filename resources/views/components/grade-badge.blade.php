@if($grade == 'A')
    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">A (Sangat Baik)</span>
@elseif($grade == 'B')
    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">B (Baik)</span>
@elseif($grade == 'C')
    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">C (Cukup)</span>
@else
    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">D (Kurang)</span>
@endif