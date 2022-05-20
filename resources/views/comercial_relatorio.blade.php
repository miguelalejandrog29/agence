<div class="table-responsive">
    @if (isset($consultores))
    @foreach ($consultores as $nombre => $relatorio)
    <table class="table table-striped table-hover table-bordered text-center shadow caption-top">
        <caption>{{ $nombre }}</caption>
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Período') }}</th>
                <th scope="col">{{ __('Receita Líquida') }}</th>
                <th scope="col">{{ __('Custo Fixo') }}</th>
                <th scope="col">{{ __('Comissão') }}</th>
                <th scope="col">{{ __('Lucro') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_receita_liquida = 0;
            $total_custo_fixo = 0;
            $total_comissao = 0;
            $total_lucro = 0;
            $locale = ['es' => 'es_ES', 'en' => 'en_US', 'pt_BR' => 'pt_BR'];
            @endphp
            @foreach ($relatorio as $item)
            @php
            $lucro = $item->receita_liquida - ($item->custo_fixo + $item->comissao);
            $dateObj = new DateTime($item->anno . '-' . $item->mes);
            $dateFormated = IntlDateFormatter::formatObject($dateObj, "MMMM 'de' y", $locale[App::getLocale()]);
            @endphp
            <tr>
                <td>{{ $dateFormated }}</td>
                <td>{{ number_format($item->receita_liquida, 2, ',', '.') }}</td>
                <td>{{ number_format($item->custo_fixo, 2, ',', '.') }}</td>
                <td>{{ number_format($item->comissao, 2, ',', '.') }}</td>
                <td @php if ($lucro < 0) { echo 'class="text-danger"' ; } @endphp>
                    {{ number_format($lucro, 2, ',', '.') }}
                </td>
            </tr>
            @php
            $total_receita_liquida += $item->receita_liquida;
            $total_custo_fixo += $item->custo_fixo;
            $total_comissao += $item->comissao;
            $total_lucro += $lucro;
            @endphp
            @endforeach
            <tr>
                <th scope="row">{{ __('Saldo') }}</th>
                <td>{{ number_format($total_receita_liquida, 2, ',', '.') }}</td>
                <td>{{ number_format($total_custo_fixo, 2, ',', '.') }}</td>
                <td>{{ number_format($total_comissao, 2, ',', '.') }}</td>
                <td>{{ number_format($total_lucro, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    @endif
</div>