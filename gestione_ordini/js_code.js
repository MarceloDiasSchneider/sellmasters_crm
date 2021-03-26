
  $(document).ready(function() {

    //richiama gli ordini dal form di ricerca
    $('#form_cerca_ordine').submit(function (event) {    
      alert('hi');        
        event.preventDefault(); 
        var datipresidalform = $("#form_cerca_ordine").serialize();
      $.ajax({
        type: "POST",
        url: "model.php",
        data: "action=recupera_ordini&" + datipresidalform,
        dataType: "json",
        async: false,
        success: function(data) {
          // console.log(data['descrizione']);
          //fetch_data();
          //$('#idcorsodamodificare').val(data['id']);
          //$('#orderid').val(data[0]['order_id']);
          $('#griglia').DataTable({
            
            data: data,
            columns: [{
                title: "order_id"
              },
              {
                title: "purchase_date"
              },
              {
                title: "item_price"
              },
              {
                title: "recipient_name"
              },
              {
                title: "amazonasin"
              }

            ]
          });
          //$('#spaziogriglia').load('griglia.php',data, function() { });
         

        },
        error: function(msg) {
          alert("Failed: " + msg.status + ": " + msg.statusText);
        }
      });
    });
    //

  });
