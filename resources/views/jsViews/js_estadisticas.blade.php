<script type="text/javascript">

  var Selectors = {
          MODAL_CALENDAR: '#ShowEventModal',
      };

    function abbrNum(number, decPlaces) {    
        decPlaces = Math.pow(10,decPlaces);
        var abbrev = [ " K", " M", " B", " T" ];
        for (var i=abbrev.length-1; i>=0; i--) {
            var size = Math.pow(10,(i+1)*3);
            if(size <= number) {
                number = Math.round(number*decPlaces/size)/decPlaces;
                if((number == 1000) && (i < abbrev.length - 1)) {
                    number = 1;
                    i++;
                }
                number += abbrev[i];
                break;
            }
        }

        return number;
    }

    function labelFormat(label){
      label = label +"|l";
      var spl = label.split("|");

      return spl[0];
    }
    const startOfMonth = moment().startOf('month').format('YYYY-MM-DD');;
    const endOfMonth   = moment().subtract(0, "days").format("YYYY-MM-DD");

    var dta_aportes_mercados;
    var dta_ventas_mercados ;
    
    RangeStat(startOfMonth,endOfMonth);
    
    var labelRange = startOfMonth + " to " + endOfMonth;
    
    $('#id_range_select').val(labelRange);
    $('#id_range_select').change(function () {
      Fechas = $(this).val().split("to");
      if(Object.keys(Fechas).length >= 2 ){
          RangeStat(Fechas[0],Fechas[1]);
      } 
    });

    

    function RangeStat(D1,D2){

      dta_aportes_mercados = []
      dta_ventas_mercados = {
        dataset: {
          VentasDelMes:   [[],[],[]],
          VentasMesRutas: [[],[],[]],
        }
      };

      var detalleRuta = [];

      $.get( "getMiProgreso/"+D1 + "/"+D2, function( data ) {
        Estadisticas(data[0].Estadistica)
        setBarVentasMes(data[0].dtaVentasMes)
        setBarVentasRutas(data[0].dtaVentasRutas)
        detalleRuta = data[0].dtaVentasRutas
      })

    }    

    function setBarVentasMes(Data){

      $.each(Data, function (i, d) {
        dta_ventas_mercados.dataset['VentasDelMes'][0].push(numeral(d.MONTO).format('00.00'))
        dta_ventas_mercados.dataset['VentasDelMes'][1].push(0)
        dta_ventas_mercados.dataset['VentasDelMes'][2].push(d.DIA + " Dia")
      });
      
      Build_Echart_bar(dta_ventas_mercados)
    
    }

    function setBarVentasRutas(Data){

      $.each(Data, function (i, d) {
        dta_ventas_mercados.dataset['VentasMesRutas'][0].push(numeral(d.MONTO).format('00.00'))
        dta_ventas_mercados.dataset['VentasMesRutas'][1].push(0)
        dta_ventas_mercados.dataset['VentasMesRutas'][2].push(d.VENDEDOR+' | '+d.NOMBRE+' | ' + d.ZONA)

      });

      Build_Echart_bar(dta_ventas_mercados)

    }


function mdlCalendar() {

  var mdlCalendar = document.querySelector(Selectors.MODAL_CALENDAR);
  var showCalendar = new window.bootstrap.Modal(mdlCalendar);
  showCalendar.show();
  
  setTimeout(function() {
    $('#btnToday').trigger('click');
  }, 1000);


}
   
    
    function Estadisticas(Data){

        $Rutas = '';
        
        $("#id_Venta_Meta").text('C$ ' + numeral(Data['Venta_Meta']).format('0,00.00'))
        $("#id_Venta_Real").text('C$ ' + numeral(Data['Venta_Real']).format('0,00.00'))
        $("#id_Venta_Actu").text('C$ ' + numeral(Data['Venta_Actu']).format('0,00.00'))
        $("#id_Venta_Week").text('C$ ' + numeral(Data['Venta_Week']).format('0,00.00'))
        $("#id_Venta_Week_Label").text(Data['Venta_Week_Label'])

        $("#id_Venta_Porc").text('% ' + numeral(Data['Venta_Porc']).format('0,00'))

        $("#id_Dias_Habiles").text(numeral(Data['Dias_Habiles']).format('0,00'))
        $("#id_Dias_Facturados").text(numeral(Data['Dias_Facturados']).format('0,00'))
        $("#id_Dias_porcent").text(Data['Dias_porcent'])

        $("#id_Cliente_Meta").text(Data['Cliente_Meta'])
        $("#id_Cliente_Real").text(Data['Cliente_Real'])
        $("#id_Cliente_Porc").text('% ' + Data['Cliente_Porc'])

        $.each(Data, function (i, d) {

            if(d.VENDEDOR !== undefined){

                $Rutas += `
                    <div class="mb-4 col-md-6 col-lg-4">
                      <div class="card mb-3">
                        <div class="card-header bg-light">
                          <div class="row justify-content-between">
                            <div class="col">
                              <div class="d-flex">
                                
                              <div class="avatar avatar-2xl status-online">
                                  <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                                </div>
                                
                                <div class="flex-1 align-self-center ms-2">
                                  <p class="mb-1 lh-1">`+ d.VENDEDOR + ' | ' + d.NOMBRE+`</p>
                                  <p class="mb-0 fs--1">`+ d.SKU + ` SKUs &bull; Tendencia: `+ d.TENDENCIA + ` &bull; Optimo. `+ Data['Dias_porcent'] + ` </p>
                                  <P class="mb-0 fs--1">`+d.ZONA+` </p>
                                </div>
                                <div class="avatar avatar-2xl">
                                    <div class="d-inline-block"  ><span class="far fa-calendar-alt fs-5" onclick="mdlCalendar()"  data-fa-transform="down-2"></span></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body ">

                              <div class="col-md-12 col-xxl-12">
                                <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">                    
                                    <div class="col-auto"> 
                                      <div class="d-flex align-items-center mb-3">
                                        <h6 class="text-primary mb-0">Ventas </h6>
                                        <span class="badge rounded-pill ms-3 badge-soft-primary"> `+ d.RUTA_CUMPLI + `</span>
                                      </div>
                                      <div class="row g-sm-4">                                
                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.META_RUTA + `</h6>
                                                <p class="mb-0 fs--2 text-500">Meta</p>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-0">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.MesActual + `</h6>
                                                <p class="mb-0 fs--2 text-500">Real</p>
                                            </div>
                                          </div>
                                        </div>

                                      </div>
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-12 col-xxl-12 mt-3">
                                <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">                    
                                    <div class="col-auto"> 
                                      <div class="d-flex align-items-center mb-3">
                                        <h6 class="text-primary mb-0">Cobertura de Clientes </h6>
                                        <span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-caret-up"></span> `+ d.CLIENTE_COBERTURA + ` </span>
                                      </div>
                                      <div class="row g-sm-4">                                
                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-user-friends text-primary"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.CLIENTE + ` Clientes</h6>
                                                <p class="mb-0 fs--2 text-500">Cubiertos</p>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-0">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-user-friends text-primary"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.META_CLIENTE + ` Clientes</h6>
                                                <p class="mb-0 fs--2 text-500">de meta</p>
                                            </div>
                                          </div>
                                        </div>

                                      </div>
                                    </div>
                                </div>
                              </div>

                              <div class="col-md-12 col-xxl-12 mt-3">
                                <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">                    
                                    <div class="col-auto"> 
                                      <div class="row g-sm-4">                                
                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.DiaActual + `</h6>
                                                <p class="mb-0 fs--2 text-500">Dia Actual </p>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col-12 col-sm-auto">
                                          <div class="d-flex align-items-center pe-0">
                                            <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                              <div class="flex-1">
                                                <h6 class="text-800 mb-0"id="" >`+ d.SaleWeek + `</h6>
                                                <p class="mb-0 fs--2 text-500">Esta semana</p>
                                            </div>
                                          </div>
                                        </div>

                                      </div>
                                    </div>
                                </div>
                              </div>
                        </div>
                        <div class="card-footer bg-light ">
                          <div class="row ">
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">SAC</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">`+ d.SAC + `</h5>
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">EJEC</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">`+ d.EJEC + `</h5>
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-0">
                                <h6 class="fs--2 text-600 mb-1">DS</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">`+ d.DS + `</h5>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>                       
                    </div>
            </div>`

            }
        

        });

        $("#id_rutas").html($Rutas);

    }    
    
    function Build_Echart_bar(data) {

      

      var tooltipFormatter = function tooltipFormatter(params) {
          return `<div class="card">
                    <div class="card-header bg-light py-2">
                      <h6 class="text-600 mb-0">`+params[0].axisValue+ `  </h6>
                    </div>
                    <div class="card-body py-2">
                      <h6 class="text-600 mb-0 fw-normal">
                        <span class="fas fa-circle text-primary me-2"></span>C$ 
                        <span class="fw-medium">`+ numeral(params[0].data).format('0,00.00')+` </span>
                      </h6>
                      </div>
                  </div>`;
      };

      


      var getOptionSales = function getOptionSales(data1, data2, data3) {
          return function () {
              return {
                  color: utils.getGrays().white,
                  tooltip: {
                  trigger: 'axis',
                  padding: 0,
                  backgroundColor: 'transparent',
                  borderWidth: 0,
                  transitionDuration: 0,
                  position: function position(pos, params, dom, rect, size) {
                      return getPosition(pos, params, dom, rect, size);
                  },
                  
                  axisPointer: {
                      type: 'none'
                  },
                  formatter: tooltipFormatter
                  },
                  xAxis: {
                      type: 'category',
                      data: data3,
                      axisLabel: {
                          color: utils.getGrays()['600'],
                          formatter: function(d){
                            return labelFormat(d);
                          }                         
                      },
                  axisLine: {
                      lineStyle: {
                      color: utils.getGrays()['300'],
                      type: 'dashed'
                      }
                  },
                  axisTick:false,
                  boundaryGap: true
                  },
                  yAxis: {
                  position: 'right',
                  axisPointer: {
                      type: 'none'
                  },
                  axisTick: 'none',
                  splitLine: {
                      show: false
                  },
                  axisLine: {
                      show: false
                  },
                  axisLabel: {
                      show: false
                  }
                  },
                  
                  series: [{
                      type: 'bar',
                      name: 'Revenue',
                      data: data1,
                      lineStyle: {
                          color: utils.getColor('primary')
                      },
                      label: {
                          show: true,
                          position: 'top',
                          formatter: function(d) {
                            return "C$ " + abbrNum(d.data,2);
                          }
                      },
                      
                      itemStyle: {
                          barBorderRadius: [4, 4, 0, 0],
                          color: utils.getColor('primary'),
                          borderColor: utils.getGrays()['300'],
                          borderWidth: 1
                      },
                      emphasis: {
                          itemStyle: {
                          color: utils.getColor('primary')
                          }
                      }
                  }, {
                  type: 'line',
                  name: 'Optimo',
                  data: data2,
                  symbol: 'circle',
                  symbolSize: 6,
                  animation: false,
                  itemStyle: {
                      color: utils.getColor('warning')
                  },
                  
                  lineStyle: {
                      type: 'dashed',
                      width: 2,
                      color: utils.getColor('warning')
                  }
                  }],
                  grid: {
                  right: 5,
                  left: 5,
                  bottom: '8%',
                  top: '5%'
                  }
              };
          };
      };

      var initChart = function initChart(el, options) {
          var userOptions = utils.getData(el, 'options');
          var chart = window.echarts.init(el);
          echartSetOption(chart, userOptions, options);
      };

      var chartKeys = ['VentasDelMes','VentasMesRutas'];
      chartKeys.forEach(function (key) {
          var el = document.querySelector(".echart-sale-".concat(key));
          el && initChart(el, getOptionSales(
            data.dataset[key][0], 
            data.dataset[key][1], 
            data.dataset[key][2]
          ));
      });
    };
    
</script>
