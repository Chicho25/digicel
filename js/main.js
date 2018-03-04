function getOptionsData(el, type, id) {
        if (el.value === "") {
            $(el).siblings("input[name=model]").val("");
        } else {
            $.ajax({
                url: 'include/getData.php',
                type: 'POST',
                dataType: 'html',
                data: "reqtype="+type+"&id="+el, //get model dan ukuran
                success: function (data) {

                    if(type == "pricebyproductterritory")
                    {
                       $('#'+id).val(data);
                    }
                    else
                    {
                        $('#'+id).html(data);
                        //$('#'+id).chosen();
                        $('#'+id).val('').trigger("chosen:updated");
                    }
                    //$(el).closest('.barang_in').find("input[name='model']").val(data.nama_model + " " + "(" + data.ukuran + ")");//get the parent element and then find the input
                },
                error: function (e) {
                    //called when there is an error
                    console.log(e.message);
                }
            });
        }
    }

function showOppForm(type)
{
    if(type == 1)
    {
        $('#more').hide();
        $('#less').show();
        $('.opportunityname').show();
    }
    else
    {
        $('#more').show();
        $('#less').hide();
        $('.opportunityname').hide();
    }
}

function validatProduct()
{
    var category = $('#category').val();
    var product = $('#product option:selected').text();
    var productid = $('#product').val();
    var orgprice = $('#orgprice').val();
    var saleprice = $('#saleprice').val();
    var qty = $('#qty').val();
    var description = $('#description').val();

    if(category!= "" && product!= "" && orgprice!= "" && saleprice!= "" && qty!= "" && description!="")
    {
        $("#frmProduct").submit();
    }
    else
    {
        alert("Please fill all fields");
    }
}
function fillProduct(oppid)
{
    alert(oppid);
    var category = $('#category').val();
    var product = $('#product option:selected').text();
    var productid = $('#product').val();
    var orgprice = $('#orgprice').val();
    var saleprice = $('#saleprice').val();
    var qty = $('#qty').val();
    var description = $('#description').val();
    var prodtval = $("#prodTVAL").val();
    var data = category+"::::"+productid+"::::"+orgprice+"::::"+saleprice+"::::"+qty+"::::"+description;
    var rowData = "<input type='hidden' name='h1[]' value='"+data+"'>";
    var btnhtml = "<td id='prod"+productid+"'><a onclick=\"editRM('"+data+"')\" href='modal-opportunitydetail.php' data-toggle='ajaxModal'><i class='glyphicon glyphicon-pencil'></i></a>&nbsp;&nbsp;<i onclick='rm()' class='glyphicon glyphicon-remove'></i></td>";


    if(prodtval > 0)
        $("#prod"+prodtval).parent().replaceWith("<tr>"+rowData+"<td>"+product+"</td><td>"+orgprice+"</td><td>"+saleprice+"</td><td>"+qty+"</td>"+btnhtml+"</tr>");
    else
        $(".tableproduct"+oppid).append("<tr>"+rowData+"<td>"+product+"</td><td>"+orgprice+"</td><td>"+saleprice+"</td><td>"+qty+"</td>"+btnhtml+"</tr>");

    var totalOrgPrice = 0;
    var totalSalePrice = 0;
    var totalQty = 0;
    $('input[name^="h1"]').each( function() {
        var spltVal = this.value.split("::::");
        totalOrgPrice += parseInt(spltVal[2]);
        totalSalePrice += parseInt(spltVal[3]);
        totalQty += parseInt(spltVal[4]);
    });
    $("#orgtotal"+oppid).html(totalOrgPrice);
    $("#saletotal"+oppid).html(totalSalePrice);
    $("#qtytotal"+oppid).html(totalQty);
    $("#myModal .close").click();
}

function editRM(data1)
{


    setTimeout(function() {

        var spltdata = data1.split("::::");
        $.ajax({
            url: 'include/getData.php',
            type: 'POST',
            dataType: 'html',
            data: "reqtype=productbycategory&id="+spltdata[0], //get model dan ukuran
            success: function (data) {

                $('#product').html(data);
                $('#category').val(spltdata[0]);
                $('#product').val(spltdata[1]);
                $('#orgprice').val(spltdata[2]);
                $('#saleprice').val(spltdata[3]);
                $('#qty').val(spltdata[4]);
                $('#description').val(spltdata[5]);
                $('#prodTVAL').val(spltdata[1]);
            },
            error: function (e) {
                //called when there is an error
                console.log(e.message);
            }
        });

    }, 100);

}
function rm() {

  $(event.target).closest("tr").remove();
  var totalOrgPrice = 0;
    var totalSalePrice = 0;
    var totalQty = 0;
    $('input[name^="h1"]').each( function() {
        var spltVal = this.value.split("::::");
        totalOrgPrice += parseInt(spltVal[2]);
        totalSalePrice += parseInt(spltVal[3]);
        totalQty += parseInt(spltVal[4]);
    });
    $("#orgtotal").html(totalOrgPrice);
    $("#saletotal").html(totalSalePrice);
    $("#qtytotal").html(totalQty);
}

$("#myModal").on("shown.bs.modal",function(){
   //will be executed everytime #item_modal is shown
   $(this).hide().show(); //hide first and then show here
});

$('.datepicker').datepicker({
        format:"yyyy-mm-dd",
        autoclose: true

      })
      .on('changeDate', function(ev){
          $('.datepicker').datepicker('hide');
      });

      /*$('#datepickermast').datepicker({
              "setDate": new Date(),
              "autoclose": true
      });*/
