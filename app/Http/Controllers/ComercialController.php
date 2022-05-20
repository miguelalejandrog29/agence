<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComercialRequest;
use App\Models\Factura;
use App\Models\Usuario;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use IntlDateFormatter;

class ComercialController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $viewData = [];

        $viewData['consultores'] = Usuario::select('cao_usuario.co_usuario', 'cao_usuario.no_usuario')
            ->leftJoin('permissao_sistema', 'permissao_sistema.co_usuario', '=', 'cao_usuario.co_usuario')
            ->where('permissao_sistema.co_sistema', 1)
            ->where('permissao_sistema.in_ativo', 'S')
            ->whereIn('permissao_sistema.co_tipo_usuario', [0, 1, 2])->get();

        $viewData['meses'] = $this->get_months_names();

        $viewData['yearRange'] = Factura::select(
            DB::raw('SQL_NO_CACHE YEAR(MIN(data_emissao)) as yearMin, YEAR(MAX(data_emissao)) as yearMax')
        )->first();

        return view('comercial', $viewData);
    }

    public function show_relatorio(ComercialRequest $request)
    {
        $viewData = [];
        $consultores = json_decode($request->consultores, true);
        $startDate = (new DateTime($request->startdate))->format('Y-m-d');
        $endDate = (new DateTime($request->enddate))->format('Y-m-t');

        foreach ($consultores as $consultor) {
            $usuario = Usuario::select('cao_usuario.no_usuario')->where('cao_usuario.co_usuario', $consultor)->first();
            $viewData['consultores'][$usuario->no_usuario] = Factura::select(
                DB::raw(
                    'SQL_NO_CACHE month(cao_fatura.data_emissao) as mes, year(cao_fatura.data_emissao) as anno,  
                    sum(cao_fatura.valor - (cao_fatura.valor * cao_fatura.total_imp_inc / 100)) as receita_liquida,
                    sum((cao_fatura.valor - (cao_fatura.valor * cao_fatura.total_imp_inc / 100)) * cao_fatura.comissao_cn / 100 ) as comissao,
                    cao_salario.brut_salario as custo_fixo'
                )
            )
                ->leftJoin('cao_os', 'cao_os.co_os', '=', 'cao_fatura.co_os')
                ->leftJoin('cao_usuario', 'cao_usuario.co_usuario', '=', 'cao_os.co_usuario')
                ->leftJoin('cao_salario', 'cao_salario.co_usuario', '=', 'cao_usuario.co_usuario')
                ->where('cao_usuario.co_usuario', $consultor)
                ->whereBetween('cao_fatura.data_emissao', [$startDate, $endDate])
                ->groupBy('anno', 'mes', 'custo_fixo')->orderBy('mes', 'ASC')->orderBy('anno', 'ASC')->get();
        }

        return view('comercial_relatorio', $viewData);
    }

    public function show_grafico(ComercialRequest $request)
    {
        $viewData = [];
        $consultoresquery = [];
        $consultores = json_decode($request->consultores, true);
        $startDate = (new DateTime($request->startdate))->format('Y-m-d');
        $endDate = (new DateTime($request->enddate))->format('Y-m-t');
        $total_custo_fixo = 0;

        foreach ($consultores as $consultor) {
            $usuario = Usuario::select('cao_usuario.no_usuario')->where('cao_usuario.co_usuario', $consultor)->first();
            $salario = DB::table('cao_salario')->select('brut_salario')->where('co_usuario', $consultor)->first();
            if (!is_null($salario)) {
                $total_custo_fixo += $salario->brut_salario;
            }

            $consultoresquery[$usuario->no_usuario] = Factura::select(
                DB::raw(
                    'SQL_NO_CACHE date_format(caol.cao_fatura.data_emissao, "%m") as mes, year(cao_fatura.data_emissao) as anno,  
                    sum(cao_fatura.valor - (cao_fatura.valor * cao_fatura.total_imp_inc / 100)) as receita'
                )
            )
                ->leftJoin('cao_os', 'cao_os.co_os', '=', 'cao_fatura.co_os')
                ->leftJoin('cao_usuario', 'cao_usuario.co_usuario', '=', 'cao_os.co_usuario')
                ->where('cao_usuario.co_usuario', $consultor)
                ->whereBetween('cao_fatura.data_emissao', [$startDate, $endDate])
                ->groupBy('anno', 'mes')->orderBy('mes', 'ASC')->orderBy('anno', 'ASC')->get();
        }

        $total_consultores = count($consultores) != 0 ? count($consultores) : 1;
        $custo_fixo_medio = number_format($total_custo_fixo / $total_consultores, 2, ',', '.');

        $fecha_inicial = new DateTime($startDate);
        $fecha_final = new DateTime($endDate);
        $intervalo = DateInterval::createFromDateString('1 Month');
        $periodo = new DatePeriod($fecha_inicial, $intervalo, $fecha_final);
        $locale = ['es' => 'es_ES', 'en' => 'en_US', 'pt_BR' => 'pt_BR'];
        $dateTemp = [];
        $fecha_periodo = [];
        foreach ($periodo as $dt) {
            $fecha_periodo[] = $dt->format("Y-m");
            $dateTemp[] = IntlDateFormatter::formatObject($dt, "MMMM 'de' y", $locale[App::getLocale()]);
        }
        $viewData['fecha_labels'] = json_encode($dateTemp);

        $chartSeries = [];
        foreach ($consultoresquery as $consult => $items) {
            $cData = [];
            foreach ($fecha_periodo as $fecha) {
                $flat = false;
                foreach ($items as $item) {
                    $itemfecha = $item->anno . '-' . $item->mes;
                    if (strcmp($fecha, $itemfecha)  === 0) {
                        $cData[] = number_format($item->receita, 2, ',', '.');
                        $flat = true;
                        break;
                    }
                }
                if (!$flat) {
                    $cData[] = 0;
                }
            }

            $chartSeries[] = array(
                "name" => $consult,
                "type" => "column",
                "data" => $cData
            );
            unset($cData);
        }

        $cData = [];
        foreach ($fecha_periodo as $fecha) {
            $cData[] = $custo_fixo_medio;
        }

        $chartSeries[] = array(
            "name" => "Cuxto Fixo Medio",
            "type" => "line",
            "data" => $cData
        );
        unset($cData);

        $viewData['chartSeries'] = json_encode($chartSeries);

        return view('comercial_grafico', $viewData);
    }

    public function show_pizza(ComercialRequest $request)
    {
        $viewData = [];
        $consultoresquery = [];
        $consultores = json_decode($request->consultores, true);
        $startDate = (new DateTime($request->startdate))->format('Y-m-d');
        $endDate = (new DateTime($request->enddate))->format('Y-m-t');

        foreach ($consultores as $consultor) {

            $consultoresquery[] = Factura::select(
                DB::raw(
                    'sum(cao_fatura.valor - (cao_fatura.valor * cao_fatura.total_imp_inc / 100)) as total_receita_liquida, 
                    cao_usuario.no_usuario'
                ),
            )
                ->leftJoin('cao_os', 'cao_os.co_os', '=', 'cao_fatura.co_os')
                ->leftJoin('cao_usuario', 'cao_usuario.co_usuario', '=', 'cao_os.co_usuario')
                ->where('cao_usuario.co_usuario', $consultor)
                ->whereBetween('cao_fatura.data_emissao', [$startDate, $endDate])
                ->groupBy('no_usuario')->get();
        }

        $chartSeries = [];
        $chartLabels = [];
        foreach ($consultoresquery as $listconsultores) {
            foreach ($listconsultores as $consult) {
                $chartSeries[] = number_format($consult->total_receita_liquida, 2, ',', '.');
                $chartLabels[] = $consult->no_usuario;
            }
        }

        $viewData['chartLabels'] = json_encode($chartLabels);
        $viewData['chartSeries'] = json_encode($chartSeries, JSON_NUMERIC_CHECK);

        return view('comercial_pizza', $viewData);
    }

    /**
     * estraer el nombre de los meses de acuerdo al locale
     */
    private function get_months_names(): array
    {
        $months = [];
        $locale = ['es' => 'es_ES', 'en' => 'en_US', 'pt_BR' => 'pt_BR'];

        for ($i = 1; $i <= 12; $i++) {
            $monthNum  = $i;
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = IntlDateFormatter::formatObject($dateObj, 'MMMM', $locale[App::getLocale()]);
            $months[$i] = $monthName;
        }

        return $months;
    }
}
