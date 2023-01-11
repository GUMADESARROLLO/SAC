<?php
namespace App\Http\Controllers;
use App\Models\Inventario;
use App\Models\Clientes;
use App\Models\Vendedor;
use App\Models\Liquidacion_a_6meses;
use App\Models\Liquidacion_a_12meses;
use App\Models\ClientesHistorico3M;
use App\Models\ClientesMora;
use App\Models\ClientesHistoricoFactura;
use App\Models\NivelPrecio;
use App\Models\Bodega;
use App\Models\ArticuloFavoritos;
use Illuminate\Http\Request;
use App\Models\Lotes;
use App\Models\Pedido;
use App\Models\PedidoComentario;
use App\Models\Usuario;
use App\Models\Factura;
use App\Models\Estadisticas;
use App\Models\MasterData;
use App\Models\PlanCrecimiento;
use App\Models\Promocion;
use Illuminate\Support\Str;
Use UxWeb\SweetAlert\SweetAlert;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psy\Readline\Hoa\Console;
use PDF;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use Session;

class HomeController extends Controller {
    public function __construct()
    {
        $this->middleware('auth');
    }   
    
    public function getHome()
    {  
        $Normal ='';
        $SAC = '';
        $Lista_SAC = Usuario::getUsuariosSAC();  
        
        if (Session::get('rol') == '1' || Session::get('rol') == '2' || Session::get('rol') == '9') {
            $SAC = 'active';
        } else {
            $Normal = 'active';
        }
        
        $promocion = Promocion::orderBy('fechaInicio')->where('Activo','S')->get();
        return view('Principal.Home', compact('Lista_SAC','SAC','Normal', 'promocion'));
        
    }

    public function Comiciones()
    {  
        $Ruta = 'F09';
        $Mes   = '1';
        $Anno   = '2023';

        $Comision = MasterData::getData($Ruta,$Mes,$Anno);
        //return response()->json($Comision);
        return view('Principal.Comiciones',compact('Comision'));
    }

    public function getEstadistiacas()
    {  
        $Normal ='';
        $SAC = '';
        $Lista_SAC = Usuario::getUsuariosSAC();  
        
        if (Session::get('rol') == '1' || Session::get('rol') == '2' || Session::get('rol') == '9') {
            $SAC = 'active';
        } else {
            $Normal = 'active';
        }
        
        return view('Principal.Estadisticas', compact('Lista_SAC','SAC','Normal'));
        
    }

    public function getStats($d1,$d2)
    { 
        $obj = Estadisticas::getData($d1,$d2);
        return response()->json($obj);
        //return view('Principal.Estadisticas', compact('Lista_SAC','SAC','Normal'));
        
    }

    public function getArticuloFavorito()
    {  
        return view('Principal.ArticuloFavorito');         
    }

    public function getMiProgreso($d1,$d2)
    {
        $dtaHome[] = array(
            'Estadistica'       => Estadisticas::getData($d1,$d2),
            'dtaVentasMes'      => Estadisticas::dtaVentasMes($d1,$d2),
            'dtaVentasRutas'    => Estadisticas::dtaVentasRutas($d1,$d2),
        );
        
        return response()->json($dtaHome);
    }
    public function dtaEstadisticas()
    {
        $d1 = date('Y-m-01', strtotime(now()));
        $d2 = date('Y-m-d', strtotime(now()));

        $dtaHome[] = array(
            'Estadistica'   => Estadisticas::getData($d1,$d2),           
        );
        return response()->json($dtaHome);
    }

    public function getData()
    {
        $dtaHome[] = array(
            'Inventario'    => Inventario::getArticulosFavoritos(),
            'Liq6Meses'     => Liquidacion_a_6meses::getArticulos(),
            'Liq12Meses'    => Liquidacion_a_12meses::getArticulos(),
            'Clientes'      => Clientes::getClientes(),
            'Vendedor'      => Vendedor::getVendedor(), 
        );
        
        return response()->json($dtaHome);
    }
    public function getArticulosFavoritos()
    {
        $dtaHome[] = array(
            'ArticulosFav'      => Inventario::getArticulosFavoritos(),
            'Inventario'        => Inventario::getArticulosSinFav(),
        );
        
        return response()->json($dtaHome);
    }
    public function AddFavs(Request $request)
    {
        $response = ArticuloFavoritos::AddFavs($request);
        return response()->json($response);
    }
    public function getDataCliente($idCliente)
    {

        $articulos_favoritos = array();
        $lista_negra = array();

        $Lista_articulos_Favoritos = ArticuloFavoritos::all();
        $getListaNegra = DB::connection('sqlsrv')->select('SELECT * FROM PRODUCCION.dbo.tbl_articulos_lista_negra');

        foreach ($Lista_articulos_Favoritos as $rec){
            $articulos_favoritos[] = $rec->Articulo;
        }

        foreach($getListaNegra as $lista){
            $lista_negra[] = $lista->articulo;
        }

        $dtaCliente[] = array(
            'InfoCliente'                   => Clientes::where('CLIENTE', $idCliente)->get(),
            'PlanCrecimieto'                => Clientes::getPlanCrecimiento($idCliente),
            'Historico3M'                   => ClientesHistorico3M::where('CLIENTE', $idCliente)->get(),
            'ClienteMora'                   => ClientesMora::where('CLIENTE', $idCliente)->get(),
            'ClientesHistoricoFactura'      => ClientesHistoricoFactura::where('COD_CLIENTE', $idCliente)->orderBy('Dia', 'DESC')->get(),            
            'ArticulosNoFacturado'          => Inventario::whereNotIn('ARTICULO', function($q)  use ($idCliente){
                                                    $q->select('ARTICULO')->from('PRODUCCION.dbo.GMV3_hstCompra_3M')->where("CLIENTE", $idCliente);
                                                })->whereIn('ARTICULO',$articulos_favoritos)->whereNotIn('ARTICULO', $lista_negra)->get(),            
        );

        return response()->json($dtaCliente);
    }
    public function getDataArticulo($idArticulo)
    {
        $dtaArticulo[] = array(
            'InfoArticulo'          => Inventario::where('ARTICULO', $idArticulo)->get(),
            'NivelPrecio'           => NivelPrecio::getNivelPrecio($idArticulo),
            'Bodega'                => Bodega::getBodega($idArticulo),
        );

        return response()->json($dtaArticulo);
    }

    public function getLotes($BODEGA,$ARTICULO)
    {
        $LOTES = Lotes::getLotes($BODEGA,$ARTICULO);
        return response()->json($LOTES);
    }
    public function ChancesStatus(Request $request)
    {
        $response = Pedido::ChancesStatus($request);
        return response()->json($response);
    }
    public function getDetallesFactura($Factura)
    {
        $response = Factura::getDetalles($Factura);
        return response()->json($response);
    }
    public function getCommentPedido(Request $request)
    {
        $response = PedidoComentario::getCommentPedido($request);
        return response()->json($response);
    }
    public function AddCommentPedido(Request $request)
    {
        $response = PedidoComentario::AddCommentPedido($request);
        return response()->json($response);
    }
    public function AddPlanCrecimiento(Request $request)
    {
        $response = PlanCrecimiento::AddPlanCrecimiento($request);
        return response()->json($response);
    }
    public function DeleteCommentPedido(Request $request)
    {
        $response = PedidoComentario::DeleteCommentPedido($request);
        return response()->json($response);
    }

    public function getPedidosRangeDates(Request $request)
    {
        $response = Pedido::getPedidos($request);
        return response()->json($response);
    }

    public function getCalendarPromocion()
    {       
        $articulos      = Inventario::getArticulos();
        return view('Principal.CalendarPromocion', compact('articulos'));         
    }

    public function getDataPromocion(){
        $promocion = Promocion::getDataCalendar();
        return response()->json($promocion);
    }

    public function insert_promocion(Request $request){
              
        try{

            $titulo = $request->title;
            $descripcion = $request->description;
            $articulo = explode("!",$request->label);
            $fechaIni = $request->startDate;
            $fechaFin = $request->endDate;
            $name = "item.jpg";
            $activo = $fechaFin >= date('Y-m-d') ? 'S' : 'N';

            if($fechaFin >= $fechaIni){
                $promocion = new Promocion();

                if($request->hasFile('nuevaImagen')){
                    $file = $request->file('nuevaImagen');
                    
                    //Path Kubernetes
                    //$destino = $_SERVER["DOCUMENT_ROOT"]."/images/promocion/";

                    //PATH DOCKER
                    //$destino = $_SERVER["DOCUMENT_ROOT"]."/SAC/public/images/promocion/";

                    $name = time() . '-' . $file->getClientOriginalName();
                    Storage::disk('s3')->put('Promociones/'.$name, file_get_contents($file));
                    //move_uploaded_file($file->getRealPath(),$destino.$name);
                    //copy($file->getRealPath(), $destino.$name);
                }
                
                $promocion->titulo = $titulo;
                $promocion->descripcion = $descripcion;
                $promocion->articulo = $articulo[0];
                $promocion->nombre = $articulo[1];
                $promocion->image = $name;
                $promocion->fechaInicio = $fechaIni;
                $promocion->fechaFinal = $fechaFin;
                $promocion->activo = $activo;
                $promocion->save();

                alert()->success('Se ha registrado la promocion', 'Success');
                return redirect()->back();
            }else{
                alert()->error('La fecha final no puede ser inferior a la fecha de inicio', 'ERROR')->persistent('Close');
                return redirect()->back();
            }
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            alert()->error($mensaje, 'ERROR')->persistent('Close');
            return redirect()->back();
    }   
    }

    public function update_promocion(Request $request){
        try{
            $id = $request->eIdPromocion;
            $titulo = $request->eTitle;
            $descripcion = $request->eDescription;
            $articulo = explode("!",$request->eLabel);
            $fechaIni = $request->eStartDate;
            $fechaFin = $request->eEndDate;
            $name = $request->fotoActual;
            $at = $fechaFin >= date('Y-m-d') ? 'S' : 'N';

            if($fechaFin >= $fechaIni){
                $promocion = new Promocion();

                if($request->hasFile('eNuevaImagen')){
                    $file = $request->file('eNuevaImagen');
                    //Path Kubernetes
                    //$destino = $_SERVER["DOCUMENT_ROOT"]."/images/promocion/";

                    //PATH DOCKER
                    //$destino = $_SERVER["DOCUMENT_ROOT"]."/SAC/public/images/promocion/";
                    
                    $name = time() . '-' . $file->getClientOriginalName();
                    //move_uploaded_file($file->getRealPath(),$destino.$name);
                    Storage::disk('s3')->put('Promociones/'.$name, file_get_contents($file));
                    //copy($file->getRealPath(), $destino.$name);
                }

                $promocion = DB::table('tbl_promocion')->where('id', $id)->update(['titulo'=>$titulo,'descripcion'=>$descripcion,'articulo'=>$articulo[0],'nombre'=>$articulo[1],'image'=>$name,'fechaInicio'=>$fechaIni,'fechaFinal'=>$fechaFin,'activo'=>$at]);
                alert()->success('Se ha modificado la promocion', 'Success');
                return redirect()->back();
            }else{
                alert()->error('La fecha final no puede ser inferior a la fecha de inicio', 'ERROR')->persistent('Close');
                return redirect()->back();
            }
        }catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            alert()->error($mensaje, 'ERROR')->persistent('Close');
            return redirect()->back();
        }

    }

    public function editPromocion($id, $activo){
        $at = $activo == 'S' ? 'N' : 'S';
        $promocion = DB::table('tbl_promocion')->where('id', $id)->update(['activo'=>$at]);
        return response()->json($promocion);
    }
    
    public function getPlanCrecimientoIco(){
        $ico = PlanCrecimiento::get();
        return $ico;

    }

    public function generarPDF(){
        $inventario = Inventario::getArticulosFavoritos();

        view()->share('Principal.invPDF', $inventario);

        $pdf = PDF::loadView('Principal.invPDF', compact('inventario'));

        return $pdf->download('inventario.pdf');
    }

    public function generarExcel() {

		$obj = Inventario::getArticulosFavoritos();

		$objPHPExcel = new PHPExcel();
        $tituloReporte = "Inventario Totalizado";
		
        $titulosColumnas = array('Codigo', 'Descripción', 'Cant. Disponible 002', 'Precio Farmacia');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A1:D1');
		$objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A2:D2');

        $estiloTituloReporte = array(
            'font' => array(
            'name'      => 'Calibri',
            'bold'      => true,
            'italic'    => false,
            'strike'    => false,
            'size'      => 14,
            'color'     => array(
                            'rgb' => '212121')
            ),
            'alignment' =>  array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation'   => 0,
                            'wrap'       => TRUE,
                            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                        'name'  => 'Calibri',
                        'bold'  => true
            ),
            'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'wrap'          => TRUE
                            ),
            'borders' => array(
                            'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(
            array(
                'borders' => array(
                'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                ),
                )
            )
        );

        $right = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        $left = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1',    $tituloReporte)
		->setCellValue('A3',    $titulosColumnas[0])
		->setCellValue('B3',    $titulosColumnas[1])
		->setCellValue('C3',    $titulosColumnas[2])
		->setCellValue('D3',    $titulosColumnas[3]);

		$i=4;
		foreach ($obj as $key) {
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $key['ARTICULO'])
			->setCellValue('B'.$i,  $key['DESCRIPCION'])
			->setCellValue('C'.$i,  $key['total'])
			->setCellValue('D'.$i,  $key['PRECIO_FARMACIA']);
			
			$i++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('INVENTARIO');
		$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->applyFromArray($estiloTituloColumnas);      
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:D".($i-1));

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

		$objPHPExcel->getActiveSheet()->getStyle('C3:D'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Inventario totalizado hasta '.date('d/m/Y').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');


	}
}
