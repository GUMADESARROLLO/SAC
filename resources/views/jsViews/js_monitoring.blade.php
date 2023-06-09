<script type="text/javascript">
   
    const startOfMonth = moment().startOf('month').format('YYYY-MM-DD');;
    const endOfMonth   = moment().subtract(0, "days").format("YYYY-MM-DD");

    
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

      $strRow = ''

      $.get( "getmonitoring/"+D1 + "/"+D2, function( data ) {

        $.each(data, function (i, d) {
          $strRow +=`<tr class="border-bottom border-200">
                          <td>
                            <div class="d-flex align-items-center position-relative">
                              <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="images/user/avatar-4.jpg" alt="" />

                              </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-0 fw-semi-bold">`+ d.Nombre +`</h6>
                                <p class="text-500 fs--2 mb-0">`+ d.RUTA + ' | ' + d.zona+`</p>
                              </div>
                            </div>
                          </td>
                          <td class="align-middle text-center"><h5 class="text-800 mb-0">`+ d.Comisiones +`</h5>
                          <td class="align-middle text-center"><h5 class="text-800 mb-0">`+ d.PlanCrecimiento +`</h5>
                          <td class="align-middle text-center"><h5 class="text-800 mb-0">`+ d.Pedido +`</h5>
                          
                          <td class="align-middle text-center">
                              <div class="col-auto"><h5 class="text-800 mb-0">`+ d.tTotal +`</h5></div>
                          
                            
                          </td>
                        </tr>`
        
        });
        
        

        $("#id_filas").html($strRow);
        
      })

     

    }    
   
</script>
